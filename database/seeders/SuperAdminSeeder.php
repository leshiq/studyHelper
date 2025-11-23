<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default superadmin only if it doesn't exist
        Student::firstOrCreate(
            ['email' => 'superadmin@studyhelper.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('superadmin'),
                'is_active' => true,
                'is_admin' => true,
                'is_superuser' => true,
                'must_change_credentials' => true,
            ]
        );
    }
}
