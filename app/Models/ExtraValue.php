<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExtraValue extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'extra_field_id',
        'content_id',
        'value',
    ];

    protected $appends = [
        'media_url'
    ];

    public function extraField(): BelongsTo
    {
        return $this->belongsTo(ExtraField::class, 'extra_field_id');
    }

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class, 'content_id');
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'value');
    }

    public function getMediaUrlAttribute()
    {
        return $this->media()->first()?->url;
    }
}
