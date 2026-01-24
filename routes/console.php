<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('admin:create-super', function () {
    $existingCount = User::query()->where('user_type', 'super_admin')->count();

    if ($existingCount > 0) {
        $continue = $this->confirm('A super admin already exists. Create another one?', false);
        if (!$continue) {
            return 0;
        }
    }

    $firstName = (string) $this->ask('First name');
    $lastName = (string) $this->ask('Last name');
    $username = (string) $this->ask('Username');
    $email = (string) $this->ask('Email');
    $password = (string) $this->secret('Password');

    if ($firstName === '' || $lastName === '' || $username === '' || $email === '' || $password === '') {
        $this->error('All fields are required.');
        return 1;
    }

    if (User::query()->where('email', $email)->exists()) {
        $this->error('A user with this email already exists.');
        return 1;
    }

    if (User::query()->where('username', $username)->exists()) {
        $this->error('A user with this username already exists.');
        return 1;
    }

    User::query()->create([
        'uuid' => (string) Str::uuid(),
        'username' => $username,
        'email' => $email,
        'password' => Hash::make($password),
        'first_name' => $firstName,
        'last_name' => $lastName,
        'full_name' => trim($firstName . ' ' . $lastName),
        'user_type' => 'super_admin',
        'account_status' => 'active',
    ]);

    $this->info('Super admin created successfully.');
    return 0;
})->purpose('Create a super admin user safely from the CLI');
