<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\ScrapPurchase;
use App\Models\Expense;
use App\Models\ElectricityLog;
use App\Models\EmployeeSalary;
use Carbon\Carbon;

class ProfitLossController extends Controller
{
    public function generateReport(Request $request)
    {
        $filter = $request->input('filter');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // 🔍 Handle predefined filters
        if ($filter) {
            switch ($filter) {
                case 'daily':
                    $startDate = Carbon::today()->startOfDay();
                    $endDate = Carbon::today()->endOfDay();
                    break;
                case 'weekly':
                    $startDate = Carbon::now()->startOfWeek();
                    $endDate = Carbon::now()->endOfWeek();
                    break;
                case 'monthly':
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();
                    break;
            }
        } elseif ($startDate && $endDate) {
            // agar user ne manually date range diya hai to parse kar lo
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();
        }

        // ---------------- Sales ----------------
        $salesQuery = Sale::query();
        if ($startDate && $endDate) {
            $salesQuery->whereBetween('sale_date', [$startDate, $endDate]);
        }
        $totalSales = $salesQuery->sum('sub_total');
        $sales = $salesQuery->latest()->paginate(10);

        // ---------------- Purchases ----------------
        $purchasesQuery = ScrapPurchase::query();
        if ($startDate && $endDate) {
            $purchasesQuery->whereBetween('purchase_date', [$startDate, $endDate]);
        }
        $totalPurchases = $purchasesQuery->sum('grand_total');
        $purchases = $purchasesQuery->get();

        // ---------------- Expenses ----------------
        $expensesQuery = Expense::query();
        if ($startDate && $endDate) {
            $expensesQuery->whereBetween('expense_date', [$startDate, $endDate]);
        }
        $totalExpenses = $expensesQuery->sum('amount');
        $expenses = $expensesQuery->get();

        // ---------------- Salaries ----------------
        $salariesQuery = EmployeeSalary::query();
        if ($startDate && $endDate) {
            $salariesQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        $salaries = $salariesQuery->get();
        $totalSalaries = $salaries->sum('amount');

        // ---------------- Electricity ----------------
        $electricityQuery = ElectricityLog::query();
        if ($startDate && $endDate) {
            $electricityQuery->whereBetween('log_date', [$startDate, $endDate]);
        }
        $totalElectricity = $electricityQuery->sum('total_cost');
        $electricityLogs = $electricityQuery->get();

        // ---------------- Profit / Loss ----------------
        $profitLoss = $totalSales - ($totalPurchases + $totalExpenses + $totalSalaries + $totalElectricity);

        return view('reports.profit_loss', compact(
            'totalSales',
            'totalPurchases',
            'totalExpenses',
            'totalSalaries',
            'totalElectricity',
            'profitLoss',
            'sales',
            'purchases',
            'expenses',
            'salaries',
            'electricityLogs',
            'filter',
            'startDate',
            'endDate'
        ));
    }
}
