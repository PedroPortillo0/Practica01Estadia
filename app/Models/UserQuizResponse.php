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
        'country',
        'religious_belief',
        'spiritual_practice_level',
        'spiritual_practice_frequency',
        'daily_challenges',
        'stoic_paths',
        'stoic_level',
        'completed_at'
    ];

    protected $casts = [
        'daily_challenges' => 'array',
        'stoic_paths' => 'array',
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
