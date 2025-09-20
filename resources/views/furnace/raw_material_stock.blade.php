@extends('layouts.app')

@section('title', __('messages.raw_material_stock_overview'))

@section('styles')
<style>
    @media (max-width: 768px) {
        .table-responsive {
            display: none;
        }
        .stock-cards {
            display: block;
        }
    }
    @media (min-width: 769px) {
        .stock-cards {
            display: none;
        }
    }
    .stock-card {
        background-color: #fff;
        border: 1px solid #e3e6f0;
        border-radius: 0.35rem;
        margin-bottom: 1rem;
        padding: 1rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="card p-4">
        <h1 class="mb-4">{{ __('messages.raw_material_stock_overview') }}</h1>

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <!-- Filter and Search Form -->
        <form action="{{ route('furnace.raw_material_stock') }}" method="get" class="mb-4">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                    <select name="filter" class="form-select" onchange="this.form.submit()">
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

        <!-- Export Buttons -->
        <div class="d-flex justify-content-end mb-3">
            <button id="printBtn" class="btn btn-secondary me-2"><i class="fas fa-print"></i> {{ __('messages.print') }}</button>
            <button id="excelBtn" class="btn btn-success me-2"><i class="fas fa-file-excel"></i> {{ __('messages.excel') }}</button>
            <button id="pdfBtn" class="btn btn-danger"><i class="fas fa-file-pdf"></i> {{ __('messages.pdf') }}</button>
        </div>

        @if (!empty($raw_materials_stock))
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="raw-material-stock-table">
                    <thead class="table-dark">
                        <tr>
                            <th>{{ __('messages.raw_material') }}</th>
                            <th>{{ __('messages.total_purchased') }}</th>
                            <th>{{ __('messages.total_issued') }}</th>
                            <th>{{ __('messages.remaining_stock_net_weight') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($raw_materials_stock as $material)
                            <tr>
                                <td>{{ $material->raw_material_name }}</td>
                                <td>{{ $material->total_purchased_qty }}</td>
                                <td>{{ $material->total_issued_qty }}</td>
                                <td>{{ $material->remaining_stock_qty }}</td>
                                <td>
                                    <a href="{{ route('furnace.edit_stock', $material) }}" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('furnace.delete_stock', $material) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('messages.are_you_sure_you_want_to_delete_this_item') }}');"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-dark">
                            <td colspan="3" class="text-end"><strong>{{ __('messages.total_remaining_stock') }}:</strong></td>
                            <td><strong>{{ number_format($remaining_stock_subtotal, 2) }}</strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                {{ $raw_materials_stock->links() }}
            </div>

        @else
            <p>{{ __('messages.no_raw_materials_found_in_stock') }}</p>
        @endif
    </div>
</div>
@endsection

@push('scripts')
    <!-- jsPDF and SheetJS Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf-autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>

    <!-- Export Buttons -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Print
            document.getElementById('printBtn').addEventListener('click', function () {
                const table = document.getElementById('raw-material-stock-table').outerHTML;
                const newWin = window.open('');
                newWin.document.write('<html><head><title>Print</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head><body>' + table + '</body></html>');
                newWin.document.close();
                newWin.print();
            });

            // Excel
            document.getElementById('excelBtn').addEventListener('click', function () {
                const table = document.getElementById('raw-material-stock-table');
                const wb = XLSX.utils.table_to_book(table, {sheet: "Raw Material Stock"});
                XLSX.writeFile(wb, 'RawMaterialStock.xlsx');
            });

            // PDF
            document.getElementById('pdfBtn').addEventListener('click', function () {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();
                doc.autoTable({ html: '#raw-material-stock-table' });
                doc.save('RawMaterialStock.pdf');
            });
        });
    </script>
@endpush