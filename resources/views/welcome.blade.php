<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .hero-section {
            padding: 4rem 0;
        }
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #4e73df;
        }
        .btn-primary {
            background-color: #4e73df;
            border-color: #4e73df;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row hero-section align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Vendor Management System</h1>
                <p class="lead mb-4">Streamline your vendor registration, requirements tracking, interview scheduling, payment approval, and invoicing processes with our comprehensive platform.</p>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-start mb-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg px-4 me-md-2">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-4 me-md-2">Login</a>
                        <a href="{{ route('register') }}" class="btn btn-secondary btn-lg px-4">Register as Vendor</a>
                    @endauth
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card h-100 p-4 text-center">
                            <div class="card-icon">📋</div>
                            <h5 class="card-title">Vendor Registration</h5>
                            <p class="card-text">Easy onboarding process for new vendors with detailed profile management.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 p-4 text-center">
                            <div class="card-icon">🔍</div>
                            <h5 class="card-title">Requirement Tracking</h5>
                            <p class="card-text">Submit and track client requirements, budgets, and interview status.</p>
                        </div>
                    </div>
                    <div class="col-md-6 mt-4">
                        <div class="card h-100 p-4 text-center">
                            <div class="card-icon">💸</div>
                            <h5 class="card-title">Payment Management</h5>
                            <p class="card-text">Track client payments, generate invoices, and manage vendor payments.</p>
                        </div>
                    </div>
                    <div class="col-md-6 mt-4">
                        <div class="card h-100 p-4 text-center">
                            <div class="card-icon">📊</div>
                            <h5 class="card-title">Reporting</h5>
                            <p class="card-text">Comprehensive reports on vendor performance, payments, and more.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>