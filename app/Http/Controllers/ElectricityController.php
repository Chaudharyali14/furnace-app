<?php 

namespace App\Http\Controllers;

use App\Models\ElectricityLog;
use Illuminate\Http\Request;

class ElectricityController extends Controller
{
    public function index()
    {
        $logs = ElectricityLog::all();
        $totals = [
            'total_units' => $logs->sum('unit_consumed'),
            'total_cost' => $logs->sum('total_cost'),
        ];
        return view('electricity.index', compact('logs', 'totals'));
    }

    public function add_log(Request $request)
    {
        $request->validate([
            'furnace_id' => 'required|numeric',
            'heat_number' => 'required',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time',
            'starting_unit' => 'required|numeric',
            'ending_unit' => 'required|numeric|gte:starting_unit',
            'unit_rate' => 'required|numeric',
        ]);

        $starting_unit = $request->input('starting_unit');
        $ending_unit = $request->input('ending_unit');
        $unit_consumed = $ending_unit - $starting_unit;
        $unit_rate = $request->input('unit_rate');
        $total_cost = $unit_consumed * $unit_rate;

        try {
            ElectricityLog::create([
                'furnace_id' => $request->input('furnace_id'),
                'heat_number' => $request->input('heat_number'),
                'start_time' => $request->input('start_time'),
                'end_time' => $request->input('end_time'),
                'starting_unit' => $starting_unit,
                'ending_unit' => $ending_unit,
                'unit_consumed' => $unit_consumed,
                'unit_rate' => $unit_rate,
                'total_cost' => $total_cost,
                'log_date' => $request->input('start_time'),
            ]);

            return redirect()->route('electricity.index')->with('success', 'Electricity log added successfully.');
        } catch (\Exception $e) {
            return redirect()->route('electricity.index')->with('error', 'Failed to add electricity log. ' . $e->getMessage());
        }
    }

    public function log_table(Request $request)
    {
        $query = ElectricityLog::query();

        $filter_type = $request->input('filter_type');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        if ($start_date && $end_date) {
            // Custom date range
            $query->whereBetween('log_date', [$start_date, $end_date]);
        } elseif ($filter_type) {
            // Predefined filters
            if ($filter_type === 'daily') {
                $query->whereDate('log_date', today());
            } elseif ($filter_type === 'weekly') {
                $query->whereBetween('log_date', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($filter_type === 'monthly') {
                $query->whereYear('log_date', now()->year)
                      ->whereMonth('log_date', now()->month);
            }
        }

        $logs = $query->get();
        $totals = [
            'total_units' => $logs->sum('unit_consumed'),
            'total_cost' => $logs->sum('total_cost'),
        ];

        return view('electricity.log_table', compact('logs', 'totals', 'filter_type', 'start_date', 'end_date'));
    }

    public function edit_log(ElectricityLog $log)
    {
        return view('electricity.edit', compact('log'));
    }

    public function update_log(Request $request, ElectricityLog $log)
    {
        $request->validate([
            'furnace_id' => 'required|numeric',
            'heat_number' => 'required',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time',
            'starting_unit' => 'required|numeric',
            'ending_unit' => 'required|numeric|gte:starting_unit',
            'unit_rate' => 'required|numeric',
        ]);

        $starting_unit = $request->input('starting_unit');
        $ending_unit = $request->input('ending_unit');
        $unit_consumed = $ending_unit - $starting_unit;
        $unit_rate = $request->input('unit_rate');
        $total_cost = $unit_consumed * $unit_rate;

        try {
            $log->update([
                'furnace_id' => $request->input('furnace_id'),
                'heat_number' => $request->input('heat_number'),
                'start_time' => $request->input('start_time'),
                'end_time' => $request->input('end_time'),
                'starting_unit' => $starting_unit,
                'ending_unit' => $ending_unit,
                'unit_consumed' => $unit_consumed,
                'unit_rate' => $unit_rate,
                'total_cost' => $total_cost,
                'log_date' => $request->input('start_time'),
            ]);

            return redirect()->route('electricity.log_table')->with('success', 'Electricity log updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('electricity.log_table')->with('error', 'Failed to update electricity log. ' . $e->getMessage());
        }
    }

    public function delete_log(ElectricityLog $log)
    {
        try {
            $log->delete();

            return redirect()->route('electricity.log_table')->with('success', 'Electricity log deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('electricity.log_table')->with('error', 'Failed to delete electricity log. ' . $e->getMessage());
        }
    }
}
