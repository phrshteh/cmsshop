<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Content extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'description',
        'category_id',
        'cover_id',
        'intro',
    ];

    /**
     * Get the name of the index associated with the model.
     *
     * @return string
     */
    public function searchableAs(): string
    {
        return 'description';
    }

    protected $hidden = ['pivot'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function extraValues(): HasMany
    {
        return $this->hasMany(ExtraValue::class, 'content_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'content_tags', 'content_id', 'tag_id');
    }

    public function cover(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'content_id')->where('parent_id', null)
            ->where('approved_at', '<>', null);
    }

    public function relatedContents(): BelongsToMany
    {
        return $this->belongsToMany(Content::class, 'related_contents', 'content_id', 'related_id');
    }

    public function getExtraFieldsAttribute()
    {
        return $this->category()->first()->extraFields()->get()->mapWithKeys(function ($extraField, $extraValue) {
            return [
                $extraField->key => [
                    'id'    => $extraField->id,
                    'title' => $extraField->title,
                    'value' => $extraField->extraValues()->where('content_id', $this->id)->first()?->value,
                ],
            ];
        });
    }

    public function getExtraFieldAttribute()
    {
        return $this->category()->first()->extraFields()->get()->map(function ($extraField) {
            return [
                'id'    => $extraField->id,
                'title' => $extraField->title,
                'value' => $extraField->extraValues()->where('content_id', $this->id)->first()?->value,
            ];
        });
    }

    public function getRelatedContentAttribute()
    {
        return Content::where('category_id', $this->category()->first()?->id)->inRandomOrder()->first(
            ['id', 'title', 'slug']
        );
    }

    public function buyable(): HasOne
    {
        return $this->hasOne(Buyable::class, 'content_id');
    }
}
