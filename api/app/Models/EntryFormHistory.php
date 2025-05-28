<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntryFormHistory extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'entry_form_id',
        'user_id',
        'field_name',
        'old_value',
        'new_value',
        'change_reason',
    ];

    
    public function entryForm(): BelongsTo
    {
        return $this->belongsTo(EntryForm::class);
    }

    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
