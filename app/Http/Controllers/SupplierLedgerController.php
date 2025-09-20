<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\ScrapPurchase;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class SupplierLedgerController extends Controller
{
    public function show(Request $request)
    {
        $suppliers = Supplier::orderBy('name')->get();
        $selectedSupplier = null;
        $transactions = collect();
        $totalPurchaseAmount = 0;
        $totalPaidAmount = 0;
        $totalRemaining = 0;

        if ($request->has('supplier_id') && $request->supplier_id) {
            $selectedSupplier = Supplier::findOrFail($request->supplier_id);

            $openingBalance = (float) $selectedSupplier->opening_balance;
            $openingBalanceType = $selectedSupplier->opening_balance_type;

            // Debit (supplier owes you), Credit (you owe supplier)
            $balance = ($openingBalanceType == 'debit') ? -$openingBalance : $openingBalance;

            $purchases = ScrapPurchase::where('supplier_id', $selectedSupplier->id)
                ->select('purchase_date as date', DB::raw("'Purchase' as description"), 'grand_total as debit', DB::raw("0 as credit"))
                ->addSelect(DB::raw('0 as balance'));

            $payments = Payment::where('supplier_id', $selectedSupplier->id)
                ->select('payment_date as date', DB::raw("'Payment' as description"), DB::raw("0 as debit"), 'amount as credit')
                ->addSelect(DB::raw('0 as balance'));

            $transactions = $purchases->union($payments)->orderBy('date')->get();

            $runningBalance = $balance;
            $transactions = $transactions->map(function ($transaction) use (&$runningBalance) {
                $runningBalance += $transaction->debit - $transaction->credit;
                $transaction->balance = $runningBalance;
                return $transaction;
            });

            if ($openingBalance > 0) {
                $transactions->prepend((object)[
                    'date' => $selectedSupplier->created_at,
                    'description' => 'Opening Balance',
                    'debit' => $openingBalanceType == 'credit' ? $openingBalance : 0, // You owe supplier
                    'credit' => $openingBalanceType == 'debit' ? $openingBalance : 0, // Supplier owes you
                    'balance' => $balance,
                ]);
            }

            $totalPurchaseAmount = $transactions->sum('debit');
            $totalPaidAmount = $transactions->sum('credit');
            $totalRemaining = $balance + $totalPurchaseAmount - $totalPaidAmount;
        }

        return view('purchase.supplier_ledger', [
            'suppliers' => $suppliers,
            'selectedSupplier' => $selectedSupplier,
            'transactions' => $transactions,
            'totalPurchaseAmount' => $totalPurchaseAmount,
            'totalPaidAmount' => $totalPaidAmount,
            'totalRemaining' => $totalRemaining,
        ]);
    }

    public function storePayment(Request $request)
    { 
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
        ]);

        Payment::create([
            'supplier_id' => $request->supplier_id,
            'amount' => $request->payment_amount,
            'payment_date' => $request->payment_date,
        ]);

        return redirect()->route('supplier.ledger', ['supplier_id' => $request->supplier_id])
                         ->with('success', 'Payment added successfully.');
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
                         ->with('success', 'Opening balance set successfully!');
    }
}
