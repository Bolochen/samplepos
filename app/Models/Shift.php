<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = [
        'user_id',
        'start_time',
        'end_time',
        'opening_cash',
        'closing_cash',
        'expected_cash',
        'difference',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
