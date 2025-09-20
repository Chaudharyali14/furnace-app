@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h2>{{ __('stock.sales_list') }}</h2>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('stock.item_name') }}</th>
                                    <th>{{ __('stock.quantity') }}</th>
                                    <th>{{ __('stock.sale_date') }}</th>
                                    <th>{{ __('stock.supplier') }}</th>
                                    <th>{{ __('stock.total_amount') }}</th>
                                    <th>{{ __('stock.paid_amount') }}</th>
                                    <th>{{ __('stock.remaining_amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sales as $sale)
                                    <tr>
                                        <td>{{ $sale->stock->cast_item_name }}</td>
                                        <td>{{ $sale->quantity }}</td>
                                        <td>{{ $sale->sale_date }}</td>
                                        <td>{{ $sale->supplier->name }}</td>
                                        <td>{{ $sale->sub_total }}</td>
                                        <td>{{ $sale->paid_amount }}</td>
                                        <td>{{ $sale->remaining_amount }}</td>
                                    </tr>
                                    @if ($sale->payments->count() > 0)
                                        <tr>
                                            <td colspan="7">
                                                <h5>{{ __('stock.payment_history') }}</h5>
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('stock.payment_date') }}</th>
                                                            <th>{{ __('stock.amount') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($sale->payments as $payment)
                                                            <tr>
                                                                <td>{{ $payment->payment_date }}</td>
                                                                <td>{{ $payment->amount }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>

                        <div class="d-flex justify-content-center">
                            {{ $sales->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
