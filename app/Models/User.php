<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Intern;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password','role','is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' =>'boolean',
        ];
    }
    public function intern(): HasOne
{
    return $this->hasOne(Intern::class);
}

public function canAccessPanel(
    \Filament\Panel $panel
): bool {

    if ($panel->getId() === 'admin') {
        return $this->role === 'admin';
    }

    if ($panel->getId() === 'app') {
        return $this->role === 'intern';
    }

    return false;
}
}