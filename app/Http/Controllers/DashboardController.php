<?php

namespace App\Http\Controllers;

use App\Models\RawMaterialStock;
use App\Models\Sale;
use App\Models\ScrapPurchase;
use App\Models\Expense;
use App\Models\FinishedGoodStock;
use App\Models\EmployeeSalary;
use App\Models\ElectricityLog;

class DashboardController extends Controller
{
    public function index()
    {
        $totalSales = Sale::sum('sub_total');
        $totalPurchases = ScrapPurchase::sum('grand_total');
        $totalExpenses = Expense::sum('amount');
        $totalSalaries = EmployeeSalary::sum('amount');
        $totalElectricity = ElectricityLog::sum('total_cost');

        $profitLoss = $totalSales - ($totalPurchases + $totalExpenses + $totalSalaries + $totalElectricity);

        $recentSales = Sale::with('customer')->latest()->take(3)->get()->map(function ($item) {
            return [
                'description' => 'Sale to ' . $item->customer->name,
                'date' => $item->created_at,
                'amount' => $item->sub_total,
                'type' => 'sale'
            ];
        });
        $recentPurchases = ScrapPurchase::with('supplier')->latest()->take(3)->get()->map(function ($item) {
            return [
                'description' => 'Purchase from ' . $item->supplier->name,
                'date' => $item->created_at,
                'amount' => $item->grand_total,
                'type' => 'purchase'
            ];
        });
        $recentExpenses = Expense::latest()->take(3)->get()->map(function ($item) {
            return [
                'description' => $item->title,
                'date' => $item->created_at,
                'amount' => $item->amount,
                'type' => 'expense'
            ];
        });

        $recentActivities = collect($recentSales)
            ->merge($recentPurchases)
            ->merge($recentExpenses)
            ->sortByDesc('date');

        $rawMaterialStock = RawMaterialStock::select('raw_material_name', 'remaining_stock_qty')->get();

        // Data for charts
        $rawMaterialStockChart = $rawMaterialStock->map(function ($stock) {
            return [
                'name' => $stock->raw_material_name,
                'quantity' => $stock->remaining_stock_qty,
            ];
        });

        return view('dashboard', compact(
            'totalSales',
            'totalPurchases',
            'totalExpenses',
            'profitLoss',
            'recentActivities',
            'rawMaterialStockChart'
        ));
    }
}
