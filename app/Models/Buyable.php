<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Buyable extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_id',
        'price',
        'discount_price',
        'availabled_at',
    ];

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class, 'content_id');
    }

    public function courseDetail(): HasOne
    {
        return $this->hasOne(CourseDetail::class, 'buyable_id');
    }
}
