<?php 

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Expense::query();

        $filter_type = $request->input('filter_type');
        $start_date  = $request->input('start_date');
        $end_date    = $request->input('end_date');
        $search      = $request->input('search');

        if ($start_date && $end_date) {
            // Custom Date Range
            $query->whereBetween('expense_date', [$start_date, $end_date]);
        } elseif ($filter_type) {
            // Predefined Filters
            if ($filter_type === 'daily') {
                $query->whereDate('expense_date', today());
            } elseif ($filter_type === 'weekly') {
                $query->whereBetween('expense_date', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($filter_type === 'monthly') {
                $query->whereYear('expense_date', now()->year)
                      ->whereMonth('expense_date', now()->month);
            }
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('category', 'like', '%' . $search . '%');
            });
        }

        $expenses = $query->paginate(10);

        return view('expenses.index', compact('expenses', 'filter_type', 'start_date', 'end_date', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('expenses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'amount'       => 'required|numeric',
            'category'     => 'required|string|max:255',
            'expense_date' => 'required|date',
            'description'  => 'nullable|string',
        ]);

        Expense::create($request->all());

        return redirect()->route('expenses.index')
            ->with('success', 'Expense created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        return view('expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        return view('expenses.edit', compact('expense'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'amount'       => 'required|numeric',
            'category'     => 'required|string|max:255',
            'expense_date' => 'required|date',
            'description'  => 'nullable|string',
        ]);

        $expense->update($request->all());

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully');
    }
}
