<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Vendor Management System</title>
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
        .form-control,
        .form-select {
            background-color: #3b3b4d;
            border: 1px solid #4a4a5a;
            color: #fff;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .form-control:focus,
        .form-select:focus {
            background-color: #4a4a5a;
            border-color: #ffd700;
            box-shadow: 0 0 0 0.25rem rgba(255, 215, 0, 0.25);
            color: #fff;
        }
        .form-select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23ffd700' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
            background-position: right 0.75rem center;
            background-size: 16px 12px;
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
        .card-body h5 {
            color: #ffd700;
            font-weight: 600;
            margin-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 215, 0, 0.1);
            padding-bottom: 0.5rem;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-4">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Vendor Registration</h4>
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

                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5>Account Information</h5>
                                    <hr>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Full Name / Company Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required autofocus>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5>Vendor Details</h5>
                                    <hr>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Vendor Type</label>
                                    <div class="d-flex">
                                        <div class="form-check me-4">
                                            <input class="form-check-input" type="radio" name="type" id="type_company" value="company" {{ old('type') == 'company' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="type_company">Company</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="type" id="type_freelancer" value="freelancer" {{ old('type') == 'freelancer' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="type_freelancer">Freelancer</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="contact_number" class="form-label">Contact Number</label>
                                    <input type="text" class="form-control" id="contact_number" name="contact_number" value="{{ old('contact_number') }}" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="skype" class="form-label">Skype ID</label>
                                    <input type="text" class="form-control" id="skype" name="skype" value="{{ old('skype') }}">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="slack" class="form-label">Slack ID</label>
                                    <input type="text" class="form-control" id="slack" name="slack" value="{{ old('slack') }}">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="poc_name" class="form-label">Vendor POC Name</label>
                                    <input type="text" class="form-control" id="poc_name" name="poc_name" value="{{ old('poc_name') }}" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="internal_poc_id" class="form-label">Internal POC</label>
                                    <select class="form-select" id="internal_poc_id" name="internal_poc_id" required>
                                        <option value="">Select Internal POC</option>
                                        @foreach($internalPocs as $poc)
                                            <option value="{{ $poc->id }}" {{ old('internal_poc_id') == $poc->id ? 'selected' : '' }}>
                                                {{ $poc->name }} ({{ ucfirst($poc->role) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5>Budget Details Per Experience Level</h5>
                                    <hr>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="budget_3_years" class="form-label">3 Years Experience ($/hr)</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="budget_3_years" name="budget_3_years" value="{{ old('budget_3_years') }}" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="budget_5_years" class="form-label">5 Years Experience ($/hr)</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="budget_5_years" name="budget_5_years" value="{{ old('budget_5_years') }}" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="budget_7_years" class="form-label">7+ Years Experience ($/hr)</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="budget_7_years" name="budget_7_years" value="{{ old('budget_7_years') }}" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="budget_10_years" class="form-label">10+ Years Experience ($/hr)</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="budget_10_years" name="budget_10_years" value="{{ old('budget_10_years') }}" required>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Register</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <p class="mb-0">Already have an account? <a href="{{ route('login') }}">Login here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>