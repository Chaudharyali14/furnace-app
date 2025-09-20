<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\ScrapPurchase;
use App\Models\Supplier;
use App\Models\RawMaterialStock;
use Illuminate\Http\Request;
use App\Exports\PurchasesExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PurchaseController extends Controller
{
    public function index()
    {
        $scrap_purchases = ScrapPurchase::with('supplier')->paginate(10);
        return view('purchase.index', compact('scrap_purchases'));
    }

    private function _calculateScrapValues(Request $request): array
    {
        $weight = $request->input('weight');
        $amount_per_kg = $request->input('amount_per_kg');
        $waste_percentage = $request->input('waste_percentage');
        $discount = $request->input('discount', 0);
        $paid_amount = $request->input('paid_amount', 0);

        $waste_in_kg = $weight * ($waste_percentage / 100);
        $net_weight = $weight - $waste_in_kg;
        $total_amount = $weight * $amount_per_kg;
        $waste_amount = $waste_in_kg * $amount_per_kg;

        // TODO: Review this business logic. Grand total is currently total amount PLUS waste amount.
        $grand_total = $total_amount + $waste_amount;
        $remaining_amount = $total_amount - $paid_amount - $discount;

        return [
            'weight' => $weight,
            'amount_per_kg' => $amount_per_kg,
            'waste_percentage' => $waste_percentage,
            'discount' => $discount,
            'paid_amount' => $paid_amount,
            'waste_in_kg' => $waste_in_kg,
            'net_weight' => $net_weight,
            'total_amount' => $total_amount,
            'waste_amount' => $waste_amount,
            'grand_total' => $grand_total,
            'remaining_amount' => $remaining_amount,
        ];
    }

    public function add_scrap(Request $request)
    {
        $request->validate([
            'supplier_name' => 'required',
            'scrap_name' => 'required',
            'weight' => 'required|numeric',
            'amount_per_kg' => 'required|numeric',
            'waste_percentage' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'paid_amount' => 'nullable|numeric',
            'purchase_date' => 'required|date',
        ]);

        $supplier = Supplier::firstOrCreate(['name' => $request->input('supplier_name')]);

        $calculated = $this->_calculateScrapValues($request);

        ScrapPurchase::create([
            'supplier_id' => $supplier->id,
            'scrap_name' => $request->input('scrap_name'),
            'weight' => $calculated['weight'],
            'amount_per_kg' => $calculated['amount_per_kg'],
            'waste_percentage' => $calculated['waste_percentage'],
            'waste_in_kg' => $calculated['waste_in_kg'],
            'weight_without_waste' => $calculated['net_weight'],
            'total_amount' => $calculated['total_amount'],
            'waste_amount' => $calculated['waste_amount'],
            'grand_total' => $calculated['grand_total'],
            'discount' => $calculated['discount'],
            'paid_amount' => $calculated['paid_amount'],
            'remaining_amount' => $calculated['remaining_amount'],
            'purchase_date' => $request->input('purchase_date'),
        ]);

        $stock = RawMaterialStock::firstOrNew(['raw_material_name' => $request->input('scrap_name')]);
        $stock->total_purchased_qty += $calculated['net_weight'];
        $stock->remaining_stock_qty += $calculated['net_weight'];
        $stock->save();

        if ($calculated['paid_amount'] > 0) {
            Payment::create([
                'supplier_id' => $supplier->id,
                'amount' => $calculated['paid_amount'],
                'payment_date' => $request->input('purchase_date'),
            ]);
        }

        return redirect()->route('purchase.index')->with('success', __('messages.scrap_purchase_added_successfully'));
    }

    public function scrap_purchase_list(Request $request)
    {
        $scrap_purchases = $this->getFilteredPurchases($request);
        return view('purchase.scrap_purchase_list', compact('scrap_purchases'));
    }

    public function edit_scrap(ScrapPurchase $purchase)
    {
        return view('purchase.edit_scrap', compact('purchase'));
    }

    public function update_scrap(Request $request, ScrapPurchase $purchase)
    {
        $request->validate([
            'supplier_name' => 'required',
            'scrap_name' => 'required',
            'weight' => 'required|numeric',
            'amount_per_kg' => 'required|numeric',
            'waste_percentage' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'paid_amount' => 'nullable|numeric',
            'purchase_date' => 'required|date',
        ]);

        $supplier = Supplier::firstOrCreate(['name' => $request->input('supplier_name')]);

        $calculated = $this->_calculateScrapValues($request);

        $purchase->update([
            'supplier_id' => $supplier->id,
            'scrap_name' => $request->input('scrap_name'),
            'weight' => $calculated['weight'],
            'amount_per_kg' => $calculated['amount_per_kg'],
            'waste_percentage' => $calculated['waste_percentage'],
            'waste_in_kg' => $calculated['waste_in_kg'],
            'weight_without_waste' => $calculated['net_weight'],
            'total_amount' => $calculated['total_amount'],
            'waste_amount' => $calculated['waste_amount'],
            'grand_total' => $calculated['grand_total'],
            'discount' => $calculated['discount'],
            'paid_amount' => $calculated['paid_amount'],
            'remaining_amount' => $calculated['remaining_amount'],
            'purchase_date' => $request->input('purchase_date'),
        ]);

        return redirect()->route('purchase.scrap_purchase_list')->with('success', __('messages.scrap_purchase_updated_successfully'));
    }

    public function delete_scrap(ScrapPurchase $purchase)
    {
        $purchase->delete();

        return redirect()->route('purchase.scrap_purchase_list')->with('success', __('messages.scrap_purchase_deleted_successfully'));
    }

    public function average_cost_dashboard()
    {
        $item_names = ScrapPurchase::distinct()->pluck('scrap_name');
        return view('purchase.average_cost_dashboard', compact('item_names'));
    }

    public function get_average_cost(Request $request)
    {
        $type = $request->input('type');
        $item_names = $request->input('item_names');
        $result = [];

        switch ($type) {
            case 'single':
                if (!empty($item_names[0])) {
                    $avg_cost = ScrapPurchase::where('scrap_name', $item_names[0])->avg('amount_per_kg');
                    if ($avg_cost !== null) {
                        $result[$item_names[0]] = $avg_cost;
                    }
                }
                break;
            case 'multiple':
                foreach ($item_names as $item_name) {
                    if (!empty($item_name)) {
                        $avg_cost = ScrapPurchase::where('scrap_name', $item_name)->avg('amount_per_kg');
                        if ($avg_cost !== null) {
                            $result[$item_name] = $avg_cost;
                        }
                    }
                }
                break;
            case 'all':
                $average_costs = ScrapPurchase::select('scrap_name', \DB::raw('avg(amount_per_kg) as average_cost'))
                    ->groupBy('scrap_name')
                    ->get();
                foreach ($average_costs as $cost) {
                    $result[$cost->scrap_name] = $cost->average_cost;
                }
                break;
        }

        if (!empty($result)) {
            return response()->json(['status' => 'success', 'data' => $result]);
        } else {
            return response()->json(['status' => 'error', 'message' => __('messages.no_data_found_or_invalid_request')]);
        }
    }

    public function exportExcel(Request $request)
    {
        $scrap_purchases = $this->getFilteredPurchases($request);
        return Excel::download(new PurchasesExport($scrap_purchases), 'purchases.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $scrap_purchases = $this->getFilteredPurchases($request);
        $pdf = Pdf::loadView('purchase.pdf', compact('scrap_purchases'));
        return $pdf->download('purchases.pdf');
    }

    private function getFilteredPurchases(Request $request)
    {
        $query = ScrapPurchase::with('supplier');

        if ($request->filled('filter')) {
            $filter = $request->input('filter');

            if ($filter === 'daily') {
                $query->whereDate('purchase_date', today());
            } elseif ($filter === 'weekly') {
                $query->whereBetween('purchase_date', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($filter === 'monthly') {
                $query->whereYear('purchase_date', now()->year)
                      ->whereMonth('purchase_date', now()->month);
            }
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('purchase_date', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('scrap_name', 'like', "%$search%")
                  ->orWhereHas('supplier', function ($q) use ($search) {
                      $q->where('name', 'like', "%$search%");
                  });
            });
        }

        return $query->orderBy('purchase_date', 'desc')->paginate(10);
    }

    public function showSupplierLedger(Request $request)
    {
        $suppliers = Supplier::all();
        $selectedSupplier = null;
        $transactions = collect();
        $totalPurchaseAmount = 0;
        $totalPaidAmount = 0;
        $totalRemaining = 0;
        $balance = 0;

        if ($request->filled('supplier_id')) {
            $selectedSupplier = Supplier::findOrFail($request->supplier_id);

            if ($selectedSupplier->opening_balance > 0) {
                if ($selectedSupplier->opening_balance_type == 'debit') {
                    $balance += $selectedSupplier->opening_balance;
                    $totalPurchaseAmount += $selectedSupplier->opening_balance;
                } else {
                    $balance -= $selectedSupplier->opening_balance;
                    $totalPaidAmount += $selectedSupplier->opening_balance;
                }

                $transactions->push([
                    'date' => $selectedSupplier->created_at,
                    'description' => __('messages.opening_balance'),
                    'debit' => $selectedSupplier->opening_balance_type == 'debit' ? $selectedSupplier->opening_balance : 0,
                    'credit' => $selectedSupplier->opening_balance_type == 'credit' ? $selectedSupplier->opening_balance : 0,
                    'balance' => $balance,
                ]);
            }

            $purchaseQuery = ScrapPurchase::where('supplier_id', $selectedSupplier->id);
            $paymentQuery = Payment::where('supplier_id', $selectedSupplier->id);

            if ($request->filled('filter')) {
                switch ($request->filter) {
                    case 'daily':
                        $purchaseQuery->whereDate('purchase_date', today());
                        $paymentQuery->whereDate('payment_date', today());
                        break;
                    case 'weekly':
                        $purchaseQuery->whereBetween('purchase_date', [now()->startOfWeek(), now()->endOfWeek()]);
                        $paymentQuery->whereBetween('payment_date', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'monthly':
                        $purchaseQuery->whereYear('purchase_date', now()->year)
                                      ->whereMonth('purchase_date', now()->month);
                        $paymentQuery->whereYear('payment_date', now()->year)
                                     ->whereMonth('payment_date', now()->month);
                        break;
                }
            } elseif ($request->filled('from_date') && $request->filled('to_date')) {
                $purchaseQuery->whereBetween('purchase_date', [$request->from_date, $request->to_date]);
                $paymentQuery->whereBetween('payment_date', [$request->from_date, $request->to_date]);
            }

            if ($request->filled('search')) {
                $searchTerm = '%' . $request->search . '%';
                $purchaseQuery->where(function ($q) use ($searchTerm) {
                    $q->where('scrap_name', 'like', $searchTerm)
                      ->orWhere('total_amount', 'like', $searchTerm);
                });
            }

            $purchases = $purchaseQuery->get();
            $payments = $paymentQuery->get();

            foreach ($purchases as $purchase) {
                $transactions->push([
                    'date' => $purchase->purchase_date,
                    'description' => __('messages.purchase') . ': ' . $purchase->scrap_name,
                    'debit' => $purchase->total_amount,
                    'credit' => 0,
                ]);

                if ($purchase->discount > 0) {
                    $transactions->push([
                        'date' => $purchase->purchase_date,
                        'description' => __('messages.discount'),
                        'debit' => 0,
                        'credit' => $purchase->discount,
                    ]);
                }
            }

            foreach ($payments as $payment) {
                $transactions->push([
                    'date' => $payment->payment_date,
                    'description' => $payment->description ?? __('messages.payment'),
                    'debit' => 0,
                    'credit' => $payment->amount,
                ]);
            }

            $transactions = $transactions->sortBy('date');

            $transactions = $transactions->map(function ($transaction) use (&$balance) {
                if ($transaction['description'] != __('messages.opening_balance')) {
                    $balance += $transaction['debit'] - $transaction['credit'];
                    $transaction['balance'] = $balance;
                }
                return $transaction;
            });

            $totalPurchaseAmount += $purchases->sum('total_amount');
            $totalPaidAmount += $payments->sum('amount') + $purchases->sum('discount');
            $totalRemaining = $totalPurchaseAmount - $totalPaidAmount;
        }

        return view('purchase.supplier_ledger', compact(
            'suppliers',
            'selectedSupplier',
            'transactions',
            'totalPurchaseAmount',
            'totalPaidAmount',
            'totalRemaining'
        ));
    }

    public function addSupplierPayment(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $supplier_id = $request->supplier_id;
        $payment_amount = $request->payment_amount;
        $description = $request->description;

        Payment::create([
            'supplier_id' => $supplier_id,
            'amount' => $payment_amount,
            'payment_date' => $request->payment_date,
            'description' => $description,
        ]);

        $purchases_to_update = ScrapPurchase::where('supplier_id', $supplier_id)
            ->where('remaining_amount', '>', 0)
            ->orderBy('purchase_date', 'asc')
            ->get();

        foreach ($purchases_to_update as $purchase) {
            if ($payment_amount <= 0) {
                break;
            }

            $due = $purchase->remaining_amount;
            $paid = min($payment_amount, $due);

            $purchase->paid_amount += $paid;
            $purchase->remaining_amount -= $paid;
            $purchase->save();

            $payment_amount -= $paid;
        }

        return redirect()->route('supplier.ledger', ['supplier_id' => $request->supplier_id])
                         ->with('success', __('messages.payment_added_successfully'));
    }

    public function purchaseReport(Request $request)
    {
        Log::info('Purchase report requested with filter: ' . $request->filter);
        Log::info('Start date: ' . $request->start_date);
        Log::info('End date: ' . $request->end_date);

        $query = ScrapPurchase::with('supplier');

        if ($request->filled('filter')) {
            $filter = $request->filter;

            if ($filter === 'daily') {
                $query->whereDate('purchase_date', today());
            } elseif ($filter === 'weekly') {
                $startOfWeek = now()->startOfWeek()->toDateString();
                $endOfWeek = now()->endOfWeek()->toDateString();
                Log::info('Weekly filter: ' . $startOfWeek . ' to ' . $endOfWeek);
                $query->whereDate('purchase_date', '>=', $startOfWeek)
                      ->whereDate('purchase_date', '<=', $endOfWeek);
            } elseif ($filter === 'monthly') {
                $query->whereYear('purchase_date', now()->year)
                      ->whereMonth('purchase_date', now()->month);
            }
        } elseif ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('purchase_date', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('scrap_name', 'like', "%$search%")
                  ->orWhereHas('supplier', function ($q) use ($search) {
                      $q->where('name', 'like', "%$search%");
                  });
            });
        }

        $purchases = $query->latest()->paginate(10);

        $totalAmount = $purchases->sum('grand_total');
        $totalWeight = $purchases->sum('weight');
        $uniqueItemCount = $purchases->unique('scrap_name')->count();

        return view('purchase.report', compact('purchases', 'totalAmount', 'totalWeight', 'uniqueItemCount'));
    }

    public function addOpeningBalance(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'opening_balance' => 'required|numeric|min:0',
            'opening_balance_type' => 'required|in:debit,credit',
        ]);

        $supplier = Supplier::findOrFail($request->supplier_id);
        $supplier->opening_balance = $request->opening_balance;
        $supplier->opening_balance_type = $request->opening_balance_type;
        $supplier->save();

        return redirect()->route('supplier.ledger', ['supplier_id' => $request->supplier_id])
                         ->with('success', __('messages.opening_balance_added_successfully'));
    }

    public function debugWeeklyPurchases()
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        $purchases = ScrapPurchase::whereBetween('purchase_date', [$startOfWeek, $endOfWeek])->get();
        dd($purchases);
    }
}
