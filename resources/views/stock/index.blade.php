@extends('layouts.app')

@section('title', __('stock.finished_goods_stock'))

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ __('stock.finished_goods_stock') }}</h1>
        </div>

        <div class="mb-4">
            <a href="{{ route('sales.create') }}" class="btn btn-primary">
                {{ __('stock.create_sale') }}
            </a>
            <a href="{{ route('sales.index') }}" class="btn btn-secondary">
                {{ __('stock.view_sales') }}
            </a>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>{{ __('stock.cast_item_name') }}</th>
                                <th>{{ __('stock.casted_metal_kg') }}</th>
                                <th>{{ __('stock.billet_size_inch') }}</th>
                                <th>{{ __('stock.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stocks as $stock)
                                <tr>
                                    <td>{{ $stock->item_name }}</td>
                                    <td>{{ $stock->total_weight }}</td>
                                    <td>{{ $stock->billet_size_inch }}</td>
                                    <td>
                                        <a href="{{ route('sales.create', ['cast_item_name' => $stock->item_name]) }}" class="btn btn-success">
                                            {{ __('stock.sale') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection 