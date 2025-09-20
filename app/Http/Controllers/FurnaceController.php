<?php 

namespace App\Http\Controllers;

use App\Models\RawMaterialStock;
use Illuminate\Http\Request;

class FurnaceController extends Controller
{
    public function issue_to_furnace_form()
    {
        $raw_materials = RawMaterialStock::all();
        return view('furnace.issue_to_furnace', compact('raw_materials'));
    }

    public function issue_to_furnace(Request $request)
    {
        $request->validate([
            'raw_material_name' => 'required',
            'quantity' => 'required|numeric|min:0.01',
        ]);

        $raw_material_name = $request->input('raw_material_name');
        $quantity = $request->input('quantity');

        $stock = RawMaterialStock::where('raw_material_name', $raw_material_name)->first();

        if ($stock && $stock->remaining_stock_qty >= $quantity) {
            $stock->total_issued_qty += $quantity;
            $stock->remaining_stock_qty -= $quantity;
            $stock->save();

            return redirect()->route('furnace.issue_to_furnace_form')
                             ->with('success', __('messages.raw_material_successfully_issued_to_furnace'));
        }

        return redirect()->route('furnace.issue_to_furnace_form')
                         ->with('error', __('messages.failed_to_issue_raw_material'));
    }

    public function raw_material_stock(Request $request)
    {
        $query = RawMaterialStock::query();

        // ✅ Daily / Weekly / Monthly Filter Fix
        if ($request->filled('filter')) {
            $filter = $request->input('filter');

            if ($filter === 'daily') {
                $query->whereDate('created_at', today());
            } elseif ($filter === 'weekly') {
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($filter === 'monthly') {
                $query->whereYear('created_at', now()->year)
                      ->whereMonth('created_at', now()->month);
            }
        }

        // ✅ Custom Date Range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->input('start_date'), 
                $request->input('end_date')
            ]);
        }

        // ✅ Search Filter
        if ($request->filled('search')) {
            $query->where('raw_material_name', 'like', '%' . $request->input('search') . '%');
        }

        $raw_materials_stock = $query->latest()->paginate(10);
        $remaining_stock_subtotal = $raw_materials_stock->sum('remaining_stock_qty');

        return view('furnace.raw_material_stock', compact('raw_materials_stock', 'remaining_stock_subtotal'));
    }

    public function edit_stock(RawMaterialStock $stock)
    {
        return view('furnace.edit_stock', compact('stock'));
    }

    public function update_stock(Request $request, RawMaterialStock $stock)
    {
        $request->validate([
            'raw_material_name' => 'required',
            'total_purchased_qty' => 'required|numeric',
            'total_issued_qty' => 'required|numeric',
            'remaining_stock_qty' => 'required|numeric',
        ]);

        $stock->update($request->all());

        return redirect()->route('furnace.raw_material_stock')
                         ->with('success', __('messages.stock_updated_successfully'));
    }

    public function delete_stock(RawMaterialStock $stock)
    {
        $stock->delete();

        return redirect()->route('furnace.raw_material_stock')
                         ->with('success', __('messages.stock_item_deleted_successfully'));
    }
}
