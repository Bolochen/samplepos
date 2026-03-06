<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'notransaction',
        'shift_id',
        'table_id',
        'user_id',
        'date',
        'subtotal',
        'tax',
        'service_charge',
        'discount',
        'total',
        'status',
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
