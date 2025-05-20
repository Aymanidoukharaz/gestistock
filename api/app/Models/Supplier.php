<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'notes',
    ];

    /**
     * Relation avec les bons d'entrée.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entryForms(): HasMany
    {
        return $this->hasMany(EntryForm::class);
    }
}
