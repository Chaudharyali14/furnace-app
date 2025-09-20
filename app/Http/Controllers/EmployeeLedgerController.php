<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\Payment;
use Illuminate\Http\Request;

class EmployeeLedgerController extends Controller
{
    public function show(Request $request)
    {
        $employees = Employee::orderBy('name')->get();
        $selectedEmployee = null;
        $transactions = collect();

        if ($request->has('employee_id') && $request->employee_id) {
            $selectedEmployee = Employee::findOrFail($request->employee_id);

            $openingBalance = (float) $selectedEmployee->opening_balance;
            $openingBalanceType = $selectedEmployee->opening_balance_type;

            $balance = ($openingBalanceType == 'debit') ? $openingBalance : -$openingBalance;

            $payments = Payment::where('employee_id', $selectedEmployee->id)->get();
            $salaries = EmployeeSalary::where('employee_id', $selectedEmployee->id)->get();

            foreach ($payments as $payment) {
                $transactions->push([
                    'date' => $payment->payment_date,
                    'description' => $payment->description ?? 'Payment',
                    'debit' => $payment->type == 'debit' ? $payment->amount : 0,
                    'credit' => $payment->type == 'credit' ? $payment->amount : 0,
                ]);
            }

            foreach ($salaries as $salary) {
                $transactions->push([
                    'date' => $salary->salary_month,
                    'description' => 'Salary for ' . date('F Y', strtotime($salary->salary_month)),
                    'debit' => $salary->amount,
                    'credit' => 0,
                ]);
            }

            $transactions = $transactions->sortBy('date');

            $runningBalance = $balance;
            $transactions = $transactions->map(function ($transaction) use (&$runningBalance) {
                $runningBalance += $transaction['debit'] - $transaction['credit'];
                $transaction['balance'] = $runningBalance;
                return $transaction;
            });

            if ($openingBalance > 0) {
                $transactions->prepend([
                    'date' => $selectedEmployee->created_at,
                    'description' => 'Opening Balance',
                    'debit' => $openingBalanceType == 'debit' ? $openingBalance : 0,
                    'credit' => $openingBalanceType == 'credit' ? $openingBalance : 0,
                    'balance' => $balance,
                ]);
            }
        }

        return view('employees.ledger', [
            'employees' => $employees,
            'selectedEmployee' => $selectedEmployee,
            'transactions' => $transactions,
        ]);
    }

    public function storePayment(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'description' => 'nullable|string',
            'payment_type' => 'required|in:debit,credit',
        ]);

        Payment::create([
            'employee_id' => $request->employee_id,
            'amount' => $request->payment_amount,
            'payment_date' => $request->payment_date,
            'description' => $request->description,
            'type' => $request->payment_type,
        ]);

        return redirect()->route('employee.ledger', ['employee_id' => $request->employee_id])
                         ->with('success', 'Payment added successfully!');
    }

    public function storeSalary(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'salary_amount' => 'required|numeric|min:0.01',
            'salary_month' => 'required|date_format:Y-m',
        ]);

        EmployeeSalary::create([
            'employee_id' => $request->employee_id,
            'amount' => $request->salary_amount,
            'salary_month' => $request->salary_month . '-01',
        ]);

        return redirect()->route('employee.ledger', ['employee_id' => $request->employee_id])
                         ->with('success', 'Salary added successfully!');
    }
}
