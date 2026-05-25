<?php

namespace App\Filament\Admin\Resources\Interns\Pages;

use App\Filament\Admin\Resources\Interns\InternResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Intern;

class CreateIntern extends CreateRecord
{
    protected static string $resource = InternResource::class;

    protected function handleRecordCreation(array $data): Model 
    {
        $userData = $data['user'] ?? [];
        $internData = collect($data)->except('user')->toArray();

        $user = User::create([

        'name' => $internData['name'] ?? $userData['name'] ?? 'Intern',
        'email' => $userData['email'] ?? 'null',
        'password' => bcrypt($userData['password'] ?? 'password'),
        'is_active' => $userData['is_active'],
        'role' => 'intern'

        ]);
        $internData['user_id'] = $user->id;
        return Intern::create($internData);
    }

    protected function getRedirectUrl(): string 
    {
        return $this->getResource()::getUrl('index');
    }
}
