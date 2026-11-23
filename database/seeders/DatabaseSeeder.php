<?php

namespace Database\Seeders;

use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(['email' => 'admin@example.com'], [
            'name' => 'System Administrator',
            'password' => 'password',
        ]);

        foreach (['Student', 'Regular', 'Senior Citizen', 'Corporate'] as $name) {
            MembershipCategory::firstOrCreate(['name' => $name], ['description' => $name.' membership category']);
        }

        foreach ([
            ['name' => 'Basic', 'fee' => 500, 'validity_months' => 12, 'benefits' => 'Standard membership access'],
            ['name' => 'Silver', 'fee' => 1000, 'validity_months' => 12, 'benefits' => 'Standard access and member discounts'],
            ['name' => 'Gold', 'fee' => 1800, 'validity_months' => 12, 'benefits' => 'Priority access and expanded discounts'],
            ['name' => 'Platinum', 'fee' => 3000, 'validity_months' => 12, 'benefits' => 'All standard benefits and premium support'],
        ] as $level) {
            MembershipLevel::firstOrCreate(['name' => $level['name']], $level);
        }
    }
}
