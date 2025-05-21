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
                    <h4 class="mb-0">Vendor Register</h4>
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

                    <form method="POST" action="{{ route('vendor-post-invite') }}">
                        @csrf
                        <input type="hidden" name="invite_token" value="{{$id}}">
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5>Founder Section</h5>
                                <hr>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name / Company Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required autofocus maxlength="50">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required  maxlength="50">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required maxlength="50">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required maxlength="50">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5>Company Details</h5>
                                <hr>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}" required maxlength="150">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="website" class="form-label">Website</label>
                                <input type="url" class="form-control" id="website" name="website" value="{{ old('website') }}" maxlength="50">
                            </div>

                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5>Bank Details</h5>
                                <hr>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="account_owner_name" class="form-label">Account Owner Name</label>
                                <input type="text" class="form-control" id="account_owner_name" name="account_owner_name" value="{{ old('account_owner_name') }}"  maxlength="70">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="account_owner_name" class="form-label">Account Number</label>
                                <input type="text" class="form-control" id="account_number" name="account_number" value="{{ old('account_number') }}" maxlength="70">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="website" class="form-label">Bank Name</label>
                                <input type="text" class="form-control" id="bank_name" name="bank_name" value="{{ old('bank_name') }}" maxlength="70">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ifsc_code" class="form-label">IFSC Code</label>
                                <input type="text" class="form-control" id="ifsc_code" name="ifsc_code" value="{{ old('ifsc_code') }}" maxlength="70">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="gst_number" class="form-label">GST Number</label>
                                <input type="text" class="form-control" id="gst_number" name="gst_number" value="{{ old('gst_number') }}" maxlength="70">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pan" class="form-label">PAN</label>
                                <input type="text" class="form-control" id="pan" name="pan" value="{{ old('pan') }}" maxlength="70">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="teams_id" class="form-label">Teams ID</label>
                                <input type="text" class="form-control" id="teams_id" name="teams_id" value="{{ old('teams_id') }}" maxlength="30">
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Register</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>