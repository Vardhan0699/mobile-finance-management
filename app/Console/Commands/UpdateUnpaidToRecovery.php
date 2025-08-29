<?php

namespace App\Console\Commands;

use App\Models\EmiSchedule;
use App\Models\Customer;
use App\Models\Admin;
use App\Models\Pincode;
use App\Models\Recovery;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateUnpaidToRecovery extends Command
{
  /**
     * The name and signature of the console command.
     */
  protected $signature = 'status:update-unpaid';

  /**
     * The console command description.
     */
  protected $description = 'Update status from unpaid to recovery if emi_date is more than 1 days old';

  /**
     * Execute the console command.
     */
  public function handle()
  {
    // Log::info("🔥 CRON: status:update-unpaid started at " . now());
    // $this->info("🔥 CRON: status:update-unpaid started at " . now());
        
    $cutoffDate = Carbon::yesterday()->toDateString(); 

    $emis = EmiSchedule::where('status', 'unpaid')
      ->whereDate('emi_date', '<=', $cutoffDate)
      ->get();

    $updatedCount = 0;
    $insertedCount = 0;

    foreach ($emis as $emi) {
      $emi->status = 'recovery';
      $emi->late_fees = 195;
      $emi->save();
      $updatedCount++;

      $customer = Customer::find($emi->customer_id);
      if (!$customer) {
       //  $this->info("❌ Customer not found for EMI ID {$emi->id}");
        continue;
      }

      // $this->info("✅ Found Customer ID {$customer->id}");

      $approvePincode = Pincode::where('pincode', $customer->pincode)->first();
      if (!$approvePincode) {
    //     $this->info("❌ No approve_pincode found for pincode {$customer->pincode}");
        continue;
      }

    //   $this->info("✅ Found ApprovePincode ID {$approvePincode->id}");

      $admin = Admin::whereJsonContains('zipcode', (string) $approvePincode->id)->first();
      if (!$admin) {
    //     $this->info("❌ No Admin found managing pincode ID {$approvePincode->id}");
        continue;
      }

    //   $this->info("✅ Found Admin ID {$admin->id}");

      try {
        Recovery::create([
          'loan_id' => $emi->loan_id,
          'emi_schedule_id' => $emi->id,
          'staff_id' => $admin->id,
          'created_at' => now(),
          'updated_at' => now(),
        ]);

        $insertedCount++;
    //     $this->info("✅ Inserted Recovery for loan_id {$emi->loan_id}");

      } catch (\Exception $e) {
        //  Log::error("Recovery insert failed for loan_id {$emi->loan_id}: " . $e->getMessage());
        $this->error("❌ Insert failed: " . $e->getMessage());
      }
    }

   //  $this->info("🎯 Updated $updatedCount EMI records to 'recovery'.");
   //  $this->info("🎯 Inserted $insertedCount records into 'recovery' table.");
  }

}