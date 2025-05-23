<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntryFormHistory extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'entry_form_id',
        'user_id',
        'field_name',
        'old_value',
        'new_value',
        'change_reason',
    ];

    /**
     * Relation avec le bon d'entrée.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entryForm(): BelongsTo
    {
        return $this->belongsTo(EntryForm::class);
    }

    /**
     * Relation avec l'utilisateur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
