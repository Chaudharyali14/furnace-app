@extends('layouts.app')

@section('title', __('messages.purchase_scrap_entry'))

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-shopping-cart"></i> {{ __('messages.purchase_scrap_entry') }}</h3>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form id="scrapForm" action="{{ route('purchase.add_scrap') }}" method="POST">
                @csrf
                <div class="row">
                    <!-- Supplier Name -->
                    <div class="col-lg-6 col-md-6 col-12 mb-3">
                        <label for="supplier_name" class="form-label">{{ __('messages.supplier_name') }}</label>
                        <input type="text" class="form-control" id="supplier_name" name="supplier_name" required>
                    </div>

                    <!-- Purchase Date -->
                    <div class="col-lg-6 col-md-6 col-12 mb-3">
                        <label for="purchase_date" class="form-label">{{ __('messages.purchase_date') }}</label>
                        <input type="datetime-local" class="form-control" id="purchase_date" name="purchase_date" required>
                    </div>
                </div>

                <div class="row">
                    <!-- Scrap -->
                    <div class="col-lg-6 col-md-6 col-12 mb-3">
                        <label for="scrap_name" class="form-label">{{ __('messages.scrap') }}</label>
                        <input type="text" class="form-control" id="scrap_name" name="scrap_name" required>
                    </div>

                    <!-- Weight -->
                    <div class="col-lg-6 col-md-6 col-12 mb-3">
                        <label for="weight" class="form-label">{{ __('messages.weight_kg') }}</label>
                        <input type="number" class="form-control" id="weight" name="weight" step="any" required>
                    </div>
                </div>

                <div class="row">
                    <!-- Amount per kg -->
                    <div class="col-lg-6 col-md-6 col-12 mb-3">
                        <label for="amount_per_kg" class="form-label">{{ __('messages.amount_per_kg') }}</label>
                        <input type="number" class="form-control" id="amount_per_kg" name="amount_per_kg" step="any" required>
                    </div>

                    <!-- Waste Percentage -->
                    <div class="col-lg-6 col-md-6 col-12 mb-3">
                        <label for="waste_percentage" class="form-label">{{ __('messages.waste_percentage') }}</label>
                        <input type="number" class="form-control" id="waste_percentage" name="waste_percentage" step="any" required>
                    </div>
                </div>

                <div class="row">
                    <!-- Waste in kg -->
                    <div class="col-lg-6 col-md-6 col-12 mb-3">
                        <label for="waste_in_kg" class="form-label">{{ __('messages.waste_in_kg') }}</label>
                        <input type="text" class="form-control" id="waste_in_kg" name="waste_in_kg" readonly>
                    </div>

                    <!-- Net Weight -->
                    <div class="col-lg-6 col-md-6 col-12 mb-3">
                        <label for="net_weight" class="form-label">{{ __('messages.net_weight_kg') }}</label>
                        <input type="text" class="form-control" id="net_weight" name="net_weight" readonly>
                    </div>
                </div>

                <div class="row">
                    <!-- Total Amount -->
                    <div class="col-lg-6 col-md-6 col-12 mb-3">
                        <label for="total_amount" class="form-label">{{ __('messages.total_amount') }}</label>
                        <input type="text" class="form-control" id="total_amount" name="total_amount" readonly>
                    </div>

                    <!-- Waste Amount -->
                    <div class="col-lg-6 col-md-6 col-12 mb-3">
                        <label for="waste_amount" class="form-label">{{ __('messages.waste_amount') }}</label>
                        <input type="text" class="form-control" id="waste_amount" name="waste_amount" readonly>
                    </div>
                </div>

                <div class="row">
                    <!-- Grand Total -->
                    <div class="col-lg-6 col-md-6 col-12 mb-3">
                        <label for="grand_total" class="form-label">{{ __('messages.grand_total') }}</label>
                        <input type="text" class="form-control" id="grand_total" name="grand_total" readonly>
                    </div>

                    <!-- Discount -->
                    <div class="col-lg-6 col-md-6 col-12 mb-3">
                        <label for="discount" class="form-label">{{ __('messages.discount') }}</label>
                        <input type="number" class="form-control" id="discount" name="discount" step="any">
                    </div>
                </div>

                <div class="row">
                    <!-- Paid Amount -->
                    <div class="col-lg-6 col-md-6 col-12 mb-3">
                        <label for="paid_amount" class="form-label">{{ __('messages.paid_amount') }}</label>
                        <input type="number" class="form-control" id="paid_amount" name="paid_amount" step="any">
                    </div>

                    <!-- Remaining Amount -->
                    <div class="col-lg-6 col-md-6 col-12 mb-3">
                        <label for="remaining_amount" class="form-label">{{ __('messages.remaining_amount') }}</label>
                        <input type="text" class="form-control" id="remaining_amount" name="remaining_amount" readonly>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">{{ __('messages.add_scrap') }}</button>
            </form>
        </div>
    </div>

    <!-- Scrap Purchase List -->
    <div class="card mt-5">
        <div class="card-header">
            <h3><i class="fas fa-list"></i> {{ __('messages.scrap_purchase_list') }}</h3>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('supplier.ledger') }}" class="btn btn-primary me-2"><i class="fas fa-book"></i> {{ __('messages.supplier_ledger') }}</a>
                <button id="printBtn" class="btn btn-secondary me-2"><i class="fas fa-print"></i> {{ __('messages.print') }}</button>
                <button id="excelBtn" class="btn btn-success me-2"><i class="fas fa-file-excel"></i> {{ __('messages.excel') }}</button>
                <button id="pdfBtn" class="btn btn-danger"><i class="fas fa-file-pdf"></i> {{ __('messages.pdf') }}</button>
            </div>

            <div class="table-responsive">
                <table class="table table-striped" id="scrap-purchase-table">
                    <thead>
                        <tr>
                            <th>{{ __('messages.supplier') }}</th>
                            <th>{{ __('messages.purchase_date') }}</th>
                            <th>{{ __('messages.scrap') }}</th>
                            <th>{{ __('messages.weight_kg') }}</th>
                            <th>{{ __('messages.amount_per_kg') }}</th>
                            <th>{{ __('messages.waste_percentage') }}</th>
                            <th>{{ __('messages.waste_in_kg') }}</th>
                            <th>{{ __('messages.net_weight') }}</th>
                            <th>{{ __('messages.total_amount') }}</th>
                            <th>{{ __('messages.waste_amount') }}</th>
                            <th>{{ __('messages.grand_total') }}</th>
                            <th>{{ __('messages.discount') }}</th>
                            <th>{{ __('messages.paid_amount') }}</th>
                            <th>{{ __('messages.remaining_amount') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($scrap_purchases as $purchase)
                            <tr>
                                <td>{{ $purchase->supplier->name }}</td>
                                <td>{{ $purchase->purchase_date }}</td>
                                <td>{{ $purchase->scrap_name }}</td>
                                <td>{{ $purchase->weight }}</td>
                                <td>{{ $purchase->amount_per_kg }}</td>
                                <td>{{ $purchase->waste_percentage }}%</td>
                                <td>{{ $purchase->waste_in_kg }}</td>
                                <td>{{ $purchase->weight_without_waste }}</td>
                                <td>{{ $purchase->total_amount }}</td>
                                <td>{{ $purchase->waste_amount }}</td>
                                <td>{{ number_format($purchase->grand_total, 2) }}</td>
                                <td>{{ $purchase->discount }}</td>
                                <td>{{ $purchase->paid_amount }}</td>
                                <td>{{ $purchase->remaining_amount }}</td>
                                <td>
                                    <a href="{{ route('purchase.edit_scrap', $purchase) }}" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('purchase.delete_scrap', $purchase) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('messages.are_you_sure_you_want_to_delete_this_item') }}');"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $scrap_purchases->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf-autotable.min.js"></script>

