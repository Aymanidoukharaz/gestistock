<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reference',
        'name',
        'description',
        'category_id',
        'price',
        'quantity',
        'min_stock',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'min_stock' => 'integer',
    ];

    /**
     * Relation avec la catégorie.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relation avec les mouvements de stock.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Relation avec les items de bon d'entrée.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entryItems(): HasMany
    {
        return $this->hasMany(EntryItem::class);
    }

    /**
     * Relation avec les items de bon de sortie.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function exitItems(): HasMany
    {
        return $this->hasMany(ExitItem::class);
    }
}
