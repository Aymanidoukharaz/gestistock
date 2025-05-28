<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExitFormHistory extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'exit_form_id',
        'user_id',
        'field_name',
        'old_value',
        'new_value',
        'change_reason',
    ];

    
    public function exitForm(): BelongsTo
    {
        return $this->belongsTo(ExitForm::class);
    }

    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
