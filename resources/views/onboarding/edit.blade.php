@extends('layouts.app')

@section('title', 'Edit Onboarding')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Onboarding</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('onboardings.update', $onboarding->id) }}" method="POST" id="onboardingEditForm">
                        @csrf
                        @method('PUT')
                        {{-- <input type="hidden" name="requirement_id" value="{{ $onboarding->requirement_id }}">
                        <input type="hidden" name="candidate_id" value="{{ $onboarding->candidate_id }}"> --}}

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="requirement_id" class="form-label">Requirement ID</label>
                                <input type="text" class="form-control" name="requirement_id" value="{{ $onboarding->requirement->requirement_id ?? $onboarding->requirement_id ?? '' }}" >
                            </div>
                            <div class="col-md-6">
                                <label for="vendor_id" class="form-label">Vendor Name</label>
                                <select class="form-control @error('vendor_id') is-invalid @enderror" name="vendor_id" id="vendor_id" >
                                    <option value="">Select Vendor</option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}" {{ $onboarding->vendor_id == $vendor->id ? 'selected' : '' }}>
                                            {{ $vendor->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('vendor_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="candidate_name" class="form-label">Candidate Name</label>
                                <input type="text" class="form-control" name="candidate_id" value="{{ $onboarding->candidate->candidate_name ?? $onboarding->candidate_id ?? ''  }}" >
                            </div>
                            <div class="col-md-6">
                                <label for="client_budget" class="form-label">Client Budget</label>
                                <input type="number" step="0.01" class="form-control @error('client_budget') is-invalid @enderror" name="client_budget" value="{{ old('client_budget', $onboarding->client_budget) }}" >
                                @error('client_budget')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="final_budget" class="form-label">Final Budget</label>
                                <input type="number" step="0.01" class="form-control @error('final_budget') is-invalid @enderror" name="final_budget" value="{{ old('final_budget', $onboarding->final_budget) }}" >
                                @error('final_budget')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="timesheet_link" class="form-label">Timesheet Link</label>
                                <input type="url" class="form-control @error('timesheet_link') is-invalid @enderror" name="timesheet_link" value="{{ old('timesheet_link', $onboarding->timesheet_link) }}">
                                @error('timesheet_link')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="delivery_manager_name" class="form-label">Delivery Manager Name</label>
                                <select class="form-select @error('delivery_manager_name') is-invalid @enderror" name="delivery_manager_name" required>
                                    <option value="">Select Delivery Manager</option>
                                    @foreach($delivery_managers as $manager)
                                        <option value="{{ $manager->name }}" {{ old('delivery_manager_name', $onboarding->delivery_manager_name) == $manager->name ? 'selected' : '' }}>
                                            {{ $manager->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('delivery_manager_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" name="start_date" value="{{ old('start_date', $onboarding->start_date ? $onboarding->start_date->format('Y-m-d') : '') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="billing_term" class="form-label">Billing Term</label>
                                <input type="text" class="form-control @error('billing_term') is-invalid @enderror" name="billing_term" value="{{ old('billing_term', $onboarding->billing_term) }}" placeholder="30 days" required>
                                @error('billing_term')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="cycle_date" class="form-label">Cycle Date</label>
                                <input type="date" class="form-control @error('cycle_date') is-invalid @enderror" name="cycle_date" value="{{ old('cycle_date', $onboarding->cycle_date ? $onboarding->cycle_date->format('Y-m-d') : '') }}" required>
                                @error('cycle_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="project_type" class="form-label">Project Type</label>
                                <select class="form-select @error('project_type') is-invalid @enderror" name="project_type" required>
                                    <option value="">Select Project Type</option>
                                    <option value="hourly" {{ old('project_type', $onboarding->project_type) == 'hourly' ? 'selected' : '' }}>Hourly</option>
                                    <option value="monthly" {{ old('project_type', $onboarding->project_type) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                </select>
                                @error('project_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="Yet to Start" {{ old('status', $onboarding->status) == 'Yet to Start' ? 'selected' : '' }}>Yet to Start</option>
                                    <option value="Running" {{ old('status', $onboarding->status) == 'Running' ? 'selected' : '' }}>Running</option>
                                    <option value="Hold" {{ old('status', $onboarding->status) == 'Hold' ? 'selected' : '' }}>Hold</option>
                                    <option value="Stopped" {{ old('status', $onboarding->status) == 'Stopped' ? 'selected' : '' }}>Stopped</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button type="submit" class="btn btn-primary">Update Onboarding</button>
                            <a href="{{ route('onboardings.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 