<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
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
        'email',
        'password',
        'role',
        'active',
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array<string, mixed>
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Vérifie si l'utilisateur a un rôle spécifique
     *
     * @param string $role Le rôle à vérifier ('admin' ou 'magasinier')
     * @return bool Retourne vrai si l'utilisateur a le rôle spécifié
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Vérifie si l'utilisateur est un administrateur
     *
     * @return bool Retourne vrai si l'utilisateur est admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Vérifie si l'utilisateur est un magasinier
     *
     * @return bool Retourne vrai si l'utilisateur est magasinier
     */
    public function isMagasinier(): bool
    {
        return $this->hasRole('magasinier');
    }
}
