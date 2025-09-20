@extends('layouts.app')

@section('title', __('expenses.edit_expense'))

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('expenses.edit_expense') }}</h1>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>{{ __('expenses.whoops') }}</strong> {{ __('expenses.problems_with_input') }}<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('expenses.expense_details') }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('expenses.update', $expense) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                        <label for="title" class="form-label">{{ __('expenses.title') }}</label>
                        <input type="text" class="form-control" id="title" name="title" value="{{ $expense->title }}" required>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                        <label for="amount" class="form-label">{{ __('expenses.amount') }}</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="{{ $expense->amount }}" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                        <label for="category" class="form-label">{{ __('expenses.category') }}</label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="Utility" {{ $expense->category == 'Utility' ? 'selected' : '' }}>{{ __('expenses.utility') }}</option>
                            <option value="Transport" {{ $expense->category == 'Transport' ? 'selected' : '' }}>{{ __('expenses.transport') }}</option>
                            <option value="Staff" {{ $expense->category == 'Staff' ? 'selected' : '' }}>{{ __('expenses.staff') }}</option>
                            <option value="Repair" {{ $expense->category == 'Repair' ? 'selected' : '' }}>{{ __('expenses.repair') }}</option>
                            <option value="Office" {{ $expense->category == 'Office' ? 'selected' : '' }}>{{ __('expenses.office') }}</option>
                            <option value="Miscellaneous" {{ $expense->category == 'Miscellaneous' ? 'selected' : '' }}>{{ __('expenses.miscellaneous') }}</option>
                        </select>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                        <label for="expense_date" class="form-label">{{ __('expenses.expense_date') }}</label>
                        <input type="date" class="form-control" id="expense_date" name="expense_date" value="{{ $expense->expense_date }}" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">{{ __('expenses.description') }}</label>
                    <textarea class="form-control" id="description" name="description" rows="3">{{ $expense->description }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('expenses.update') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection
