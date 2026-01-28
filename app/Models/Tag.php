<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    protected $fillable = ['name', 'slug'];

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::saving(function (Tag $tag) {
            if (empty($tag->slug) && !empty($tag->name)) {
                $base = Str::slug($tag->name);
                $slug = $base;
                $i = 1;
                while (static::where('slug', $slug)->when($tag->exists, function ($q) use ($tag) {
                    $q->where('id', '!=', $tag->id);
                })->exists()) {
                    $slug = $base.'-'.$i++;
                }
                $tag->slug = $slug;
            }
        });
    }
}
