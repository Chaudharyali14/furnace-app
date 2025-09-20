@extends('layouts.app')

@section('title', __('messages.dashboard'))

@section('content')
<style>
    .stat-card {
        border-radius: 20px;
        padding: 25px;
        margin-bottom: 20px;
        color: #fff;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.25);
        transition: transform 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    .stat-card.sales {
        background: linear-gradient(135deg, #1e90ff 0%, #00bfff 100%);
    }
    .stat-card.purchases {
        background: linear-gradient(135deg, #2ed573 0%, #7bed9f 100%);
    }
    .stat-card.expenses {
        background: linear-gradient(135deg, #ff4757 0%, #ff6b81 100%);
    }
    .stat-card.profit-loss {
        background: linear-gradient(135deg, #ffa502 0%, #ffb84d 100%);
    }
    .activity-list .list-group-item {
        border-bottom: 1px solid #eee;
    }
    @media (max-width: 768px) {
        .stat-card {
            padding: 20px;
        }
    }
    @media (max-width: 576px) {
        .stat-card {
            padding: 15px;
        }
    }
</style>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.dashboard') }}</h1>
    </div>

    <!-- Stat Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card sales">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="mb-0">{{ number_format($totalSales, 2) }}</h3>
                        <p class="mb-0">{{ __('messages.total_sales') }}</p>
                    </div>
                    <div class="fs-1"><i class="fas fa-dollar-sign"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card purchases">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="mb-0">{{ number_format($totalPurchases, 2) }}</h3>
                        <p class="mb-0">{{ __('messages.total_purchases') }}</p>
                    </div>
                    <div class="fs-1"><i class="fas fa-shopping-cart"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card expenses">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="mb-0">{{ number_format($totalExpenses, 2) }}</h3>
                        <p class="mb-0">{{ __('messages.total_expenses') }}</p>
                    </div>
                    <div class="fs-1"><i class="fas fa-money-bill-wave"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card profit-loss">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="mb-0">{{ number_format($profitLoss, 2) }}</h3>
                        <p class="mb-0">{{ __('messages.profit_loss') }}</p>
                    </div>
                    <div class="fs-1"><i class="fas fa-chart-line"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities + Chart -->
    <div class="row mt-4">
        <!-- Recent Activities -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5><i class="fas fa-history me-2"></i> {{ __('messages.recent_activity') }}</h5>
                </div>
                <ul class="list-group list-group-flush activity-list">
                    @forelse($recentActivities as $activity)
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent">
                            <div>
                                <strong>{{ $activity['description'] }}</strong>
                            </div>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($activity['date'])->diffForHumans() }}</small>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted bg-transparent">
                            {{ __('messages.no_recent_activities_found') }}
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Raw Material Stock Chart -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5><i class="fas fa-chart-pie me-2"></i> {{ __('messages.raw_material_stock') }}</h5>
                </div>
                <div class="card-body">
                    <canvas id="rawMaterialStockChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const rawMaterialStockCtx = document.getElementById('rawMaterialStockChart').getContext('2d');
    new Chart(rawMaterialStockCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($rawMaterialStockChart->pluck('name')) !!},
            datasets: [{
                label: '{{ __('messages.raw_material_stock') }}',
                data: {!! json_encode($rawMaterialStockChart->pluck('quantity')) !!},
                backgroundColor: [
                    '#6a11cb','#00b09b','#fc4a1a','#ee0979',
                    '#2575fc','#96c93d','#f7b733','#ff6a00'
                ],
                borderColor: '#ffffff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' }
            }
        }
    });
</script>
@endpush
@endsection
