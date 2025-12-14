<?php

namespace Modules\ProjectManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $userData = [
              [
                'name' => 'Mustafa Salama',
                'username' => 'mustafa.salama',
                'email' => 'test@test.com',
                'mobile' => '+201552923438'
            ],
            [
                'name' => 'Ahmed Al-Rashid',
                'username' => 'ahmed.rashid',
                'email' => 'ahmed.rashid@example.com',
                'mobile' => '+966501234567'
            ],
            [
                'name' => 'Fatima Al-Zahra',
                'username' => 'fatima.zahra',
                'email' => 'fatima.zahra@example.com',
                'mobile' => '+966501234568'
            ],
            [
                'name' => 'Mohammed Al-Mansouri',
                'username' => 'mohammed.mansouri',
                'email' => 'mohammed.mansouri@example.com',
                'mobile' => '+966501234569'
            ],
            [
                'name' => 'Nora Al-Khalil',
                'username' => 'nora.khalil',
                'email' => 'nora.khalil@example.com',
                'mobile' => '+966501234570'
            ],
            [
                'name' => 'Omar Al-Farisi',
                'username' => 'omar.farisi',
                'email' => 'omar.farisi@example.com',
                'mobile' => '+966501234571'
            ],
            [
                'name' => 'Aisha Al-Sabah',
                'username' => 'aisha.sabah',
                'email' => 'aisha.sabah@example.com',
                'mobile' => '+966501234572'
            ],
            [
                'name' => 'Khalid Al-Otaibi',
                'username' => 'khalid.otaibi',
                'email' => 'khalid.otaibi@example.com',
                'mobile' => '+966501234573'
            ],
            [
                'name' => 'Mariam Al-Zahrani',
                'username' => 'mariam.zahrani',
                'email' => 'mariam.zahrani@example.com',
                'mobile' => '+966501234574'
            ],
            [
                'name' => 'Saad Al-Ghamdi',
                'username' => 'saad.ghamdi',
                'email' => 'saad.ghamdi@example.com',
                'mobile' => '+966501234575'
            ],
            [
                'name' => 'Layla Al-Harbi',
                'username' => 'layla.harbi',
                'email' => 'layla.harbi@example.com',
                'mobile' => '+966501234576'
            ],
            [
                'name' => 'Yusuf Al-Shehri',
                'username' => 'yusuf.shehri',
                'email' => 'yusuf.shehri@example.com',
                'mobile' => '+966501234577'
            ],
            [
                'name' => 'Zainab Al-Mutairi',
                'username' => 'zainab.mutairi',
                'email' => 'zainab.mutairi@example.com',
                'mobile' => '+966501234578'
            ]
        ];

        foreach ($userData as $data) {
            $existingUser = User::where('email', $data['email'])->first();

            if (!$existingUser) {
                User::create([
                    'name' => $data['name'],
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'mobile' => $data['mobile'],
                    'password' => Hash::make('password123'),
                    'status' => true
                ]);

                $this->command->info("Created user: " . $data['name']);
            } else {
                $this->command->warn("User already exists: " . $data['email']);
            }
        }

        $totalUsers = User::count();
        $this->command->info("Total users in system: {$totalUsers}");
    }
}
