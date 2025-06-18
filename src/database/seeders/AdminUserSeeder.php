<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AdminUser;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $param = [
            'name' => 'admin',
            'email' => 'example@test.com',
            'password' => 'password',
        ];
        $adminUser = new AdminUser;
        $adminUser->fill($param)->save();
    }
}
