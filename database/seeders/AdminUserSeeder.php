<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'Rafalima277@gmail.com'],
            [
                'name'     => 'Rafael Lima',
                'password' => Hash::make('152164@Ab'),
                'is_admin' => true, // se seu schema tiver essa coluna
            ]
        );
    }
}
