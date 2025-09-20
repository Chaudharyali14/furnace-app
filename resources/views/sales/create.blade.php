@extends('layouts.app')

@section('title', __('sales.create_sale'))

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
            <h1 class="h3 mb-0 text-gray-800">{{ __('sales.create_sale') }}</h1>
            <a href="{{ route('sales.index') }}" class="btn btn-primary">{{ __('sales.back') }}</a>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="{{ route('sales.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sale_date">{{ __('sales.sale_date') }}</label>
                                <input type="date" name="sale_date" class="form-control" id="sale_date"
                                    value="{{ old('sale_date', date('Y-m-d')) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cc_plant_id">{{ __('sales.cc_plant') }}</label>
                                <select name="cc_plant_id" id="cc_plant_id" class="form-control" required>
                                    <option value="">{{ __('sales.select_cc_plant') }}</option>
                                    @foreach ($ccPlants as $ccPlant)
                                        <option value="{{ $ccPlant->id }}">{{ $ccPlant->name }}</option>
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
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#addCustomerModal">{{ __('sales.add_new_customer') }}</a>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="stock_id">{{ __('sales.item') }}</label>
                                <select name="stock_id" id="stock_id" class="form-control" required>
                                    <option value="">{{ __('sales.select_item') }}</option>
                                    @foreach ($stocks as $stock)
                                        <option value="{{ $stock->item_name }}_{{ $stock->billet_size_inch }}"
                                            data-available-weight="{{ $stock->total_weight }}">
                                            {{ $stock->item_name }} - {{ $stock->billet_size_inch }}"
                                            ({{ __('sales.available') }}: {{ $stock->total_weight }} kg)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="weight">{{ __('sales.weight_to_sell_kg') }}</label>
                                <input type="number" name="weight" class="form-control" id="weight" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="rate">{{ __('sales.rate_per_kg') }}</label>
                                <input type="number" name="rate" class="form-control" id="rate" step="0.01" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="discount">{{ __('sales.discount') }}</label>
                                <input type="number" name="discount" class="form-control" id="discount" value="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="sub_total">{{ __('sales.sub_total') }}</label>
                                <input type="number" name="sub_total" class="form-control" id="sub_total" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="paid_amount">{{ __('sales.paid_amount') }}</label>
                                <input type="number" name="paid_amount" class="form-control" id="paid_amount" value="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="remaining_amount">{{ __('sales.remaining_amount') }}</label>
                                <input type="number" name="remaining_amount" class="form-control" id="remaining_amount" readonly>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">{{ __('sales.create_sale') }}</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addCustomerModalLabel">{{ __('sales.add_new_customer_modal_title') }}</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="addCustomerForm">
            @csrf
            <div class="form-group">
                <label for="customer_name">{{ __('sales.customer_name') }}</label>
                <input type="text" class="form-control" id="customer_name" name="name" required>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('sales.close') }}</button>
        <button type="button" class="btn btn-primary" id="saveCustomer">{{ __('sales.save_customer') }}</button>
      </div>
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

        // Add new customer
        $('#saveCustomer').on('click', function(){
            var customerName = $('#customer_name').val();
            if(customerName.trim() === '') {
                alert("{{ __('sales.customer_name_cannot_be_empty') }}");
                return;
            }

            $.ajax({
                url: "{{ route('customers.store') }}",
                method: 'POST',
                data: $('#addCustomerForm').serialize(),
                success: function(response){
                    if(response.success){
                        var newCustomer = response.customer;
                        $('#customer_id').append('<option value="' + newCustomer.id + '" selected>' + newCustomer.name + '</option>');
                        $('#addCustomerModal').modal('hide');
                        $('#addCustomerForm')[0].reset();
                    } else {
                        alert("{{ __('sales.error_adding_customer') }}");
                    }
                }
            });
        });

        // Check available stock
        $('#stock_id').on('change', function(){
            var selectedOption = $(this).find(':selected');
            var availableWeight = parseFloat(selectedOption.data('available-weight'));
            var sellingWeight = parseFloat($('#weight').val()) || 0;

            if(sellingWeight > availableWeight) {
                alert("{{ __('sales.cannot_sell_more_than_available_stock') }}" + availableWeight + ' kg');
                $('#weight').val(availableWeight);
                calculateAmounts();
            }
        });

        $('#weight').on('input', function(){
            var selectedOption = $('#stock_id').find(':selected');
            var availableWeight = parseFloat(selectedOption.data('available-weight'));
            var sellingWeight = parseFloat($(this).val()) || 0;

            if(sellingWeight > availableWeight) {
                alert("{{ __('sales.cannot_sell_more_than_available_stock') }}" + availableWeight + ' kg');
                $(this).val(availableWeight);
                calculateAmounts();
            }
        });
    });
</script>
@endpush
