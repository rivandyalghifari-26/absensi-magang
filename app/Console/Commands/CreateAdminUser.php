<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    protected $signature = 'make:admin';
    protected $description = 'Membuat akun admin baru secara interaktif';

    public function handle(): int
    {
        $this->info('--- Buat Akun Admin Baru ---');

        $name = $this->ask('Masukkan Nama Lengkap Admin');
        while (empty($name)) {
            $this->error('Nama tidak boleh kosong!');
            $name = $this->ask('Masukkan Nama Lengkap Admin');
        }

        $email = $this->ask('Masukkan Email');
        $validator = Validator::make(['email' => $email], [
            'email' => ['required', 'email', 'unique:users,email'],
        ]);

        while ($validator->fails()) {
            $this->error($validator->errors()->first('email'));
            $email = $this->ask('Masukkan Email');
            $validator = Validator::make(['email' => $email], [
                'email' => ['required', 'email', 'unique:users,email'],
            ]);
        }

        $password = $this->secret('Masukkan Password');
        while (strlen($password) < 6) {
            $this->error('Password minimal 6 karakter!');
            $password = $this->secret('Masukkan Password');
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->info("Berhasil! Akun Admin [{$user->email}] telah dibuat.");

        return Command::SUCCESS;
    }
}