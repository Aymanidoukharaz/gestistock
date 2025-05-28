<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntryItem extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'entry_form_id',
        'product_id',
        'quantity',
        'unit_price',
        'total',
    ];

    
    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    
    public function entryForm(): BelongsTo
    {
        return $this->belongsTo(EntryForm::class);
    }

    
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
