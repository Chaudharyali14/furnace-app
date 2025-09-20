<div class="offcanvas offcanvas-start offcanvas-lg text-bg-dark" tabindex="-1" 
     id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="sidebarMenuLabel">{{ __('messages.furnace_ms') }}</h5>
        <button type="button" class="btn-close btn-close-white" 
                data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ url('/') }}" style="font-size: 1.8rem;">
                    <i class="fas fa-home"></i> {{ __('messages.furnace_ms') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ url('/') }}">
                    <i class="fas fa-tachometer-alt"></i> {{ __('messages.dashboard') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ url('/purchase') }}">
                    <i class="fas fa-shopping-cart"></i> {{ __('messages.purchase_scrap') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ route('purchase.scrap_purchase_list') }}">
                    <i class="fas fa-list"></i> {{ __('messages.purchase_scrap_list') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ url('/furnace/issue_to_furnace') }}">
                    <i class="fas fa-fire"></i> {{ __('messages.issued_to_furnace') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ url('/furnace/raw_material_stock') }}">
                    <i class="fas fa-boxes"></i> {{ __('messages.raw_material_stock') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ url('/electricity') }}">
                    <i class="fas fa-bolt"></i> {{ __('messages.electricity') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ url('/electricity/log_table') }}">
                    <i class="fas fa-file-alt"></i> {{ __('messages.electricity_log_table') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ route('expenses.index') }}">
                    <i class="fas fa-money-bill-wave"></i> {{ __('messages.manage_expenses') }}
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-white" href="#" 
                   id="employeesDropdown" role="button" data-bs-toggle="dropdown" 
                   aria-expanded="false">
                    <i class="fas fa-users"></i> {{ __('messages.employees') }}
                </a>
                <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="employeesDropdown">
                    <li><a class="dropdown-item" href="{{ route('employees.index') }}"><i class="fas fa-user-cog"></i> {{ __('messages.manage_employees') }}</a></li>
                    <li><a class="dropdown-item" href="{{ route('employee.ledger') }}"><i class="fas fa-book"></i> {{ __('messages.employee_ledger') }}</a></li>
                </ul>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ url('/ccplant') }}">
                    <i class="fas fa-cogs"></i> {{ __('messages.cc_plant') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ route('stock.index') }}">
                    <i class="fas fa-boxes"></i> {{ __('messages.finish_goods_stock') }}
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-white" href="#" 
                   id="reportsDropdown" role="button" data-bs-toggle="dropdown" 
                   aria-expanded="false">
                    <i class="fas fa-chart-bar"></i> {{ __('messages.reports') }}
                </a>
                <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="reportsDropdown">
                    <li><a class="dropdown-item" href="{{ route('purchase.report') }}"><i class="fas fa-shopping-cart"></i> {{ __('messages.purchase_report') }}</a></li>
                    <li><a class="dropdown-item" href="{{ route('sales.report') }}"><i class="fas fa-chart-line"></i> {{ __('messages.sales_report') }}</a></li>
                    <li><a class="dropdown-item" href="{{ route('reports.profit_loss') }}"><i class="fas fa-file-invoice-dollar"></i> {{ __('messages.profit_loss_report') }}</a></li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-white" href="#" 
                   id="settingsDropdown" role="button" data-bs-toggle="dropdown" 
                   aria-expanded="false">
                    <i class="fas fa-cog"></i> {{ __('messages.settings') }}
                </a>
                <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="settingsDropdown">
                    <li><a class="dropdown-item" href="{{ route('profile.settings') }}"><i class="fas fa-user-edit"></i> {{ __('messages.profile_settings') }}</a></li>
                    <li><a class="dropdown-item" href="{{ route('security.settings') }}"><i class="fas fa-shield-alt"></i> {{ __('messages.security_settings') }}</a></li>
                    <li><a class="dropdown-item" href="{{ route('application.settings') }}"><i class="fas fa-cogs"></i> {{ __('messages.application_settings') }}</a></li>
                </ul>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="#" 
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> {{ __('messages.logout') }}
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>
    </div>
</div>
