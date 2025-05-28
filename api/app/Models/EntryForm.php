<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class EntryForm extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'reference',
        'date',
        'supplier_id',
        'notes',
        'status',
        'total',
        'user_id',
    ];

    
    protected $casts = [
        'date' => 'date',
        'total' => 'decimal:2',
    ];

    
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

        public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    
    public function items(): HasMany
    {
        return $this->hasMany(EntryItem::class);
    }
    
    
    public function entryItems(): HasMany
    {
        return $this->items();
    }
    
    
    public function histories(): HasMany
    {
        return $this->hasMany(EntryFormHistory::class);
    }
}
