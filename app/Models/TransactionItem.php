<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    protected $fillable = [
        'transaction_id',
        'menu_item_id',
        'quantity',
        'price',
        'kitchen_status',
        'serving_status',
        'type',
        'notes',
        'bill_id',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function menuItem()
    {
        return $this->belongsTo(Menu::class, 'menu_item_id');
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
