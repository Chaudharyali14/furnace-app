@extends('layouts.app')

@section('title', __('ccplant.cc_plant'))

@section('content')
<div class="container">

    {{-- ✅ Flash Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ✅ Add Heat Form --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-industry"></i> {{ __('ccplant.cc_plant') }}</h3>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <form action="{{ route('ccplant.add') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <label class="form-label fw-bold">{{ __('ccplant.heat_no') }}</label>
                        <input type="text" class="form-control" name="heat_no">
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <label class="form-label fw-bold">{{ __('ccplant.total_metal_kg') }}</label>
                        <input type="text" class="form-control" name="total_metal">
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <label class="form-label fw-bold">{{ __('ccplant.casted_metal_kg') }}</label>
                        <input type="text" class="form-control" name="casted_metal">
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <label class="form-label fw-bold">{{ __('ccplant.billet_size_inch') }}</label>
                        <input type="text" class="form-control" name="billet_size_inch">
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <label class="form-label fw-bold">{{ __('ccplant.cast_item_name') }}</label>
                        <input type="text" class="form-control" name="cast_item_name">
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <label class="form-label fw-bold">{{ __('ccplant.heat_date') }}</label>
                        <input type="date" class="form-control" name="date">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">{{ __('ccplant.add_heat') }}</button>
            </form>
        </div>
    </div>

    {{-- ✅ Filter Form --}}
    <div class="card mt-5">
        <div class="card-header">
            <h3><i class="fas fa-filter"></i> {{ __('ccplant.filter_heats') }}</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('ccplant.index') }}" method="get">
                <div class="row">
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <select name="filter" class="form-select" onchange="this.form.submit()">
                            <option value="">{{ __('messages.all') }}</option>
                            <option value="daily" {{ request('filter') == 'daily' ? 'selected' : '' }}>{{ __('messages.daily') }}</option>
                            <option value="weekly" {{ request('filter') == 'weekly' ? 'selected' : '' }}>{{ __('messages.weekly') }}</option>
                            <option value="monthly" {{ request('filter') == 'monthly' ? 'selected' : '' }}>{{ __('messages.monthly') }}</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="{{ __('messages.search') }}" value="{{ request('search') }}">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ✅ Heats History --}}
    <div class="card mt-5">
        <div class="card-header">
            <h3><i class="fas fa-list"></i> {{ __('ccplant.heats_history') }}</h3>
        </div>
        <div class="card-body">

            <div class="d-flex justify-content-end mb-3">
                <button id="printBtn" class="btn btn-secondary me-2"><i class="fas fa-print"></i> {{ __('ccplant.print') }}</button>
                <button id="excelBtn" class="btn btn-success me-2"><i class="fas fa-file-excel"></i> {{ __('ccplant.excel') }}</button>
                <button id="pdfBtn" class="btn btn-danger"><i class="fas fa-file-pdf"></i> {{ __('ccplant.pdf') }}</button>
            </div>
            <div class="table-responsive">
            <table class="table table-bordered table-striped" id="heatsTable">
                <thead>
                    <tr>
                        <th>{{ __('ccplant.heat_date') }}</th>
                        <th>{{ __('ccplant.heat_no') }}</th>
                        <th>{{ __('ccplant.total_metal_kg') }}</th>
                        <th>{{ __('ccplant.casted_metal_kg') }}</th>
                        <th>{{ __('ccplant.uncast_metal_kg') }}</th>
                        <th>{{ __('ccplant.billet_size_inch') }}</th>
                        <th>{{ __('ccplant.cast_item_name') }}</th>
                        <th>{{ __('ccplant.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($heats as $heat)
                        <tr>
                            <td>{{ $heat->date }}</td>
                            <td>{{ $heat->heat_no }}</td>
                            <td>{{ $heat->total_metal }}</td>
                            <td>{{ $heat->casted_metal }}</td>
                            <td>{{ $heat->uncast_metal }}</td>
                            <td>{{ $heat->billet_size_inch }}</td>
                            <td>{{ $heat->cast_item_name }}</td>
                            <td>
                                <a href="{{ route('ccplant.edit', $heat) }}" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('ccplant.delete', $heat) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('ccplant.are_you_sure') }}');"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center">{{ __('ccplant.no_records_found') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf-autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ✅ Print
    document.getElementById('printBtn').addEventListener('click', function () {
        const table = document.getElementById('heatsTable').outerHTML;
        const newWin = window.open('');
        newWin.document.write('<html><head><title>Print</title>' +
            '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">' +
            '</head><body>' + table + '</body></html>');
        newWin.document.close();
        newWin.print();
    });

    // ✅ Excel
    document.getElementById('excelBtn').addEventListener('click', function () {
        const table = document.getElementById('heatsTable');
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.table_to_sheet(table, { raw: true });
        XLSX.utils.book_append_sheet(wb, ws, "{{ __('ccplant.heats_history_sheet') }}");
        XLSX.writeFile(wb, "{{ __('ccplant.heats_history_excel') }}");
    });

    // ✅ PDF
    document.getElementById('pdfBtn').addEventListener('click', function () {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        doc.autoTable({ html: '#heatsTable' });
        doc.save("{{ __('ccplant.heats_history_pdf') }}");
    });
});
</script>
@endpush