<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyable_id',
        'capacity',
        'quorum',
        'finished_at'
    ];

    public function buyable(): BelongsTo
    {
        return $this->belongsTo(Buyable::class, 'buyable_id');
    }
}
