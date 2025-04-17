@extends('layouts.app')

@section('title', 'Vendor Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Vendor Details</h1>
        <div>
            {{-- <a href="{{ route('requirements.create', ['vendor_id' => $vendor->id]) }}" class="btn btn-success me-2">
                <i class="fas fa-plus-circle me-1"></i> Add Requirement
            </a>
            <a href="{{ route('interviews.create', ['vendor_id' => $vendor->id]) }}" class="btn btn-info me-2">
                <i class="fas fa-calendar-plus me-1"></i> Schedule Interview
            </a>
            <a href="{{ route('vendors.edit', $vendor->id) }}" class="btn btn-primary me-2">
                <i class="fas fa-edit me-1"></i> Edit Vendor
            </a> --}}
            <a href="{{ route('vendors.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Vendors
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4">
            <!-- Vendor Info Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Vendor Information</h6>
                    <span class="badge bg-{{ $vendor->client_ready ? 'success' : 'warning' }}">
                        {{ $vendor->client_ready ? 'Client Ready' : 'Not Client Ready' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="display-4 mb-2">
                            <i class="fas {{ $vendor->vendor_type == 'company' ? 'fa-building' : 'fa-user-tie' }}"></i>
                        </div>
                        <h4>{{ $vendor->company_name }}</h4>
                        <span class="badge bg-primary">{{ ucfirst($vendor->vendor_type) }}</span>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <p class="mb-1"><strong>Contact Person:</strong></p>
                        <p>{{ $vendor->contact_person }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <p class="mb-1"><strong>Contact Information:</strong></p>
                        <p><i class="fas fa-envelope me-2"></i> {{ $vendor->email }}</p>
                        <p><i class="fas fa-phone me-2"></i> {{ $vendor->phone }}</p>
                        @if($vendor->skype_id)
                        <p><i class="fab fa-skype me-2"></i> {{ $vendor->skype_id }}</p>
                        @endif
                        @if($vendor->slack_id)
                        <p><i class="fab fa-slack me-2"></i> {{ $vendor->slack_id }}</p>
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <p class="mb-1"><strong>Internal POC:</strong></p>
                        <p>{{ $vendor->internalPoc ? $vendor->internalPoc->name : 'N/A' }}</p>
                    </div>
                    
                    <hr>
                    
                    <h6 class="font-weight-bold">Budget Information</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Experience</th>
                                    <th>Budget</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>3 Years</td>
                                    <td>${{ number_format($vendor->budget_3_years, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>5 Years</td>
                                    <td>${{ number_format($vendor->budget_5_years, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>7+ Years</td>
                                    <td>${{ number_format($vendor->budget_7_years, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>10+ Years</td>
                                    <td>${{ number_format($vendor->budget_10_years, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <hr>
                    
                    <h6 class="font-weight-bold">Ratings & Availability</h6>
                    <div class="mb-3">
                        <p class="mb-1"><strong>Communication:</strong> 
                            @if($vendor->communication_rating)
                                @if($vendor->communication_rating == 'excellent')
                                    <span class="text-success">Excellent</span>
                                @elseif($vendor->communication_rating == 'good')
                                    <span class="text-primary">Good</span>
                                @elseif($vendor->communication_rating == 'average')
                                    <span class="text-warning">Average</span>
                                @else
                                    <span class="text-danger">Bad</span>
                                @endif
                            @else
                                <span class="text-muted">Not rated</span>
                            @endif
                        </p>
                        
                        <p class="mb-1"><strong>Technical:</strong> 
                            @if($vendor->technical_rating)
                                @if($vendor->technical_rating == 'excellent')
                                    <span class="text-success">Excellent</span>
                                @elseif($vendor->technical_rating == 'good')
                                    <span class="text-primary">Good</span>
                                @elseif($vendor->technical_rating == 'average')
                                    <span class="text-warning">Average</span>
                                @else
                                    <span class="text-danger">Bad</span>
                                @endif
                            @else
                                <span class="text-muted">Not rated</span>
                            @endif
                        </p>
                        
                        <p class="mb-1"><strong>Availability:</strong> {{ $vendor->availability ?: 'Not specified' }}</p>
                        <p class="mb-1"><strong>MT/EAD Status:</strong> {{ $vendor->mt_ead_status ?: 'Not specified' }}</p>
                    </div>
                    
                    <!-- Approval Form for POCs, HODs, and Admins -->
                    @if(auth()->user()->isPoc() || auth()->user()->isHod() || auth()->user()->isAdmin())
                    <hr>
                    <h6 class="font-weight-bold">Update Client Readiness</h6>
                    <form action="{{ route('vendors.approve', $vendor->id) }}" method="POST" class="mt-3">
                        @csrf
                        <div class="mb-3">
                            <label for="client_ready" class="form-label">Client Interview Ready</label>
                            <select class="form-select" id="client_ready" name="client_ready" required>
                                <option value="1" {{ $vendor->client_ready ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ !$vendor->client_ready ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="communication_rating" class="form-label">Communication Rating</label>
                            <select class="form-select" id="communication_rating" name="communication_rating" required>
                                <option value="excellent" {{ $vendor->communication_rating == 'excellent' ? 'selected' : '' }}>Excellent</option>
                                <option value="good" {{ $vendor->communication_rating == 'good' ? 'selected' : '' }}>Good</option>
                                <option value="average" {{ $vendor->communication_rating == 'average' ? 'selected' : '' }}>Average</option>
                                <option value="bad" {{ $vendor->communication_rating == 'bad' ? 'selected' : '' }}>Bad</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="technical_rating" class="form-label">Technical Rating</label>
                            <select class="form-select" id="technical_rating" name="technical_rating" required>
                                <option value="excellent" {{ $vendor->technical_rating == 'excellent' ? 'selected' : '' }}>Excellent</option>
                                <option value="good" {{ $vendor->technical_rating == 'good' ? 'selected' : '' }}>Good</option>
                                <option value="average" {{ $vendor->technical_rating == 'average' ? 'selected' : '' }}>Average</option>
                                <option value="bad" {{ $vendor->technical_rating == 'bad' ? 'selected' : '' }}>Bad</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Update Status</button>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-xl-8">
            <!-- Requirements Tab -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <ul class="nav nav-tabs card-header-tabs" id="vendorTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="requirements-tab" data-bs-toggle="tab" data-bs-target="#requirements" type="button" role="tab" aria-controls="requirements" aria-selected="true">
                                <i class="fas fa-clipboard-list me-1"></i> Requirements
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="interviews-tab" data-bs-toggle="tab" data-bs-target="#interviews" type="button" role="tab" aria-controls="interviews" aria-selected="false">
                                <i class="fas fa-user-tie me-1"></i> Interviews
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance" type="button" role="tab" aria-controls="attendance" aria-selected="false">
                                <i class="fas fa-calendar-check me-1"></i> Attendance
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="invoices-tab" data-bs-toggle="tab" data-bs-target="#invoices" type="button" role="tab" aria-controls="invoices" aria-selected="false">
                                <i class="fas fa-file-invoice me-1"></i> Invoices & Payments
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="vendorTabContent">
                        <!-- Requirements Tab Content -->
                        <div class="tab-pane fade show active" id="requirements" role="tabpanel" aria-labelledby="requirements-tab">
                            @if($vendor->requirements->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Job Description</th>
                                                <th>Budget</th>
                                                <th>Status</th>
                                                <th>Department</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($vendor->requirements as $requirement)
                                            <tr>
                                                <td>{{ $requirement->requirement_id }}</td>
                                                <td>{{ \Illuminate\Support\Str::limit($requirement->job_description, 50) }}</td>
                                                <td>${{ number_format($requirement->proposed_budget, 2) }}</td>
                                                <td>
                                                    @if($requirement->founder_approved && $requirement->hod_approved)
                                                        <span class="badge bg-success">Approved</span>
                                                    @elseif($requirement->hod_approved)
                                                        <span class="badge bg-warning">HOD Approved</span>
                                                    @else
                                                        <span class="badge bg-secondary">Pending</span>
                                                    @endif
                                                </td>
                                                <td>{{ $requirement->department->name ?? 'N/A' }}</td>
                                                <td>
                                                    <a href="{{ route('requirements.show', $requirement->id) }}" class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    No requirements submitted yet for this vendor.
                                </div>
                            @endif
                            <div class="mt-3">
                                <a href="{{ route('requirements.create', ['vendor_id' => $vendor->id]) }}" class="btn btn-success">
                                    <i class="fas fa-plus-circle me-1"></i> Submit New Requirement
                                </a>
                            </div>
                        </div>
                        
                        <!-- Interviews Tab Content -->
                        <div class="tab-pane fade" id="interviews" role="tabpanel" aria-labelledby="interviews-tab">
                            @if($vendor->interviews->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Type</th>
                                                <th>Requirement</th>
                                                <th>Interviewer</th>
                                                <th>Status</th>
                                                <th>Result</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($vendor->interviews as $interview)
                                            <tr>
                                                <td>{{ $interview->scheduled_at->format('M d, Y H:i') }}</td>
                                                <td>{{ ucfirst($interview->type) }}</td>
                                                <td>{{ $interview->requirement->requirement_id ?? 'N/A' }}</td>
                                                <td>{{ $interview->interviewer->name ?? 'N/A' }}</td>
                                                <td>
                                                    @if($interview->status == 'scheduled')
                                                        <span class="badge bg-primary">Scheduled</span>
                                                    @elseif($interview->status == 'completed')
                                                        <span class="badge bg-success">Completed</span>
                                                    @else
                                                        <span class="badge bg-danger">Cancelled</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($interview->result == 'pass')
                                                        <span class="badge bg-success">Pass</span>
                                                    @elseif($interview->result == 'fail')
                                                        <span class="badge bg-danger">Fail</span>
                                                    @else
                                                        <span class="badge bg-secondary">Pending</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('interviews.show', $interview->id) }}" class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    No interviews scheduled yet for this vendor.
                                </div>
                            @endif
                            <div class="mt-3">
                                <a href="{{ route('interviews.create', ['vendor_id' => $vendor->id]) }}" class="btn btn-success">
                                    <i class="fas fa-calendar-plus me-1"></i> Schedule New Interview
                                </a>
                            </div>
                        </div>
                        
                        <!-- Attendance Tab Content -->
                        <div class="tab-pane fade" id="attendance" role="tabpanel" aria-labelledby="attendance-tab">
                            @if($vendor->attendances->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Month</th>
                                                <th>Working Days</th>
                                                <th>Present Days</th>
                                                <th>Leave Days</th>
                                                <th>Approved Leaves</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($vendor->attendances as $attendance)
                                            <tr>
                                                <td>{{ $attendance->month_year->format('F Y') }}</td>
                                                <td>{{ $attendance->working_days }}</td>
                                                <td>{{ $attendance->present_days }}</td>
                                                <td>{{ $attendance->leave_days }}</td>
                                                <td>{{ $attendance->approved_leave_days }}</td>
                                                <td>
                                                    @if($attendance->status == 'approved')
                                                        <span class="badge bg-success">Approved</span>
                                                    @elseif($attendance->status == 'rejected')
                                                        <span class="badge bg-danger">Rejected</span>
                                                    @else
                                                        <span class="badge bg-warning">Pending</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('attendance.index', ['vendor_id' => $vendor->id, 'month' => $attendance->month_year->month, 'year' => $attendance->month_year->year]) }}" class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    No attendance records found for this vendor.
                                </div>
                            @endif
                            <div class="mt-3">
                                <a href="{{ route('vendor-attendances.create', ['vendor_id' => $vendor->id]) }}" class="btn btn-success">
                                    <i class="fas fa-plus-circle me-1"></i> Submit Attendance
                                </a>
                            </div>
                        </div>
                        
                        <!-- Invoices & Payments Tab Content -->
                        <div class="tab-pane fade" id="invoices" role="tabpanel" aria-labelledby="invoices-tab">
                            <h5 class="mb-3">Invoices</h5>
                            @if($vendor->invoices->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Invoice #</th>
                                                <th>Month</th>
                                                <th>Amount</th>
                                                <th>Verification</th>
                                                <th>Payment Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($vendor->invoices as $invoice)
                                            <tr>
                                                <td>{{ $invoice->invoice_number }}</td>
                                                <td>{{ $invoice->month_year->format('F Y') }}</td>
                                                <td>${{ number_format($invoice->amount, 2) }}</td>
                                                <td>
                                                    @if($invoice->verification_status == 'verified')
                                                        <span class="badge bg-success">Verified</span>
                                                    @elseif($invoice->verification_status == 'discrepancy')
                                                        <span class="badge bg-danger">Discrepancy</span>
                                                    @else
                                                        <span class="badge bg-warning">Pending</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($invoice->payment_status == 'paid')
                                                        <span class="badge bg-success">Paid</span>
                                                    @elseif($invoice->payment_status == 'approved')
                                                        <span class="badge bg-info">Approved</span>
                                                    @else
                                                        <span class="badge bg-warning">Pending</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    No invoices submitted yet for this vendor.
                                </div>
                            @endif
                            
                            <div class="mt-3 mb-4">
                                <a href="{{ route('invoices.create', ['vendor_id' => $vendor->id]) }}" class="btn btn-success">
                                    <i class="fas fa-file-invoice me-1"></i> Submit New Invoice
                                </a>
                            </div>
                            
                            <h5 class="mb-3">Payments</h5>
                            @if($vendor->payments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Month</th>
                                                <th>Calculated Amount</th>
                                                <th>Deductions</th>
                                                <th>Final Amount</th>
                                                <th>Status</th>
                                                <th>Payment Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($vendor->payments as $payment)
                                            <tr>
                                                <td>{{ $payment->month_year->format('F Y') }}</td>
                                                <td>${{ number_format($payment->calculated_amount, 2) }}</td>
                                                <td>${{ number_format($payment->deductions, 2) }}</td>
                                                <td>${{ number_format($payment->final_amount, 2) }}</td>
                                                <td>
                                                    @if($payment->payment_status == 'paid')
                                                        <span class="badge bg-success">Paid</span>
                                                    @elseif($payment->payment_status == 'approved')
                                                        <span class="badge bg-info">Approved</span>
                                                    @else
                                                        <span class="badge bg-warning">Pending</span>
                                                    @endif
                                                </td>
                                                <td>{{ $payment->payment_date ? $payment->payment_date->format('M d, Y') : 'Not paid' }}</td>
                                                <td>
                                                    <a href="{{ route('vendor.payments.index', ['vendor_id' => $vendor->id, 'month' => $payment->month_year->month, 'year' => $payment->month_year->year]) }}" class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    No payment records found for this vendor.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Handle tab activation from URL hash
        var hash = window.location.hash;
        if (hash) {
            $('.nav-tabs a[href="' + hash + '"]').tab('show');
        }
        
        // Update URL hash when tab is clicked
        $('.nav-tabs a').on('shown.bs.tab', function(e) {
            window.location.hash = e.target.hash;
        });
    });
</script>
@endsection
