<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserQuizResponse extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'age_range',
        'gender',
        'sexual_orientation',
        'state',
        'religious_belief',
        'spiritual_practice_level',
        'spiritual_practice_frequency',
        'stoic_values',
        'life_purpose',
        'happiness_source',
        'adversity_response',
        'life_development_area',
        'completed_at'
    ];

    protected $casts = [
        'stoic_values' => 'array',
        'completed_at' => 'datetime'
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    // Relación con User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
