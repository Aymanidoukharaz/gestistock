<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class StockMovement extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'reason',
        'date',
        'user_id',
    ];

    
    protected $casts = [
        'quantity' => 'integer',
        'date' => 'datetime',
    ];

    
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
