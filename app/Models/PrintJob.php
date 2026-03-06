<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrintJob extends Model
{
    protected $fillable = [
        'transaction_item_id',
        'type',
        'status',
        'printer_name',
        'printed_at',
    ];

    public function transactionItem()
    {
        return $this->belongsTo(TransactionItem::class);
    }
}
