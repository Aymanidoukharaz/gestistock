<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExitItem extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'exit_form_id',
        'product_id',
        'quantity',
    ];

    
    protected $casts = [
        'quantity' => 'integer',
    ];

    
    public function exitForm(): BelongsTo
    {
        return $this->belongsTo(ExitForm::class);
    }

    
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
