@extends('layouts.app')
@section('styles')
    <style>
        .select2-container .select2-search--inline .select2-search__field{
            height: 21px !important;
        }
    </style>
@endsection
@section('title', 'Add New Requirement')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add New Requirement</h1>
        <a href="{{ route('requirements.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Requirements
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Requirement Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('requirements.store') }}" method="POST">
                @csrf

                <!-- Top Section -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="key_skills" class="form-label">Key Skills <span class="text-danger">*</span></label>
                        <select class="form-select select2 @error('key_skills') is-invalid @enderror" id="key_skills" name="key_skills[]" multiple required>
                            @foreach(App\Models\KeySkill::all() as $skill)
                                <option value="{{ $skill->id }}" {{ in_array($skill->id, old('key_skills', [])) ? 'selected' : '' }}>
                                    {{ $skill->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('key_skills')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="company_id" class="form-label">Company <span class="text-danger">*</span></label>
                        <select id="company_id" name="company_id" class="form-select @error('company_id') is-invalid @enderror" required>
                            <option value="">Select Company</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id', request()->get('company_id')) == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('company_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                        <select id="department_id" name="department_id" class="form-select @error('department_id') is-invalid @enderror" required >
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" data-percentage="{{ $department->percentage }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }} (HOD: {{ $department->hod->name ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="requirement_id" class="form-label">Requirement ID</label>
                        <input type="text" class="form-control" id="requirement_id" value="{{ $requirement_id }}" >
                        <div class="form-text">Requirement ID will be automatically generated</div>
                    </div>
                    <div class="col-md-6">
                        <label for="bde_name" class="form-label">BDE Name</label>
                        <input type="text" class="form-control"  name="bde_name" id="bde_name">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="job_description" class="form-label">Job Description <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('job_description') is-invalid @enderror" id="job_description" name="job_description" rows="6" required>{{ old('job_description') }}</textarea>
                    @error('job_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-4">

                <!-- Bottom Section -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="client_budget" class="form-label">Client Budget <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('client_budget') is-invalid @enderror" id="client_budget" name="client_budget" value="{{ old('client_budget') }}" required>
                        @error('client_budget')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="final_budget" class="form-label">Final Budget</label>
                        <input type="text" class="form-control" id="final_budget" name="final_budget" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="show_budget_to_vendor" name="show_budget_to_vendor" value="1">
                            <label class="form-check-label" for="show_budget_to_vendor">
                                Show Final Budget for Vendor
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="custom_percentage_check">
                            <label class="form-check-label" for="custom_percentage_check">
                                Custom Percentage
                            </label>
                        </div>
                        <div id="percentage_input_container" class="mt-2" style="display: none;">
                            <input type="number" class="form-control" id="custom_percentage" min="0" max="100" step="0.01">
                        </div>
                    </div>
                </div>

                <input type="hidden" name="needs_hod_approval" id="needs_hod_approval" value="0">
                <input type="hidden" name="custom_percentage_value" id="custom_percentage_value">

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <button type="reset" class="btn btn-secondary me-md-2">Reset</button>
                    <button type="submit" class="btn btn-primary">Submit Requirement</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: 'Select Key Skills',
            allowClear: true,
            width: '100%'
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const clientBudgetInput = document.getElementById('client_budget');
        const departmentSelect = document.getElementById('department_id');
        const finalBudgetInput = document.getElementById('final_budget');
        const customPercentageCheck = document.getElementById('custom_percentage_check');
        const customPercentageInput = document.getElementById('custom_percentage');
        const percentageInputContainer = document.getElementById('percentage_input_container');
        const needsHodApprovalInput = document.getElementById('needs_hod_approval');
        const customPercentageValueInput = document.getElementById('custom_percentage_value');

        function calculateFinalBudget() {
            const clientBudget = parseFloat(clientBudgetInput.value) || 0;
            let percentage = 0;

            if (customPercentageCheck.checked) {
                percentage = parseFloat(customPercentageInput.value) || 0;
                needsHodApprovalInput.value = "1";
                customPercentageValueInput.value = percentage;
            } else {
                const selectedOption = departmentSelect.options[departmentSelect.selectedIndex];
                percentage = selectedOption ? parseFloat(selectedOption.dataset.percentage) || 0 : 0;
                needsHodApprovalInput.value = "0";
                customPercentageValueInput.value = "";
            }
            
            if (clientBudget > 0 && percentage > 0) {
                const finalBudget = (clientBudget * percentage) / 100;
                finalBudgetInput.value = finalBudget;
            } else {
                finalBudgetInput.value = '';
            }
        }

        function updateCustomPercentage() {
            const selectedOption = departmentSelect.options[departmentSelect.selectedIndex];
            const defaultPercentage = selectedOption ? parseFloat(selectedOption.dataset.percentage) || 0 : 0;
            customPercentageInput.value = defaultPercentage;
            calculateFinalBudget();
        }

        clientBudgetInput.addEventListener('input', function(e) {
            if (this.value && this.value > 0) {
                departmentSelect.removeAttribute('disabled');
            } else {
                departmentSelect.setAttribute('disabled', 'disabled');
                departmentSelect.value = ''; // Reset department selection when budget is empty
            }
            calculateFinalBudget();
        });

        departmentSelect.addEventListener('change', function() {
            updateCustomPercentage();
            calculateFinalBudget();
        });

        customPercentageCheck.addEventListener('change', function() {
            percentageInputContainer.style.display = this.checked ? 'block' : 'none';
            if (this.checked) {
                updateCustomPercentage();
            }
            calculateFinalBudget();
        });

        customPercentageInput.addEventListener('input', calculateFinalBudget);
    });
</script>
@endsection
