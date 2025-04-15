<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Vendor Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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