@extends('layouts.app')

@section('title', __('messages.supplier_ledger'))

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ __('messages.supplier_ledger') }}</h1>
        <a href="{{ route('purchase.index') }}" class="btn btn-secondary">{{ __('messages.back_to_purchases') }}</a>
    </div>

    <!-- Session Messages -->
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Supplier Selection Form -->
    <div class="card mb-4">
        <div class="card-header">{{ __('messages.select_supplier') }}</div>
        <div class="card-body">
            <form method="GET" action="{{ route('supplier.ledger') }}">
                <div class="row">
                    <div class="col-lg-8 col-md-12 mb-3">
                        <select name="supplier_id" class="form-select" required>
                            <option value="">-- {{ __('messages.select_a_supplier') }} --</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ ($selectedSupplier && $selectedSupplier->id == $supplier->id) ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <button type="submit" class="btn btn-primary w-100">{{ __('messages.show_ledger') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if ($selectedSupplier && $selectedSupplier->opening_balance == 0)
        <div class="card mt-4">
            <div class="card-header">
                <h5>{{ __('messages.set_opening_balance') }}</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('supplier.ledger.opening_balance') }}">
                    @csrf
                    <input type="hidden" name="supplier_id" value="{{ $selectedSupplier->id }}">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                            <label for="opening_balance" class="form-label">{{ __('messages.opening_balance') }}</label>
                            <input type="number" step="0.01" class="form-control" id="opening_balance" name="opening_balance" required>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                            <label for="opening_balance_type" class="form-label">{{ __('messages.balance_type') }}</label>
                            <select name="opening_balance_type" id="opening_balance_type" class="form-select" required>
                                <option value="debit">{{ __('messages.debit_supplier_owes_you') }}</option>
                                <option value="credit">{{ __('messages.credit_you_owe_supplier') }}</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('messages.set_opening_balance') }}</button>
                </form>
            </div>
        </div>
    @endif

    @if ($selectedSupplier)
        <!-- Ledger Details -->
        <div class="d-flex justify-content-end mb-3">
            <button id="printBtn" class="btn btn-secondary me-2"><i class="fas fa-print"></i> {{ __('messages.print') }}</button>
            <button id="excelBtn" class="btn btn-success me-2"><i class="fas fa-file-excel"></i> {{ __('messages.excel') }}</button>
        </div>
        <div class="card">
            <div class="card-header">
                <h4>{{ __('messages.ledger_for') }}: {{ $selectedSupplier->name }}</h4>
            </div>
            <div class="card-body">
                <h5 class="mb-3">{{ __('messages.transaction_history') }}</h5>

                <!-- Filter and Search Form -->
                <form method="GET" action="{{ route('supplier.ledger') }}" class="mb-4">
                    <input type="hidden" name="supplier_id" value="{{ $selectedSupplier->id }}">
                    <div class="row g-2">
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                            <input type="text" name="search" class="form-control" placeholder="{{ __('messages.search') }}" value="{{ request('search') }}">
                        </div>
                        <div class="col-lg-5 col-md-6 col-sm-12 mb-3">
                            <div class="input-group">
                                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                                <button type="submit" class="btn btn-primary">{{ __('messages.filter') }}</button>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-12 col-sm-12 mb-3">
                            <div class="btn-group w-100">
                                <button type="submit" name="filter" value="daily" class="btn btn-outline-secondary">{{ __('messages.daily') }}</button>
                                <button type="submit" name="filter" value="weekly" class="btn btn-outline-secondary">{{ __('messages.weekly') }}</button>
                                <button type="submit" name="filter" value="monthly" class="btn btn-outline-secondary">{{ __('messages.monthly') }}</button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="supplier-ledger-table">
                        <thead class="table-dark">
                            <tr>
                                <th>{{ __('messages.date') }}</th>
                                <th>{{ __('messages.description') }}</th>
                                <th>{{ __('messages.debit') }}</th>
                                <th>{{ __('messages.credit') }}</th>
                                <th>{{ __('messages.balance') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $transaction)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($transaction['date'])->format('d-M-Y') }}</td>
                                    <td>{{ $transaction['description'] }}</td>
                                    <td>{{ $transaction['debit'] > 0 ? number_format($transaction['debit'], 2) : '' }}</td>
                                    <td>{{ $transaction['credit'] > 0 ? number_format($transaction['credit'], 2) : '' }}</td>
                                    <td>{{ number_format($transaction['balance'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">{{ __('messages.no_transactions_found_for_this_supplier') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-secondary fw-bold">
                            <tr>
                                <td colspan="2" class="text-end">{{ __('messages.totals') }}:</td>
                                <td>{{ number_format($totalPurchaseAmount, 2) }}</td>
                                <td>{{ number_format($totalPaidAmount, 2) }}</td>
                                <td class="text-danger">{{ number_format($totalRemaining, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add Payment Form -->
        <div class="card mt-4">
            <div class="card-header">
                <h5>{{ __('messages.add_new_payment') }}</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('supplier.ledger.payment') }}">
                    @csrf
                    <input type="hidden" name="supplier_id" value="{{ $selectedSupplier->id }}">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                            <label for="payment_amount" class="form-label">{{ __('messages.payment_amount') }}</label>
                            <input type="number" step="0.01" class="form-control @error('payment_amount') is-invalid @enderror" id="payment_amount" name="payment_amount" required>
                            @error('payment_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                            <label for="payment_date" class="form-label">{{ __('messages.payment_date') }}</label>
                            <input type="date" class="form-control @error('payment_date') is-invalid @enderror" id="payment_date" name="payment_date" value="{{ date('Y-m-d') }}" required>
                            @error('payment_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-12 mb-3">
                            <label for="description" class="form-label">{{ __('messages.description') }}</label>
                            <input type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description">
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success">{{ __('messages.add_payment') }}</button>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const printBtn = document.getElementById('printBtn');
        if (printBtn) {
            printBtn.addEventListener('click', function () {
                const table = document.getElementById('supplier-ledger-table').outerHTML;
                const newWin = window.open('');
                newWin.document.write('<html><head><title>Print</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head><body>' + table + '</body></html>');
                newWin.document.close();
                newWin.print();
            });
        }

        const excelBtn = document.getElementById('excelBtn');
        if (excelBtn) {
            excelBtn.addEventListener('click', function () {
                const table = document.getElementById('supplier-ledger-table');
                const wb = XLSX.utils.table_to_book(table, {sheet: "Supplier Ledger"});
                XLSX.writeFile(wb, 'SupplierLedger.xlsx');
            });
        }
    });
</script>
@endpush
