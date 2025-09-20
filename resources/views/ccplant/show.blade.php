@extends('layouts.app')

@section('title', __('ccplant.cc_plant_details'))

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3>{{ __('ccplant.cc_plant_details') }} No: {{ $heat->heat_no }}</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>{{ __('ccplant.heat_date') }}:</strong> {{ $heat->date }}</p>
                    <p><strong>{{ __('ccplant.total_metal_kg') }}:</strong> {{ $heat->total_metal }} kg</p>
                    <p><strong>{{ __('ccplant.casted_metal_kg') }}:</strong> {{ $heat->casted_metal }} kg</p>
                </div>
                <div class="col-md-6">
                    <p><strong>{{ __('ccplant.uncast_metal_kg') }}:</strong> {{ $heat->uncast_metal }} kg</p>
                    <p><strong>{{ __('ccplant.billet_size_inch') }}:</strong> {{ $heat->billet_size_inch }} inch</p>
                    <p><strong>{{ __('ccplant.cast_item_name') }}:</strong> {{ $heat->cast_item_name }}</p>
                </div>
            </div>
            <a href="{{ route('ccplant.index') }}" class="btn btn-primary mt-3">{{ __('ccplant.back_to_list') }}</a>
        </div>
    </div>
</div>
@endsection