<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateStaffReferralCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-staff-referral-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update referral codes for existing staff members';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $staffMembers = \App\Models\Staff::whereNull('referral_code')->get();
        
        foreach ($staffMembers as $staff) {
            // Generate a unique referral code
            do {
                $referralCode = 'STAFF' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
            } while (\App\Models\Staff::where('referral_code', $referralCode)->exists());
            
            $staff->referral_code = $referralCode;
            $staff->save();
            
            $this->info("Referral code {$referralCode} assigned to staff member: {$staff->name}");
        }
        
        $this->info('All staff members have been assigned referral codes.');
    }
}
