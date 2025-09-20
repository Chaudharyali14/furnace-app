<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('sales.print_sales_table') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="card p-4">
            <h1 class="mb-4 text-center">{{ __('sales.sales_overview') }}</h1>

            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="sales-table">
                    <thead class="table-dark">
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
                        <tr class="table-dark">
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
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>