# Billing Module

A comprehensive billing management system for Laravel that automatically generates monthly billing for onboarded candidates based on working days, leaves, and salary calculations.

## Features

- **Monthly Billing Generation**: Automatically generates billing records for all active onboardings
- **Month-wise Filtering**: View billing data for any specific month with calendar filter
- **DataTable Integration**: Responsive table with search, pagination, and sorting
- **Status Management**: Pending → Approved → Paid workflow
- **Export Functionality**: Export billing data to CSV format
- **CRON Job Integration**: Automated monthly billing generation
- **Detailed Calculations**: Shows working days, leave days, per-day salary, and monthly salary

## Installation

### 1. Run Migration
```bash
php artisan migrate
```

### 2. Seed Permissions
```bash
php artisan db:seed --class=BillingPermissionSeeder
```

### 3. Set up CRON Job (Optional)
Add the following to your server's crontab to run monthly billing generation:
```bash
# Run monthly billing generation on the last day of each month at 11:59 PM
59 23 28-31 * * [ "$(date +\%d -d tomorrow)" = "01" ] && cd /path/to/your/project && php artisan billing:generate-monthly
```

Or add this to your Laravel scheduler in `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule)
{
    // Run monthly billing generation on the last day of each month
    $schedule->command('billing:generate-monthly')
             ->monthlyOn(date('t'), '23:59')
             ->withoutOverlapping();
}
```

## Usage

### Accessing the Billing Module
1. Navigate to the billing section in the sidebar
2. The module will show current month's billing by default
3. Use the month filter to view billing for different months

### Billing Workflow
1. **Pending**: New billing records start with 'pending' status
2. **Approved**: Managers can approve billing records
3. **Paid**: Approved billing can be marked as paid
4. **Rejected**: Billing can be rejected with remarks

### Manual Billing Generation
To manually generate billing for a specific month:
```bash
php artisan billing:generate-monthly --month=12 --year=2024
```

## Calculation Logic

### Per Day Salary Calculation
```
Per Day Salary = Final Budget (from onboarding) ÷ 22 working days
```

### Monthly Salary Calculation
```
Monthly Salary = (Total Working Days - Total Leave Days) × Per Day Salary
```

### Working Days Calculation
- Only Monday to Friday are considered working days
- Weekends (Saturday, Sunday) are excluded
- Leaves are deducted from working days

## Database Schema

### Billings Table
- `onboarding_id`: Reference to onboarding record
- `requirement_id`: Reference to requirement
- `vendor_id`: Reference to vendor
- `candidate_id`: Reference to candidate
- `month`: Month number (1-12)
- `year`: Year
- `total_working_days`: Total working days in the month
- `total_leave_days`: Total leave days taken
- `net_working_days`: Working days minus leave days
- `per_day_salary`: Calculated per day salary
- `monthly_salary`: Final monthly salary
- `status`: pending/approved/paid/rejected
- `approved_at`: Timestamp when approved
- `approved_by`: User who approved
- `paid_at`: Timestamp when marked as paid
- `paid_by`: User who marked as paid
- `remarks`: Rejection remarks

## Permissions

The following permissions are required:

- `view-billing`: View billing list
- `view-billing-details`: View individual billing details
- `approve-billing`: Approve billing records
- `reject-billing`: Reject billing records
- `mark-billing-paid`: Mark billing as paid
- `export-billing`: Export billing data

## API Endpoints

### Web Routes
- `GET /billing` - Billing index page
- `GET /billing/{billing}` - Show billing details
- `PATCH /billing/{billing}/approve` - Approve billing
- `PATCH /billing/{billing}/reject` - Reject billing
- `PATCH /billing/{billing}/mark-as-paid` - Mark as paid
- `GET /billing/export` - Export billing data

### Console Commands
- `php artisan billing:generate-monthly` - Generate monthly billing

## Files Structure

```
app/
├── Models/
│   └── Billing.php
├── Http/Controllers/
│   └── BillingController.php
├── Console/Commands/
│   └── GenerateMonthlyBilling.php
├── Jobs/
│   └── GenerateMonthlyBillingJob.php
└── database/
    ├── migrations/
    │   └── 2025_01_15_000000_create_billings_table.php
    └── seeders/
        └── BillingPermissionSeeder.php

resources/views/
└── billing/
    ├── index.blade.php
    └── show.blade.php
```

## Troubleshooting

### Common Issues

1. **No billing records generated**
   - Check if onboardings have `final_budget` set
   - Verify onboardings have 'active' status
   - Check if billing already exists for the month

2. **Permission errors**
   - Run the permission seeder: `php artisan db:seed --class=BillingPermissionSeeder`
   - Assign appropriate permissions to user roles

3. **CRON job not working**
   - Verify the command exists: `php artisan list | grep billing`
   - Check Laravel scheduler is running
   - Review logs in `storage/logs/laravel.log`

### Logs
Billing generation activities are logged in:
- `storage/logs/laravel.log`

## Support

For issues or questions regarding the billing module, please check:
1. Laravel logs for error messages
2. Database permissions and roles
3. CRON job configuration
4. Onboarding data integrity 