@extends('layouts.app')

@section('title', __('messages.scrap_purchase_list'))

@section('content')
<div class="container-fluid">

    <div class="card mt-5">
        <div class="card-header">
            <h3><i class="fas fa-list"></i> {{ __('messages.scrap_purchase_list') }}</h3>
        </div>
        <div class="card-body">
            <!-- 🔍 Filter Form -->
            <form action="{{ route('purchase.scrap_purchase_list') }}" method="get" id="filter-form">
                <div class="row mb-3">
                    <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                        <select name="filter" class="form-select" id="filter-select" onchange="this.form.submit()">
                            <option value="">{{ __('messages.all') }}</option>
                            <option value="daily" {{ request('filter') == 'daily' ? 'selected' : '' }}>{{ __('messages.daily') }}</option>
                            <option value="weekly" {{ request('filter') == 'weekly' ? 'selected' : '' }}>{{ __('messages.weekly') }}</option>
                            <option value="monthly" {{ request('filter') == 'monthly' ? 'selected' : '' }}>{{ __('messages.monthly') }}</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="{{ __('messages.search') }}" value="{{ request('search') }}">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- 📤 Export Buttons -->
            <div class="d-flex justify-content-end mb-3">
                <button id="printBtn" class="btn btn-secondary me-2"><i class="fas fa-print"></i> {{ __('messages.print') }}</button>
                <button id="excelBtn" class="btn btn-success me-2"><i class="fas fa-file-excel"></i> {{ __('messages.excel') }}</button>
                <button id="pdfBtn" class="btn btn-danger me-2"><i class="fas fa-file-pdf"></i> {{ __('messages.pdf') }}</button>
                <a href="{{ route('supplier.ledger') }}" class="btn btn-primary">
                    <i class="fas fa-book"></i> {{ __('messages.supplier_ledger') }}
                </a>
            </div>

            <!-- 📊 Scrap Purchases Table -->
            <div class="table-responsive">
                <table id="scrap-purchase-table" class="table table-striped table-bordered">
                    <thead class="table-dark">
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
                        @forelse ($scrap_purchases as $purchase)
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
                                    <a href="{{ route('purchase.edit_scrap', $purchase) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('purchase.delete_scrap', $purchase) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('messages.are_you_sure_you_want_to_delete_this_item') }}');">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="15" class="text-center">{{ __('messages.no_records_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $scrap_purchases->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.onload = function () {
        const table = document.getElementById('scrap-purchase-table');

        // 🖨 Print
        document.getElementById('printBtn').onclick = function () {
            const newWin = window.open('');
            newWin.document.write(`
                <html>
                    <head>
                        <title>Print</title>
                        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                    </head>
                    <body>
                        ${table.outerHTML}
                    </body>
                </html>
            `);
            newWin.document.close();
            newWin.print();
        };

        // 📊 Excel
        document.getElementById('excelBtn').onclick = function () {
            const wb = XLSX.utils.table_to_book(table, { sheet: "Scrap Purchases" });
            XLSX.writeFile(wb, 'ScrapPurchases.xlsx');
        };

        // 📄 PDF
        document.getElementById('pdfBtn').onclick = function () {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({ orientation: "landscape" });

            doc.autoTable({
                html: '#scrap-purchase-table',
                theme: 'grid',
                headStyles: { fillColor: [52, 58, 64] }
            });

            doc.save('ScrapPurchases.pdf');
        };
    };
</script>
@endpush
