<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPersonalizedQuote extends Model
{
    protected $table = 'user_personalized_quotes';

    protected $fillable = [
        'user_id',
        'date',
        'personalized_quote',
        'explanation',
        'original_quote_id',
        'day_of_year',
        'original_author',
        'original_category',
    ];

    protected $casts = [
        'date' => 'date',
        'day_of_year' => 'integer',
    ];

    /**
     * Relación con el usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con la frase original
     */
    public function originalQuote(): BelongsTo
    {
        return $this->belongsTo(DailyQuote::class, 'original_quote_id');
    }
}

