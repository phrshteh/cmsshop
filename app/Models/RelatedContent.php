<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelatedContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_id',
        'related_id',
    ];
}
