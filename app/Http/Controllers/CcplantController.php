<?php 

namespace App\Http\Controllers;

use App\Models\CcPlant;
use App\Models\FinishedGoodStock;
use App\Models\RawMaterialStock;
use Illuminate\Http\Request;
use Exception;

class CcplantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = CcPlant::query();

            $filter     = $request->input('filter');
            $start_date = $request->input('start_date');
            $end_date   = $request->input('end_date');
            $search     = $request->input('search');

            // ✅ Date Range has highest priority
            if ($start_date && $end_date) {
                $query->whereBetween('date', [$start_date, $end_date]);
            } elseif ($filter) {
                if ($filter === 'daily') {
                    $query->whereDate('date', today());
                } elseif ($filter === 'weekly') {
                    $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
                } elseif ($filter === 'monthly') {
                    $query->whereYear('date', now()->year)
                          ->whereMonth('date', now()->month);
                }
            }

            // ✅ Search filter
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('heat_no', 'like', "%{$search}%")
                      ->orWhere('cast_item_name', 'like', "%{$search}%");
                });
            }

            // ✅ Use pagination instead of get()
            $heats = $query->orderBy('date', 'desc')->paginate(15);

            return view('ccplant.index', compact('heats'))
                ->with('success', session('success'))
                ->with('error', session('error'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to load CC Plant data. ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('ccplant.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'date'            => 'required|date',
                'heat_no'         => 'required|string|max:255',
                'total_metal'     => 'required|numeric|min:0',
                'casted_metal'    => 'required|numeric|min:0',
                'billet_size_inch'=> 'required|numeric|min:0',
                'cast_item_name'  => 'required|string|max:255',
            ]);

            $total_metal   = $request->total_metal;
            $casted_metal  = $request->casted_metal;
            $uncast_metal  = $total_metal - $casted_metal;

            $ccPlant = CcPlant::create([
                'date'            => $request->date,
                'heat_no'         => $request->heat_no,
                'total_metal'     => $total_metal,
                'casted_metal'    => $casted_metal,
                'uncast_metal'    => $uncast_metal,
                'billet_size_inch'=> $request->billet_size_inch,
                'cast_item_name'  => $request->cast_item_name,
            ]);

            // ✅ Finished Goods update
            FinishedGoodStock::create([
                'cc_plant_id' => $ccPlant->id,
                'item_name'   => $request->cast_item_name,
                'weight'      => $casted_metal,
            ]);

            // ✅ Raw Material update (uncast metal)
            if ($uncast_metal > 0) {
                $rawMaterialStock = RawMaterialStock::firstOrCreate(['raw_material_name' => 'Uncast Metal']);
                $rawMaterialStock->increment('total_purchased_qty', $uncast_metal);
                $rawMaterialStock->increment('remaining_stock_qty', $uncast_metal);
            }

            return redirect()->route('ccplant.index')->with('success', 'CC Plant data saved successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to save CC Plant data. ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CcPlant $heat)
    {
        try {
            return view('ccplant.show', compact('heat'));
        } catch (Exception $e) {
            return redirect()->route('ccplant.index')->with('error', 'Failed to load heat details.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CcPlant $heat)
    {
        try {
            return view('ccplant.edit', compact('heat'));
        } catch (Exception $e) {
            return redirect()->route('ccplant.index')->with('error', 'Failed to load edit form.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CcPlant $heat)
    {
        try {
            $request->validate([
                'date'            => 'required|date',
                'heat_no'         => 'required|string|max:255',
                'total_metal'     => 'required|numeric|min:0',
                'casted_metal'    => 'required|numeric|min:0',
                'billet_size_inch'=> 'required|numeric|min:0',
                'cast_item_name'  => 'required|string|max:255',
            ]);

            $old_uncast_metal = $heat->uncast_metal;

            $total_metal   = $request->total_metal;
            $casted_metal  = $request->casted_metal;
            $new_uncast_metal = $total_metal - $casted_metal;

            $heat->update([
                'date'            => $request->date,
                'heat_no'         => $request->heat_no,
                'total_metal'     => $total_metal,
                'casted_metal'    => $casted_metal,
                'uncast_metal'    => $new_uncast_metal,
                'billet_size_inch'=> $request->billet_size_inch,
                'cast_item_name'  => $request->cast_item_name,
            ]);

            // ✅ Finished Goods update
            $finishedGoodStock = FinishedGoodStock::where('cc_plant_id', $heat->id)->first();
            if ($finishedGoodStock) {
                $finishedGoodStock->update([
                    'item_name' => $request->cast_item_name,
                    'weight'    => $casted_metal,
                ]);
            } else {
                FinishedGoodStock::create([
                    'cc_plant_id' => $heat->id,
                    'item_name'   => $request->cast_item_name,
                    'weight'      => $casted_metal,
                ]);
            }

            // ✅ Raw Material stock adjust
            $uncast_metal_diff = $new_uncast_metal - $old_uncast_metal;
            if ($uncast_metal_diff != 0) {
                $rawMaterialStock = RawMaterialStock::firstOrCreate(['raw_material_name' => 'Uncast Metal']);
                $rawMaterialStock->increment('total_purchased_qty', $uncast_metal_diff);
                $rawMaterialStock->increment('remaining_stock_qty', $uncast_metal_diff);
            }

            return redirect()->route('ccplant.index')->with('success', 'Heat updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to update heat. ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CcPlant $heat)
    {
        try {
            // ✅ Raw Material stock adjust
            if ($heat->uncast_metal > 0) {
                $rawMaterialStock = RawMaterialStock::where('raw_material_name', 'Uncast Metal')->first();
                if ($rawMaterialStock) {
                    $rawMaterialStock->decrement('total_purchased_qty', $heat->uncast_metal);
                    $rawMaterialStock->decrement('remaining_stock_qty', $heat->uncast_metal);
                }
            }

            // ✅ Finished Goods delete
            $finishedGoodStock = FinishedGoodStock::where('cc_plant_id', $heat->id)->first();
            if ($finishedGoodStock) {
                $finishedGoodStock->delete();
            }

            $heat->delete();
            
            return redirect()->route('ccplant.index')->with('success', 'Heat deleted successfully.');
        } catch (Exception $e) {
            return redirect()->route('ccplant.index')->with('error', 'Failed to delete heat. ' . $e->getMessage());
        }
    }
}
