<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyQuote extends Model
{
    protected $fillable = [
        'quote',
        'author',
        'category',
        'day_of_year',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'day_of_year' => 'integer'
    ];
}
