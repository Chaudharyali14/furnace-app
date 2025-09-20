@extends('layouts.app')

@section('title', __('messages.edit_raw_material_stock'))

@section('content')
<div class="container">
    <div class="card p-4">
        <h1 class="mb-4">{{ __('messages.edit_raw_material_stock') }}</h1>

        <form action="{{ route('furnace.update_stock', $stock) }}" method="post">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="raw_material_name" class="form-label">{{ __('messages.raw_material_name') }}</label>
                    <input type="text" class="form-control" id="raw_material_name" name="raw_material_name" value="{{ old('raw_material_name', $stock->raw_material_name) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="total_purchased_qty" class="form-label">{{ __('messages.total_purchased_quantity') }}</label>
                    <input type="number" class="form-control" id="total_purchased_qty" name="total_purchased_qty" value="{{ old('total_purchased_qty', $stock->total_purchased_qty) }}" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="total_issued_qty" class="form-label">{{ __('messages.total_issued_quantity') }}</label>
                    <input type="number" class="form-control" id="total_issued_qty" name="total_issued_qty" value="{{ old('total_issued_qty', $stock->total_issued_qty) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="remaining_stock_qty" class="form-label">{{ __('messages.remaining_stock_quantity') }}</label>
                    <input type="number" class="form-control" id="remaining_stock_qty" name="remaining_stock_qty" value="{{ old('remaining_stock_qty', $stock->remaining_stock_qty) }}" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">{{ __('messages.update_stock') }}</button>
            <a href="{{ route('furnace.raw_material_stock') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
        </form>

    </div>
</div>
@endsection
