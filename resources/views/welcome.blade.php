<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(180deg, #1e1e2d 0%, #2d2d3f 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: Arial, sans-serif;
            color: #ccc;
        }
        .hero-section {
            padding: 4rem 0;
            color: #fff;
        }
        .hero-section h1 {
            color: #ffd700;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .hero-section p.lead {
            color: rgba(255, 255, 255, 0.8);
        }
        .card {
            background-color: #2a2a3a;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.6);
        }
        .card-icon {
            font-size: 3rem;
            margin-bottom: 1.5rem;
            color: #ffd700;
            transition: transform 0.3s ease;
        }
        .card:hover .card-icon {
            transform: scale(1.1);
        }
        .card-title {
            color: #ffd700;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }
        .card-text {
            color: rgba(255, 255, 255, 0.7);
        }
        .btn-primary {
            background-color: #ffd700;
            border-color: #ffd700;
            color: #1e1e2d;
            font-weight: 700;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(255, 215, 0, 0.2);
        }
        .btn-primary:hover {
            background-color: #e6c200;
            border-color: #e6c200;
            color: #1e1e2d;
            box-shadow: 0 8px 20px rgba(255, 215, 0, 0.3);
            transform: translateY(-2px);
        }
        .btn-secondary {
            background-color: #3b3b4d;
            border-color: #3b3b4d;
            color: #fff;
            font-weight: 600;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        .btn-secondary:hover {
            background-color: #4a4a5a;
            border-color: #4a4a5a;
            color: #fff;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            transform: translateY(-2px);
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