<script>
function initScrapPurchaseForm() {
    const weightInput = document.getElementById('weight');
    const amountPerKgInput = document.getElementById('amount_per_kg');
    const wastePercentageInput = document.getElementById('waste_percentage');
    const wasteInKgInput = document.getElementById('waste_in_kg');
    const netWeightInput = document.getElementById('net_weight');
    const totalAmountInput = document.getElementById('total_amount');
    const wasteAmountInput = document.getElementById('waste_amount');
    const grandTotalInput = document.getElementById('grand_total');
    const discountInput = document.getElementById('discount');
    const paidAmountInput = document.getElementById('paid_amount');
    const remainingAmountInput = document.getElementById('remaining_amount');

    function calculate() {
        const weight = parseFloat(weightInput.value) || 0;
        const amountPerKg = parseFloat(amountPerKgInput.value) || 0;
        const wastePercentage = parseFloat(wastePercentageInput.value) || 0;
        const discount = parseFloat(discountInput.value) || 0;
        const paidAmount = parseFloat(paidAmountInput.value) || 0;

        const wasteInKg = weight * (wastePercentage / 100);
        const netWeight = weight - wasteInKg;
        const totalAmount = weight * amountPerKg;
        const wasteAmount = wasteInKg * amountPerKg;
        const grandTotal = totalAmount + wasteAmount;
        const remainingAmount = totalAmount - paidAmount - discount;

        wasteInKgInput.value = wasteInKg.toFixed(2);
        netWeightInput.value = netWeight.toFixed(2);
        totalAmountInput.value = totalAmount.toFixed(2);
        wasteAmountInput.value = wasteAmount.toFixed(2);
        grandTotalInput.value = grandTotal.toFixed(2);
        remainingAmountInput.value = remainingAmount.toFixed(2);
    }

    [weightInput, amountPerKgInput, wastePercentageInput, discountInput, paidAmountInput]
        .forEach(input => input && input.addEventListener('input', calculate));

    // Print
    document.getElementById('printBtn').addEventListener('click', function () {
        const table = document.getElementById('scrap-purchase-table').outerHTML;
        const newWin = window.open('');
        newWin.document.write('<html><head><title>Print</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head><body>' + table + '</body></html>');
        newWin.document.close();
        newWin.print();
    });

    // Excel
    document.getElementById('excelBtn').addEventListener('click', function () {
        const table = document.getElementById('scrap-purchase-table');
        const wb = XLSX.utils.table_to_book(table, {sheet: "Scrap Purchases"});
        XLSX.writeFile(wb, 'ScrapPurchases.xlsx');
    });

    // PDF
    document.getElementById('pdfBtn').addEventListener('click', function () {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        doc.autoTable({ html: '#scrap-purchase-table' });
        doc.save('ScrapPurchases.pdf');
    });
}

document.addEventListener('DOMContentLoaded', initScrapPurchaseForm);
</script>
@endpush
