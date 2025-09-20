@extends('layouts.app')

@section('title', __('messages.edit_scrap_purchase'))

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-edit"></i> {{ __('messages.edit_scrap_purchase') }}</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('purchase.update_scrap', $purchase) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">

                    <!-- Supplier Name -->
                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                        <label for="supplier_name" class="form-label">{{ __('messages.supplier_name') }}</label>
                        <input type="text" class="form-control" id="supplier_name" name="supplier_name" value="{{ old('supplier_name', $purchase->supplier->name) }}" required>
                    </div>


                    <!-- Scrap -->
                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                        <label for="scrap_name" class="form-label">{{ __('messages.scrap') }}</label>
                        <input type="text" class="form-control" id="scrap_name" name="scrap_name" value="{{ old('scrap_name', $purchase->scrap_name) }}" required>
                    </div>
                </div>

                <div class="row">
                    <!-- Weight -->
                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                        <label for="weight" class="form-label">{{ __('messages.weight_kg') }}</label>
                        <input type="number" class="form-control" id="weight" name="weight" value="{{ old('weight', $purchase->weight) }}" required>
                    </div>

                    <!-- Amount per kg -->
                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                        <label for="amount_per_kg" class="form-label">{{ __('messages.amount_per_kg') }}</label>
                        <input type="number" class="form-control" id="amount_per_kg" name="amount_per_kg" value="{{ old('amount_per_kg', $purchase->amount_per_kg) }}" required>
                    </div>
                </div>

                <div class="row">
                    <!-- Waste Percentage -->
                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                        <label for="waste_percentage" class="form-label">{{ __('messages.waste_percentage') }}</label>
                        <input type="number" class="form-control" id="waste_percentage" name="waste_percentage" value="{{ old('waste_percentage', $purchase->waste_percentage) }}" required>
                    </div>

                    <!-- Weight without waste -->
                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                        <label for="weight_without_waste" class="form-label">{{ __('messages.weight_with_waste_kg') }}</label>
                        <input type="text" class="form-control" id="weight_without_waste" name="weight_without_waste" value="{{ old('weight_without_waste', $purchase->weight_without_waste) }}" readonly>
                    </div>
                </div>

                <div class="row">
                    <!-- Total Amount -->
                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                        <label for="total_amount" class="form-label">{{ __('messages.total_amount') }}</label>
                        <input type="text" class="form-control" id="total_amount" name="total_amount" value="{{ old('total_amount', $purchase->total_amount) }}" readonly>
                    </div>

                    <!-- Waste Amount -->
                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                        <label for="total_amount_with_waste" class="form-label">{{ __('messages.waste_amount') }}</label>
                        <input type="text" class="form-control" id="total_amount_with_waste" name="total_amount_with_waste" value="{{ old('total_amount_with_waste', $purchase->total_amount_with_waste) }}" readonly>
                    </div>
                </div>

                <div class="row">
                    <!-- Grand Total -->
                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                        <label for="grand_total" class="form-label">{{ __('messages.grand_total') }}</label>
                        <input type="text" class="form-control" id="grand_total" name="grand_total" value="{{ old('grand_total', $purchase->grand_total) }}" readonly>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">{{ __('messages.update_scrap') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <!-- Custom JS for auto-calculation -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const weightInput = document.getElementById('weight');
            const amountPerKgInput = document.getElementById('amount_per_kg');
            const wastePercentageInput = document.getElementById('waste_percentage');
            const weightWithoutWasteInput = document.getElementById('weight_without_waste');
            const totalAmountInput = document.getElementById('total_amount');
            const totalAmountWithWasteInput = document.getElementById('total_amount_with_waste');
            const grandTotalInput = document.getElementById('grand_total');

            function calculate() {
                const weight = parseFloat(weightInput.value) || 0;
                const amountPerKg = parseFloat(amountPerKgInput.value) || 0;
                const wastePercentage = parseFloat(wastePercentageInput.value) || 0;

                const weightWithoutWaste = weight * (1 - wastePercentage / 100);
                const totalAmount = weight * amountPerKg;
                const wasteAmount = (weight * (wastePercentage / 100)) * amountPerKg;
                const grandTotal = (weight * amountPerKg) + wasteAmount;

                weightWithoutWasteInput.value = weightWithoutWaste.toFixed(2);
                totalAmountInput.value = totalAmount.toFixed(2);
                totalAmountWithWasteInput.value = wasteAmount.toFixed(2);
                grandTotalInput.value = grandTotal.toFixed(2);
            }

            weightInput.addEventListener('input', calculate);
            amountPerKgInput.addEventListener('input', calculate);
            wastePercentageInput.addEventListener('input', calculate);
        });
    </script>
@endsection
