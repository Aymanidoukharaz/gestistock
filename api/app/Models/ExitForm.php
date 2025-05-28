<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class ExitForm extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'reference',
        'date',
        'destination',
        'reason',
        'notes',
        'status',
        'user_id',
    ];

    
    protected $casts = [
        'date' => 'date',
    ];

        public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    
    public function items(): HasMany
    {
        return $this->hasMany(ExitItem::class);
    }
    
    
    public function exitItems(): HasMany
    {
        return $this->items();
    }
    
    
    public function histories(): HasMany
    {
        return $this->hasMany(ExitFormHistory::class);
    }
}
