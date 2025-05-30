@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Onboarding Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('onboardings.index') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px;">Requirement ID</th>
                                    <td>{{ $onboarding->requirement->requirement_id ?? $onboarding->requirement_id ??  'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Vendor Name</th>
                                    <td>{{ $onboarding->vendor->company_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Candidate Name</th>
                                    <td>{{ $onboarding->candidate->candidate_name ?? $onboarding->candidate_id ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Client Budget</th>
                                    <td>{{ number_format($onboarding->client_budget, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Final Budget</th>
                                    <td>{{ number_format($onboarding->final_budget, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px;">Timesheet Link</th>
                                    <td>
                                        @if($onboarding->timesheet_link)
                                            <a href="{{ $onboarding->timesheet_link }}" target="_blank">View Timesheet</a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Delivery Manager</th>
                                    <td>{{ $onboarding->delivery_manager_name }}</td>
                                </tr>
                                <tr>
                                    <th>Start Date</th>
                                    <td>{{ $onboarding->start_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Billing Term</th>
                                    <td>{{ $onboarding->billing_term }}</td>
                                </tr>
                                <tr>
                                    <th>Cycle Date</th>
                                    <td>{{ $onboarding->cycle_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Project Type</th>
                                    <td>{{ ucfirst($onboarding->project_type) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('onboardings.edit', $onboarding->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('onboardings.destroy', $onboarding->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this onboarding?')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 