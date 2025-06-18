<?php

namespace App\Jobs;

use App\Http\Controllers\BillingController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateMonthlyBillingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes timeout

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Starting monthly billing generation job...');
            
            $billingController = new BillingController();
            $generatedCount = $billingController->generateMonthlyBilling();
            
            Log::info("Monthly billing generation job completed. Generated {$generatedCount} records.");
        } catch (\Exception $e) {
            Log::error('Monthly billing generation job failed: ' . $e->getMessage());
            throw $e;
        }
    }
} 