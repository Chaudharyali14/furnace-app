@extends('layouts.app')

@section('title', __('ccplant.edit_cc_plant_heat'))

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3>{{ __('ccplant.edit_cc_plant_heat') }} No: {{ $heat->heat_no }}</h3>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('ccplant.update', $heat) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="mb-3">
                            <label for="heat_no" class="form-label">{{ __('ccplant.heat_no') }}</label>
                            <input type="text" class="form-control" id="heat_no" name="heat_no" value="{{ old('heat_no', $heat->heat_no) }}">
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="mb-3">
                            <label for="total_metal" class="form-label">{{ __('ccplant.total_metal_kg') }}</label>
                            <input type="text" class="form-control" id="total_metal" name="total_metal" value="{{ old('total_metal', $heat->total_metal) }}">
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="mb-3">
                            <label for="casted_metal" class="form-label">{{ __('ccplant.casted_metal_kg') }}</label>
                            <input type="text" class="form-control" id="casted_metal" name="casted_metal" value="{{ old('casted_metal', $heat->casted_metal) }}">
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="mb-3">
                            <label for="billet_size_inch" class="form-label">{{ __('ccplant.billet_size_inch') }}</label>
                            <input type="text" class="form-control" id="billet_size_inch" name="billet_size_inch" value="{{ old('billet_size_inch', $heat->billet_size_inch) }}">
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="mb-3">
                            <label for="cast_item_name" class="form-label">{{ __('ccplant.cast_item_name') }}</label>
                            <input type="text" class="form-control" id="cast_item_name" name="cast_item_name" value="{{ old('cast_item_name', $heat->cast_item_name) }}">
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="mb-3">
                            <label for="date" class="form-label">{{ __('ccplant.heat_date') }}</label>
                            <input type="date" class="form-control" id="date" name="date" value="{{ old('date', $heat->date) }}">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('ccplant.update_heat') }}</button>
                <a href="{{ route('ccplant.index') }}" class="btn btn-secondary">{{ __('ccplant.cancel') }}</a>
            </form>
        </div>
    </div>
</div>
@endsection