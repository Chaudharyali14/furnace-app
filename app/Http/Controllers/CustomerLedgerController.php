<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerLedgerController extends Controller
{
    public function show(Request $request)
    {
        $customers = Customer::orderBy('name')->get();
        $selectedCustomer = null;
        $transactions = collect();
        $totalSalesAmount = 0;
        $totalPaidAmount = 0;
        $totalRemaining = 0;

        if ($request->has('customer_id') && $request->customer_id) {
            $selectedCustomer = Customer::findOrFail($request->customer_id);

            $openingBalance = (float) $selectedCustomer->opening_balance;
            $openingBalanceType = $selectedCustomer->opening_balance_type;

            $balance = ($openingBalanceType == 'debit') ? $openingBalance : -$openingBalance;

            $sales = Sale::where('customer_id', $selectedCustomer->id)->get();
            $payments = Payment::where('customer_id', $selectedCustomer->id)->get();

            foreach ($sales as $sale) {
                $transactions->push([
                    'date' => $sale->sale_date,
                    'description' => 'Sale',
                    'debit' => $sale->sub_total,
                    'credit' => 0,
                ]);

                if ($sale->discount > 0) {
                    $transactions->push([
                        'date' => $sale->sale_date,
                        'description' => 'Discount',
                        'debit' => 0,
                        'credit' => $sale->discount,
                    ]);
                }


            }

            foreach ($payments as $payment) {
                $transactions->push([
                    'date' => $payment->payment_date,
                    'description' => $payment->description ?? 'Payment',
                    'debit' => 0,
                    'credit' => $payment->amount,
                ]);
            }

            $transactions = $transactions->sortBy('date');

            $totalSalesAmount = $transactions->sum('debit');
            $totalPaidAmount = $transactions->sum('credit');

            $runningBalance = $balance;
            $transactions = $transactions->map(function ($transaction) use (&$runningBalance) {
                $runningBalance += $transaction['debit'] - $transaction['credit'];
                $transaction['balance'] = $runningBalance;
                return $transaction;
            });

            if ($openingBalance > 0) {
                $transactions->prepend([
                    'date' => $selectedCustomer->created_at,
                    'description' => 'Opening Balance',
                    'debit' => $openingBalanceType == 'debit' ? $openingBalance : 0,
                    'credit' => $openingBalanceType == 'credit' ? $openingBalance : 0,
                    'balance' => $balance,
                ]);
            }

            $totalRemaining = $transactions->isEmpty() ? $balance : $transactions->last()['balance'];
        }

        return view('sales.customer_ledger', [
            'customers' => $customers,
            'selectedCustomer' => $selectedCustomer,
            'transactions' => $transactions,
            'totalSalesAmount' => $totalSalesAmount,
            'totalPaidAmount' => $totalPaidAmount,
            'totalRemaining' => $totalRemaining,
        ]);
    }

    public function storePayment(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $customerId = $request->input('customer_id');
        $paymentAmount = (float) $request->input('payment_amount');
        $paymentDate = $request->input('payment_date');
        $description = $request->input('description');

        Log::info('Attempting to store payment for customer_id: ' . $customerId . ' amount: ' . $paymentAmount);
        try {
            DB::transaction(function () use ($customerId, $paymentAmount, $paymentDate, $description) {
                Payment::create([
                    'customer_id' => $customerId,
                    'amount' => $paymentAmount,
                    'payment_date' => $paymentDate,
                    'description' => $description,
                ]);
                Log::info('Payment record created successfully for customer_id: ' . $customerId);

            $salesToUpdate = Sale::where('customer_id', $customerId)
                ->where('remaining_amount', '>', 0)
                ->orderBy('sale_date', 'asc')
                ->get();

                            $remainingPayment = $paymentAmount;
            
                            foreach ($salesToUpdate as $sale) {
                                if ($remainingPayment <= 0) {
                                    break;
                                }
            
                                $amountToApply = min($remainingPayment, $sale->remaining_amount);
            
                                $sale->paid_amount += $amountToApply;
                                $sale->remaining_amount -= $amountToApply;
                                $sale->save();
                                Log::info('Updated sale ' . $sale->id . ': paid_amount=' . $sale->paid_amount . ', remaining_amount=' . $sale->remaining_amount);
            
                                $remainingPayment -= $amountToApply;
                            }
                            Log::info('Sales updated successfully for customer_id: ' . $customerId);
                        });
                    } catch (\Exception $e) {
                        Log::error('Error storing payment for customer_id: ' . $customerId . ': ' . $e->getMessage());
                        return redirect()->back()->withInput()->with('error', 'Failed to add payment. Please try again.');
                    }
        return redirect()->route('customer.ledger', ['customer_id' => $customerId])
                         ->with('success', 'Payment added and adjusted successfully.');
    }

    public function addOpeningBalance(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'opening_balance' => 'required|numeric|min:0',
            'opening_balance_type' => 'required|in:debit,credit',
        ]);

        $customer = Customer::findOrFail($request->customer_id);
        $customer->opening_balance = $request->opening_balance;
        $customer->opening_balance_type = $request->opening_balance_type;
        $customer->save();

        return redirect()->route('customer.ledger', ['customer_id' => $request->customer_id])
                         ->with('success', 'Opening balance set successfully!');
    }
}
