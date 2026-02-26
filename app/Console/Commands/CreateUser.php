<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a default staff user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = new \App\Models\User();
        $user->name = 'Staff User';
        $user->email = 'staff@example.com';
        $user->password = Hash::make('password123');
        $user->save();
        
        $this->info('Staff user created successfully!');
        $this->info('Email: staff@example.com');
        $this->info('Password: password123');
    }
}
