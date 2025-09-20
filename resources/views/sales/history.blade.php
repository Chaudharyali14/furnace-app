@extends('layouts.app')

@section('title', __('sales.deleted_sales'))

@section('content')
<style>
    @media (max-width: 767.98px) {
        .table-responsive table td form {
            display: block !important;
            margin-bottom: 5px;
        }
        .table-responsive table td form .btn {
            width: 100%;
        }
        .card-header h1 {
            font-size: 1.5rem;
        }
    }
</style>
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white">
                <h1 class="mb-0">{{ __('sales.deleted_sales_history') }}</h1>
            </div>
            <div class="card-body">
                @if($sales->isEmpty())
                    <div class="alert alert-info" role="alert">
                        {{ __('sales.no_deleted_sales_found') }}
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('sales.id') }}</th>
                                    <th>{{ __('sales.sale_date') }}</th>
                                    <th>{{ __('sales.item_name') }}</th>
                                    <th>{{ __('sales.billet_size') }}</th>
                                    <th>{{ __('sales.customer') }}</th>
                                    <th>{{ __('sales.total_weight') }}</th>
                                    <th>{{ __('sales.rate') }}</th>
                                    <th>{{ __('sales.sub_total') }}</th>
                                    <th>{{ __('sales.deleted_at') }}</th>
                                    <th>{{ __('sales.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sales as $sale)
                                    <tr>
                                        <td>{{ $sale->id }}</td>
                                        <td>{{ $sale->sale_date }}</td>
                                        <td>{{ $sale->item_name }}</td>
                                        <td>{{ $sale->billet_size }}</td>
                                        <td>{{ $sale->customer->name ?? 'N/A' }}</td>
                                        <td>{{ $sale->total_weight }}</td>
                                        <td>{{ $sale->rate }}</td>
                                        <td>{{ $sale->sub_total }}</td>
                                        <td>{{ $sale->deleted_at }}</td>
                                        <td>
                                            <form action="{{ route('sales.restore', $sale->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" title="{{ __('sales.restore_sale') }}">
                                                    <i class="fas fa-undo"></i> {{ __('sales.restore') }}
                                                </button>
                                            </form>
                                            <form action="{{ route('sales.forceDelete', $sale->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('{{ __('sales.confirm_force_delete') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="{{ __('sales.force_delete_sale') }}">
                                                    <i class="fas fa-trash-alt"></i> {{ __('sales.force_delete') }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection