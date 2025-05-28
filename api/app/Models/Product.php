<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'reference',
        'name',
        'description',
        'category_id',
        'price',
        'quantity',
        'min_stock',
    ];

    
    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'min_stock' => 'integer',
    ];

    
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    
    public function entryItems(): HasMany
    {
        return $this->hasMany(EntryItem::class);
    }

    
    public function exitItems(): HasMany
    {
        return $this->hasMany(ExitItem::class);
    }
}
