@extends('layouts.app')

@section('title', __('sales.edit_sale'))

@section('content')
<style>
    @media (max-width: 767.98px) {
        .form-group {
            margin-bottom: 15px; /* Add some vertical spacing between stacked form groups */
        }
        .d-sm-flex {
            flex-direction: column;
            align-items: flex-start !important;
        }
        .d-sm-flex h1 {
            margin-bottom: 10px;
        }
    }
</style>
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ __('sales.edit_sale') }}</h1>
            <a href="{{ route('sales.index') }}" class="btn btn-primary">{{ __('sales.back') }}</a>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="{{ route('sales.update', $sale->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sale_date">{{ __('sales.sale_date') }}</label>
                                <input type="date" name="sale_date" class="form-control" id="sale_date"
                                    value="{{ old('sale_date', $sale->sale_date) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cc_plant_id">{{ __('sales.cc_plant') }}</label>
                                <select name="cc_plant_id" id="cc_plant_id" class="form-control" required>
                                    <option value="">{{ __('sales.select_cc_plant') }}</option>
                                    @foreach ($ccPlants as $ccPlant)
                                        <option value="{{ $ccPlant->id }}" {{ $sale->cc_plant_id == $ccPlant->id ? 'selected' : '' }}>
                                            {{ $ccPlant->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="customer_id">{{ __('sales.customer') }}</label>
                                <select name="customer_id" id="customer_id" class="form-control">
                                    <option value="">{{ __('sales.select_customer') }}</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ $sale->customer_id == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="item_name">{{ __('sales.item_name') }}</label>
                                <input type="text" name="item_name" class="form-control" id="item_name"
                                    value="{{ $sale->item_name }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="billet_size">{{ __('sales.billet_size') }}</label>
                                <input type="text" name="billet_size" class="form-control" id="billet_size"
                                    value="{{ $sale->billet_size }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="weight">{{ __('sales.weight_to_sell_kg') }}</label>
                                <input type="number" name="weight" class="form-control" id="weight" step="0.01"
                                    value="{{ old('weight', $sale->total_weight) }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="rate">{{ __('sales.rate_per_kg') }}</label>
                                <input type="number" name="rate" class="form-control" id="rate" step="0.01"
                                    value="{{ old('rate', $sale->rate) }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="discount">{{ __('sales.discount') }}</label>
                                <input type="number" name="discount" class="form-control" id="discount"
                                    value="{{ old('discount', $sale->discount) }}" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="sub_total">{{ __('sales.sub_total') }}</label>
                                <input type="number" name="sub_total" class="form-control" id="sub_total"
                                    value="{{ old('sub_total', $sale->sub_total) }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="paid_amount">{{ __('sales.paid_amount') }}</label>
                                <input type="number" name="paid_amount" class="form-control" id="paid_amount"
                                    value="{{ old('paid_amount', $sale->paid_amount) }}" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="remaining_amount">{{ __('sales.remaining_amount') }}</label>
                                <input type="number" name="remaining_amount" class="form-control" id="remaining_amount"
                                    value="{{ old('remaining_amount', $sale->remaining_amount) }}" readonly>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">{{ __('sales.update_sale') }}</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function(){
        // Calculate sub total and remaining
        function calculateAmounts() {
            var weight = parseFloat($('#weight').val()) || 0;
            var rate = parseFloat($('#rate').val()) || 0;
            var discount = parseFloat($('#discount').val()) || 0;
            var paid = parseFloat($('#paid_amount').val()) || 0;

            var sub_total = (weight * rate) - discount;
            $('#sub_total').val(sub_total.toFixed(2));

            var remaining = sub_total - paid;
            $('#remaining_amount').val(remaining.toFixed(2));
        }

        $('#weight, #rate, #discount, #paid_amount').on('input', calculateAmounts);
    });
</script>
@endpush
