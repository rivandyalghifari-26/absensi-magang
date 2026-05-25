<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Intern;

class InternSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $names = [
            'Taesan',
            'Leehan',
            'Jay',
            'Carmen',
            'Zain',
            'Michelle',
            'Madison',
            'Putra',
            'Laufey',
            'Eric',
            'Anton',
            'Jacob',
            'Louis',
            'Darwin',
            'Eko',
            'Grace',
            'Sean',
            'Martin',
            'James',
            'Leon S Kennedy'

        ];
        $departments = [
            'it',
            'finance',
            'hr',
            'operations'
        ];

        foreach ($names as $index => $name) {
            $email = strtolower($name) . '@gmail.com';

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt('password'),
                'is_active' => true,
                'role' => 'intern',
            ]);

            Intern::create([
                'user_id' => $user->id,
                'intern_code' => 'MA-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'phone' => '0821' . rand(10000000,99999999),
                'position' => 'Magang' . ucfirst($departments[$index % count($departments)]),
                'department' => $departments[$index % count($departments)],
                'status' => 'active',
            ]);
        }
    }
}
