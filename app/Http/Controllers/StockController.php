<?php

namespace App\Http\Controllers;

use App\Models\CcPlant;
use App\Models\Sale;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\FinishedGoodStock;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index()
    {
        $stocks = FinishedGoodStock::join('cc_plant', 'finished_good_stocks.cc_plant_id', '=', 'cc_plant.id')
            ->select(
                'finished_good_stocks.item_name',
                'cc_plant.billet_size_inch',
                DB::raw('SUM(finished_good_stocks.weight) as total_weight')
            )
            ->groupBy('finished_good_stocks.item_name', 'cc_plant.billet_size_inch')
            ->get();

        return view('stock.index', compact('stocks'));
    }

    public function createSale()
    {
        $suppliers = Supplier::all();
        $castItems = CcPlant::select('cast_item_name')->distinct()->get();
        return view('stock.create_sale', compact('suppliers', 'castItems'));
    }

    public function storeSale(Request $request)
    {
        $request->validate([
            'cast_item_name' => 'required|string',
            'supplier_id' => 'required|exists:suppliers,id',
            'quantity' => 'required|integer',
            'date' => 'required|date',
            'rate' => 'required|numeric',
        ]);

        $ccPlant = CcPlant::where('cast_item_name', $request->cast_item_name)->first();

        Sale::create([
            'cast_item_name' => $request->cast_item_name,
            'supplier_id' => $request->supplier_id,
            'quantity' => $request->quantity,
            'date' => $request->date,
            'rate' => $request->rate,
            'total_weight' => $ccPlant->casted_metal_kg * $request->quantity,
            'sub_total' => $request->rate * $request->quantity,
            'discount' => $request->discount,
        ]);

        return redirect()->route('stock.index')->with('success', 'Sale recorded successfully.');
    }
}

