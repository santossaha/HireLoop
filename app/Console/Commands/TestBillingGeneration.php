<?php

namespace App\Console\Commands;

use App\Http\Controllers\BillingController;
use App\Models\Onboarding;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestBillingGeneration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:test-generation {--onboarding-id= : Specific onboarding ID to test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test billing generation for debugging data issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting billing generation test...');

        try {
            $onboardingId = $this->option('onboarding-id');
            
            if ($onboardingId) {
                // Test specific onboarding
                $onboarding = Onboarding::where('id', $onboardingId)
                    ->where('status', '!=', 'Stopped')
                    ->whereNotNull('final_budget')
                    ->with(['requirement', 'vendor', 'candidate'])
                    ->first();

                if (!$onboarding) {
                    $this->error("Onboarding with ID {$onboardingId} not found or not eligible for billing.");
                    return 1;
                }

                $this->info("Testing billing generation for onboarding ID: {$onboardingId}");
                $this->info("Requirement ID: " . ($onboarding->requirement->requirement_id ?? 'NULL'));
                $this->info("Vendor Name: " . ($onboarding->vendor->name ?? 'NULL'));
                $this->info("Candidate Name: " . ($onboarding->candidate->candidate_name ?? 'NULL'));
                $this->info("Final Budget: " . ($onboarding->final_budget ?? 'NULL'));

                $billingController = new BillingController();
                $billingData = $this->invokePrivateMethod($billingController, 'calculateBillingData', [$onboarding, now()->month, now()->year]);

                $this->info("Generated billing data:");
                $this->table(['Field', 'Value'], [
                    ['Requirement ID', $billingData['requirement_id']],
                    ['Vendor Name', $billingData['vendor_name']],
                    ['Candidate Name', $billingData['candidate_name']],
                    ['Final Budget', $billingData['final_budget']],
                    ['Total Working Days', $billingData['total_working_days']],
                    ['Monthly Salary', $billingData['monthly_salary']],
                ]);

            } else {
                // Test general generation
                $billingController = new BillingController();
                $generatedCount = $billingController->generateMonthlyBilling();

                $this->info("Successfully generated {$generatedCount} billing records.");
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('Error testing billing generation: ' . $e->getMessage());
            Log::error('Billing test generation failed: ' . $e->getMessage());
            
            return 1;
        }
    }

    private function invokePrivateMethod($object, $methodName, $parameters = [])
    {
        $reflection = new \ReflectionClass($object);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }
} 