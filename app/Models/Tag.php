<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
    ];

    protected $hidden = ['pivot'];

    public function contents(): BelongsToMany
    {
        return $this->belongsToMany(Content::class, 'content_tags', 'tag_id', 'content_id');
    }
}
