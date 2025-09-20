<!DOCTYPE html>
<html>
<head>
    <title>{{ __('sales.sales_overview') }}</title>
    <style>
        body {
            font-family: sans-serif;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .table th {
            background-color: #f2f2f2;
        }
        .text-center {
            text-align: center;
        }
        .text-end {
            text-align: right;
        }
        .mb-4 {
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <h1 class="mb-4 text-center">{{ __('sales.sales_overview') }}</h1>

    <table class="table">
        <thead>
            <tr>
                <th>{{ __('sales.id') }}</th>
                <th>{{ __('sales.sale_date') }}</th>
                <th>{{ __('sales.customer') }}</th>
                <th>{{ __('sales.item') }}</th>
                <th>{{ __('sales.billet_size') }}</th>
                <th>{{ __('sales.total_weight') }}</th>
                <th>{{ __('sales.rate') }}</th>
                <th>{{ __('sales.sub_total') }}</th>
                <th>{{ __('sales.discount') }}</th>
                <th>{{ __('sales.paid_amount') }}</th>
                <th>{{ __('sales.remaining_amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sales as $sale)
                <tr>
                    <td>{{ $sale->id }}</td>
                    <td>{{ $sale->sale_date }}</td>
                    <td>{{ $sale->customer->name ?? 'N/A' }}</td>
                    <td>{{ $sale->item_name ?? 'N/A' }}</td>
                    <td>{{ $sale->billet_size ?? 'N/A' }}</td>
                    <td>{{ $sale->total_weight }}</td>
                    <td>{{ $sale->rate }}</td>
                    <td>{{ $sale->sub_total }}</td>
                    <td>{{ $sale->discount }}</td>
                    <td>{{ $sale->paid_amount }}</td>
                    <td>{{ $sale->remaining_amount }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">{{ __('sales.no_sales_found') }}</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-end"><strong>{{ __('sales.total') }}</strong></td>
                <td><strong>{{ number_format($sales->sum('total_weight'), 2) }}</strong></td>
                <td></td>
                <td><strong>{{ number_format($sales->sum('sub_total'), 2) }}</strong></td>
                <td><strong>{{ number_format($sales->sum('discount'), 2) }}</strong></td>
                <td><strong>{{ number_format($sales->sum('paid_amount'), 2) }}</strong></td>
                <td><strong>{{ number_format($sales->sum('remaining_amount'), 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
