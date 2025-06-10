<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Vendor Management System') }} - @yield('title', 'Dashboard')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.min.css" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Custom CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    
    @yield('styles')
</head>
<body>
    <div class="wrapper d-flex">
        @auth
            @include('layouts.sidebar')
        @endauth
        
        <div class="content-wrapper flex-grow-1">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
                <div class="container-fluid">
                    @auth
                        <button class="btn btn-outline-secondary me-2" id="sidebar-toggle">
                            <i class="fas fa-bars"></i>
                        </button>
                    @endauth
                    
                    <a class="navbar-brand" href="{{ route('dashboard') }}">
                        <span>Vendor Management System</span>
                    </a>
                    
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto">
                            @guest
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">Register</a>
                                </li>
                            @else
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-bell"></i>
                                        <span class="badge bg-danger rounded-pill notification-badge" style="display: none;">0</span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationsDropdown" style=" max-height: 400px; overflow-y: auto;">
                                        <div class="notification-list">
                                            <!-- Notifications will be loaded here -->
                                        </div>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item text-center" href="{{ route('notifications.index') }}">View All Notifications</a>
                                    </div>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                        {{-- <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profile</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Settings</a></li> --}}
                                        {{-- <li><hr class="dropdown-divider"></li> --}}
                                        <li>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" class="dropdown-item">
                                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </nav>
            
            <!-- Main Content -->
            <main class="container-fluid py-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @yield('content')
            </main>
            
            <!-- Footer -->
            <footer class="bg-light py-3 text-center" >
                <div class="container">
                    <p class="mb-0">&copy; {{ date('Y') }} Vendor Management System. All rights reserved.</p>
                </div>
            </footer>
        </div>
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.all.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
    
    <!-- Custom JS -->

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="{{ asset('js/app.js') }}"></script>

    <script>
        let notificationUrl  = "{{ url('notifications/latest') }}";
        
        // Sidebar Toggle Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebar = document.getElementById('sidebar');
            const contentWrapper = document.querySelector('.content-wrapper');
            
            if (sidebarToggle && sidebar && contentWrapper) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('d-none');
                    contentWrapper.classList.toggle('ms-0');
                    contentWrapper.classList.toggle('ms-240');
                });
            }
        });

        function loadNotifications() {
            fetch(notificationUrl)
                .then(response => response.json())
                .then(notifications => {
                    const notificationList = document.querySelector('.notification-list');
                    const badge = document.querySelector('.notification-badge');
                    
                    if (notifications.length > 0) {
                        badge.style.display = 'inline';
                        badge.textContent = notifications.length;
                        
                        notificationList.innerHTML = notifications.map(notification => `
                            <a class="dropdown-item notification-item ${notification.read_at ? 'read' : ''}" 
                               href="#" onclick="markAsRead('${notification.id}')">
                                <div class="d-flex flex-column">
                                    <div class="notification-title fw-bold">${notification.data.title || 'New Notification'}</div>
                                    <div class="notification-message">${notification.data.message || notification.data.job_description || 'New requirement has been created'}</div>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <small class="text-muted">${new Date(notification.created_at).toLocaleString()}</small>
                                        ${notification.data.redirect_url ? `
                                            <a href="${notification.data.redirect_url}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        ` : ''}
                                    </div>
                                </div>
                            </a>
                        `).join('');
                    } else {
                        badge.style.display = 'none';
                        notificationList.innerHTML = '<div class="dropdown-item text-center">No new notifications</div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                });
        }

        function markAsRead(id) {
            fetch(`/notifications/${id}/mark-as-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    loadNotifications(); // Reload notifications after marking as read
                }
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
            });
        }

        // Load notifications every 5 seconds
        setInterval(loadNotifications, 10000);

        // Initial load
        document.addEventListener('DOMContentLoaded', loadNotifications);
    </script>
    
    @yield('scripts')
</body>
</html>
