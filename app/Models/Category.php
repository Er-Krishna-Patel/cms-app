<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = ['name', 'slug'];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::saving(function (Category $category) {
            if (empty($category->slug) && !empty($category->name)) {
                $base = Str::slug($category->name);
                $slug = $base;
                $i = 1;
                while (static::where('slug', $slug)->when($category->exists, function ($q) use ($category) {
                    $q->where('id', '!=', $category->id);
                })->exists()) {
                    $slug = $base.'-'.$i++;
                }
                $category->slug = $slug;
            }
        });
    }
}
