<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateStaffTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-staff-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create staff table directly';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating staff table...');
        
        try {
            DB::statement("CREATE TABLE IF NOT EXISTS staff (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                referral_code VARCHAR(255) UNIQUE NOT NULL,
                role VARCHAR(255) DEFAULT 'staff',
                remember_token VARCHAR(100) NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            
            $this->info('Staff table created successfully!');
            
            // Create a default admin user
            $password = bcrypt('password');
            DB::statement("INSERT IGNORE INTO staff (name, email, password, referral_code, role, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())", [
                'Admin User',
                'admin@example.com',
                $password,
                'ADMIN001',
                'admin'
            ]);
            
            $this->info('Default admin user created: admin@example.com / password');
        } catch (\Exception $e) {
            $this->error('Error creating staff table: ' . $e->getMessage());
        }
    }
}
