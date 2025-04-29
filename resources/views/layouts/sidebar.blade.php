<div class="sidebar bg-dark text-white" id="sidebar">
    <div class="sidebar-header p-3 border-bottom">
        <h5 class="mb-0">Vendor MS</h5>
    </div>
    <div class="sidebar-content p-0">
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                @can('view-dashboard')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                    </a>
                </li>
                @endcan

                @can('view-users')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                        <i class="fas fa-users me-2"></i> Users
                    </a>
                </li>
                @endcan

                @can('view-roles')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}">
                        <i class="fas fa-user-shield me-2"></i> Roles & Permissions
                    </a>
                </li>
                @endcan
                
                @can('view-vendors')
                <li class="nav-section mt-2">
                    <span class="sidebar-heading px-3 py-2 d-block">Vendor Management</span>
                </li>
                @endcan

                @can('view-vendors')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('vendors.*') ? 'active' : '' }}" href="{{ route('vendors.index') }}">
                        <i class="fas fa-users me-2"></i> Vendors
                    </a>
                </li>
                @endcan

                @can('view-vendors')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('vendors.*') ? 'active' : '' }}" href="{{ route('candidate-sourcing.index') }}">
                        <i class="fas fa-users me-2"></i> Vendors Requirements
                    </a>
                </li>
                @endcan

                {{-- @can('view-requirements') --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('candidate-sourcing.*') ? 'active' : '' }}" href="{{ route('requirements.index') }}">
                        <i class="fas fa-clipboard-list me-2"></i> Requirements
                    </a>
                </li>
                {{-- @endcan --}}

                @can('view-interviews')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('interviews.*') ? 'active' : '' }}" href="{{ route('interviews.index') }}">
                        <i class="fas fa-user-tie me-2"></i> Interviews
                    </a>
                </li>
                @endcan
                
                @can('view-client-payments')
                <li class="nav-section mt-2">
                    <span class="sidebar-heading px-3 py-2 d-block">Payment Management</span>
                </li>
                @endcan

                @can('view-client-payments')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('client-payments.*') ? 'active' : '' }}" href="{{ route('client-payments.index') }}">
                        <i class="fas fa-money-check-alt me-2"></i> Client Payments
                    </a>
                </li>
                @endcan

                @can('view-vendor-attendances')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('vendor-attendances.*') ? 'active' : '' }}" href="{{ route('vendor-attendances.index') }}">
                        <i class="fas fa-calendar-check me-2"></i> Attendance
                    </a>
                </li>
                @endcan

                @can('view-invoices')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}" href="{{ route('invoices.index') }}">
                        <i class="fas fa-file-invoice me-2"></i> Invoices
                    </a>
                </li>
                @endcan

                @can('view-client-payments')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('vendor-payments.*') ? 'active' : '' }}" href="{{ route('vendor-payments.index') }}">
                        <i class="fas fa-hand-holding-usd me-2"></i> Vendor Payments
                    </a>
                </li>
                @endcan
                
                @can('view-vendor-payments', 'export-vendor-payments','view-invoice-summary','view-vendor-attendance-summary')
                <li class="nav-section mt-2">
                    <span class="sidebar-heading px-3 py-2 d-block">Reports</span>
                </li>
                @endcan
               

                @can('view-vendor-payments')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('vendor-payments.monthly-report') ? 'active' : '' }}" href="{{ route('vendor-payments.monthly-report') }}">
                        <i class="fas fa-history me-2"></i> Payment History
                    </a>
                </li>
                @endcan

                @can('export-vendor-payments')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('vendor-payments.export') ? 'active' : '' }}" href="{{ route('vendor-payments.export') }}">
                        <i class="fas fa-file-export me-2"></i> Export Payments
                    </a>
                </li>
                @endcan

                @can('view-invoice-summary')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('invoices.summary') ? 'active' : '' }}" href="{{ route('invoices.summary') }}">
                        <i class="fas fa-chart-bar me-2"></i> Invoice Summary
                    </a>
                </li>
                @endcan

                @can('view-vendor-attendance-summary')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('vendor-attendances.summary') ? 'active' : '' }}" href="{{ route('vendor-attendances.summary') }}">
                        <i class="fas fa-calendar-alt me-2"></i> Attendance Summary
                    </a>
                </li>
                @endcan
                
                {{-- @if(auth()->user()->can('view departments') || auth()->user()->can('view settings'))
                <li class="nav-section mt-2">
                    <span class="sidebar-heading px-3 py-2 d-block">Administration</span>
                </li>
                @endif --}}

                {{-- @can('view departments')
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-building me-2"></i> Departments
                    </a>
                </li>
                @endcan --}}

                {{-- @can('view settings')
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-cog me-2"></i> Settings
                    </a>
                </li>
                @endcan --}}
            </ul>
        </nav>
    </div>
</div>
