<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'price',
        'image',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
