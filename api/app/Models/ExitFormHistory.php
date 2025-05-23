<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExitFormHistory extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'exit_form_id',
        'user_id',
        'field_name',
        'old_value',
        'new_value',
        'change_reason',
    ];

    /**
     * Relation avec le bon de sortie.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function exitForm(): BelongsTo
    {
        return $this->belongsTo(ExitForm::class);
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
