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
                                <h5>General Details</h5>
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
                                <label for="type" class="form-label">Vendor Type</label>
                                <select class="form-select" id="type" name="vendor_type" required onchange="vendorType(this.value);">
                                    <option value="">Select Vendor Type</option>
                                    <option value="company" {{ old('type') == 'company' ? 'selected' : '' }}>Company</option>
                                    <option value="individual" {{ old('type') == 'individual' ? 'selected' : '' }}>Individual</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="company_name" class="form-label company_name">Person/Founder Name</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" value="{{ old('company_name') }}" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="phone" class="form-label founder_number_label"> Contact Number</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="company_email" class="form-label founder_email_label"> Email</label>
                                <input type="email" class="form-control" id="founder_email" name="company_email" value="{{ old('company_email') }}" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}" required maxlength="150">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="website" class="form-label">Website</label>
                                <input type="url" class="form-control" id="website" name="website" value="{{ old('website') }}" maxlength="50">
                            </div>

                            <div class="individual_vendor" style="display: none">
                                <div class="col-md-12 mb-3">
                                    <label for="year_of_experience" class="form-label">Year of Experience</label>
                                    <input type="number" min="0" class="form-control" id="year_of_experience" name="year_of_experience" value="{{ old('year_of_experience') }}" maxlength="10">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="website" class="form-label">Budget</label>
                                    <input type="url" class="form-control" id="website" name="website" value="{{ old('website') }}" maxlength="50">
                                </div>
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

                        <div class="company_budget" style="display: none">
                            <h5 class="mb-3">Budget Information</h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="budget_3_years" class="form-label">3 Years Experience</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" step="0.01" class="form-control" id="budget_3_years" name="budget_3_years" value="{{ old('budget_3_years') }}" >
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="budget_5_years" class="form-label">5 Years Experience</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" step="0.01" class="form-control" id="budget_5_years" name="budget_5_years" value="{{ old('budget_5_years') }}" >
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="budget_7_years" class="form-label">7+ Years Experience</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" step="0.01" class="form-control" id="budget_7_years" name="budget_7_years" value="{{ old('budget_7_years') }}" >
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="budget_10_years" class="form-label">10+ Years Experience</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" step="0.01" class="form-control" id="budget_10_years" name="budget_10_years" value="{{ old('budget_10_years') }}" >
                                        </div>
                                    </div>
                                </div>
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
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function vendorType(vtype){
        if(vtype == 'individual'){
            $('.company_budget').hide();
            $('.individual_vendor').show();
        }
        else if(vtype == 'company'){
            $('.company_budget').show();
            $('.individual_vendor').hide();
        }
    }

    $(function (){
        $('input[type="number"]').keyup(function(e)
        {
            if (/\D/g.test(this.value))
            {
                // Filter non-digits from input value.
                this.value = this.value.replace(/\D/g, '');
            }
        });
        $('input[type="number"]').change(function(e)
        {
            if (/\D/g.test(this.value))
            {
                // Filter non-digits from input value.
                this.value = this.value.replace(/\D/g, '');
            }
        });
    });
</script>
</body>
</html>