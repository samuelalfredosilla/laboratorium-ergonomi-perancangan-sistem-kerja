<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'two_factor_code',
        'two_factor_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'     => 'datetime',
            'password'              => 'hashed',
            'two_factor_expires_at' => 'datetime',
        ];
    }

    /**
     * Cek apakah user memiliki role admin
     */
    public function isAdmin(): bool
    {
        return strtolower($this->role ?? '') === 'admin';
    }

    /**
     * Cek apakah user memiliki salah satu dari role yang ditentukan
     */
    public function hasRole(array|string $roles): bool
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }

        return in_array(strtolower($this->role ?? ''), array_map('strtolower', $roles));
    }
}
