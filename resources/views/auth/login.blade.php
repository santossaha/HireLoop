<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Vendor Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(180deg, #1e1e2d 0%, #2d2d3f 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .card {
            background-color: #2a2a3a;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(90deg, #2d2d3f 0%, #1e1e2d 100%);
            color: #ffd700;
            border-bottom: 1px solid rgba(255, 215, 0, 0.2);
            padding: 1.5rem 1.8rem;
            text-align: center;
        }
        .card-header h4 {
            color: #ffd700;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-size: 1.5rem;
            margin-bottom: 0;
        }
        .card-body {
            padding: 2rem;
        }
        .form-label {
            color: #fff;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        .form-control {
            background-color: #3b3b4d;
            border: 1px solid #4a4a5a;
            color: #fff;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            background-color: #4a4a5a;
            border-color: #ffd700;
            box-shadow: 0 0 0 0.25rem rgba(255, 215, 0, 0.25);
            color: #fff;
        }
        .form-check-label {
            color: #ccc;
        }
        .form-check-input:checked {
            background-color: #ffd700;
            border-color: #ffd700;
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
        .alert-danger {
            background-color: #4a2a30;
            border-color: #8b0000;
            color: #ffcccc;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        .alert-danger ul {
            margin-bottom: 0;
            padding-left: 20px;
        }
        .card-footer {
            background-color: #2a2a3a;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding: 1rem 1.8rem;
            color: #ccc;
        }
        .card-footer a {
            color: #ffd700;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        .card-footer a:hover {
            color: #e6c200;
            text-decoration: underline;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Login to Vendor Management System</h4>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autofocus>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Remember Me</label>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <p class="mb-0">Don't have an account? <a href="{{ route('register') }}">Register now</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>