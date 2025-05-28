<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    
    use HasFactory, Notifiable;

    
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'active',
    ];

    
    protected $hidden = [
        'password',
        'remember_token',
    ];

    
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    
    public function getJWTCustomClaims()
    {
        return [];
    }

    
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    
    public function isMagasinier(): bool
    {
        return $this->hasRole('magasinier');
    }
}
