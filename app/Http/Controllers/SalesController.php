<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Sale;
use App\Models\FinishedGoodStock;
use App\Models\CcPlant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesExport;
use PDF;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $this->getSalesQuery($request);
        $sales = $query->latest()->get();
        $customers = Customer::select('id', 'name')->get(); // For dropdown filter

        return view('sales.index', compact('sales', 'customers'));
    }

    public function exportExcel(Request $request)
    {
        $query = $this->getSalesQuery($request);
        $sales = $query->latest()->get();

        return Excel::download(new SalesExport($sales), 'sales.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $query = $this->getSalesQuery($request);
        $sales = $query->latest()->get();

        $pdf = PDF::loadView('sales.pdf', compact('sales'));
        return $pdf->download('sales.pdf');
    }

    public function print(Request $request)
    {
        $query = $this->getSalesQuery($request);
        $sales = $query->latest()->get();

        return view('sales.print', compact('sales'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $stocks = FinishedGoodStock::join('cc_plant', 'finished_good_stocks.cc_plant_id', '=', 'cc_plant.id')
            ->select(
                'finished_good_stocks.item_name',
                'cc_plant.billet_size_inch',
                DB::raw('SUM(finished_good_stocks.weight) as total_weight')
            )
            ->groupBy('finished_good_stocks.item_name', 'cc_plant.billet_size_inch')->get();
        $customers = Customer::select('id', 'name')->get();
        $ccPlants = CcPlant::select('id', 'billet_size_inch')->get();
        return view('sales.create', compact('stocks', 'customers', 'ccPlants'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sale_date' => 'required|date',
            'cc_plant_id' => 'required|exists:cc_plant,id',
            'stock_id' => 'required|string',
            'customer_id' => 'nullable|exists:customers,id',
            'weight' => 'required|numeric|min:0.01',
            'rate' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            list($itemName, $billetSize) = explode('_', $request->stock_id);

            $availableStock = FinishedGoodStock::join('cc_plant', 'finished_good_stocks.cc_plant_id', '=', 'cc_plant.id')
                ->where('finished_good_stocks.item_name', $itemName)
                ->where('cc_plant.billet_size_inch', $billetSize)
                ->sum('finished_good_stocks.weight');

            if ($availableStock < $request->weight) {
                throw new \Exception(__('messages.not_enough_stock_available'));
            }

            $sub_total = $request->weight * $request->rate;
            $remaining_amount = $sub_total - $request->discount - $request->paid_amount;

            $sale = Sale::create([
                'sale_date' => $request->sale_date,
                'cc_plant_id' => $request->cc_plant_id,
                'item_name' => $itemName,
                'billet_size' => $billetSize,
                'customer_id' => $request->customer_id,
                'quantity' => 1, // Or calculate based on weight if needed
                'total_weight' => $request->weight,
                'rate' => $request->rate,
                'discount' => $request->discount,
                'sub_total' => $sub_total,
                'paid_amount' => $request->paid_amount,
                'remaining_amount' => $remaining_amount,
            ]);

            if ($request->paid_amount > 0 && $request->customer_id) {
                \App\Models\Payment::create([
                    'customer_id' => $request->customer_id,
                    'sale_id' => $sale->id,
                    'amount' => $request->paid_amount,
                    'payment_date' => $request->sale_date,
                    'description' => 'Payment for Sale #' . $sale->id,
                ]);
            }

            // Deduct from stock
            $weightToDeduct = $request->weight;
            $stocksToUpdate = FinishedGoodStock::join('cc_plant', 'finished_good_stocks.cc_plant_id', '=', 'cc_plant.id')
                ->where('finished_good_stocks.item_name', $itemName)
                ->where('cc_plant.billet_size_inch', $billetSize)
                ->select('finished_good_stocks.*')
                ->orderBy('finished_good_stocks.created_at', 'asc')
                ->get();

            foreach ($stocksToUpdate as $stock) {
                if ($weightToDeduct <= 0) {
                    break;
                }

                if ($stock->weight >= $weightToDeduct) {
                    $stock->decrement('weight', $weightToDeduct);
                    $weightToDeduct = 0;
                } else {
                    $weightToDeduct -= $stock->weight;
                    $stock->delete();
                }
            }
        });

        return redirect()->route('sales.index')->with('success', __('messages.sale_created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        $customers = Customer::select('id', 'name')->get();
        $ccPlants = CcPlant::select('id', 'billet_size_inch')->get();
        return view('sales.edit', compact('sale', 'customers', 'ccPlants'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        $request->validate([
            'sale_date' => 'required|date',
            'cc_plant_id' => 'required|exists:cc_plant,id',
            'customer_id' => 'nullable|exists:customers,id',
            'weight' => 'required|numeric|min:0.01',
            'rate' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $sale) {
            // Add back the old weight to stock
            $weightToAdd = $sale->total_weight;
            FinishedGoodStock::create([
                'cc_plant_id' => $sale->cc_plant_id,
                'item_name' => $sale->item_name,
                'weight' => $weightToAdd,
            ]);

            // Deduct the new weight from stock
            $availableStock = FinishedGoodStock::where('item_name', $sale->item_name)
                ->where('cc_plant_id', $request->cc_plant_id)
                ->sum('weight');

            if ($availableStock < $request->weight) {
                throw new \Exception(__('messages.not_enough_stock_available'));
            }

            $sub_total = $request->weight * $request->rate;
            $remaining_amount = $sub_total - $request->discount - $request->paid_amount;

            $oldPaidAmount = $sale->paid_amount;

            $sale->update([
                'sale_date' => $request->sale_date,
                'cc_plant_id' => $request->cc_plant_id,
                'customer_id' => $request->customer_id,
                'total_weight' => $request->weight,
                'rate' => $request->rate,
                'discount' => $request->discount,
                'sub_total' => $sub_total,
                'paid_amount' => $request->paid_amount,
                'remaining_amount' => $remaining_amount,
            ]);

            // If paid amount increased, create a payment record
            if ($request->paid_amount > $oldPaidAmount && $request->customer_id) {
                $newPaymentAmount = $request->paid_amount - $oldPaidAmount;
                \App\Models\Payment::create([
                    'customer_id' => $request->customer_id,
                    'sale_id' => $sale->id,
                    'amount' => $newPaymentAmount,
                    'payment_date' => $request->sale_date,
                    'description' => 'Payment for Sale #' . $sale->id . ' (Update)',
                ]);
            }

            $weightToDeduct = $request->weight;
            $stocksToUpdate = FinishedGoodStock::where('item_name', $sale->item_name)
                ->where('cc_plant_id', $request->cc_plant_id)
                ->orderBy('created_at', 'asc')
                ->get();

            foreach ($stocksToUpdate as $stock) {
                if ($weightToDeduct <= 0) {
                    break;
                }

                if ($stock->weight >= $weightToDeduct) {
                    $stock->decrement('weight', $weightToDeduct);
                    $weightToDeduct = 0;
                } else {
                    $weightToDeduct -= $stock->weight;
                    $stock->delete();
                }
            }
        });

        return redirect()->route('sales.index')->with('success', __('messages.sale_updated_successfully'));
    }

// sales report
    public function salesReport(Request $request)
    {
        $query = $this->getSalesQuery($request, 'sale_date');
        $sales = $query->latest()->paginate(10);

        $totalAmount = $sales->sum('sub_total');
        $totalWeight = $sales->sum('total_weight');
        $uniqueItemCount = $sales->unique('item_name')->count();

        return view('sales.report', compact('sales', 'totalAmount', 'totalWeight', 'uniqueItemCount'));
    }

  private function getSalesQuery(Request $request, $dateColumn = 'created_at')
{
    $query = Sale::with(['customer']);

    // 🔍 Search filter
    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where(function ($q) use ($search) {
            $q->where('item_name', 'like', "%{$search}%")
                ->orWhere('billet_size', 'like', "%{$search}%")
                ->orWhereHas('customer', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
        });
    }

    // 📅 Predefined filters
    if ($request->filled('filter')) {
        $filter = $request->input('filter');

        if ($filter === 'daily') {
            $query->whereDate($dateColumn, today());
        } elseif ($filter === 'weekly') {
            $query->whereBetween($dateColumn, [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($filter === 'monthly') {
            $query->whereYear($dateColumn, now()->year)   // ✅ month ke sath year bhi check karna zaroori hai
                  ->whereMonth($dateColumn, now()->month);
        }
    }

    // ⏳ Custom date range
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $query->whereBetween($dateColumn, [
            $request->input('start_date'),
            $request->input('end_date')
        ]);
    }

    return $query;
}

    }

