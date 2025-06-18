<?php

namespace App\Console\Commands;

use App\Http\Controllers\BillingController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateMonthlyBilling extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:generate-monthly {--month= : Specific month (1-12)} {--year= : Specific year}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly billing for all active onboardings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting monthly billing generation...');

        try {
            $billingController = new BillingController();
            $generatedCount = $billingController->generateMonthlyBilling();

            $this->info("Successfully generated {$generatedCount} billing records.");
            Log::info("Monthly billing generation completed. Generated {$generatedCount} records.");

            return 0;
        } catch (\Exception $e) {
            $this->error('Error generating monthly billing: ' . $e->getMessage());
            Log::error('Monthly billing generation failed: ' . $e->getMessage());
            
            return 1;
        }
    }
} 