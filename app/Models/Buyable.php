<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buyable extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_id',
        'price',
        'discount_price',
        'availabled_at',
    ];
}
