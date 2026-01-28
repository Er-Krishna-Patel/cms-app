<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItems extends Model
{
    protected $fillable = [
        'title',
        'icon',
        'route',
        'url',
        'parent_id',
        'position',
        'type',
        'permission',
        'is_active',
        'meta',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'meta' => 'array',
        'position' => 'integer',
    ];

    /**
     * Get child menu items
     */
    public function children()
    {
        return $this->hasMany(MenuItems::class, 'parent_id')->orderBy('position');
    }

    /**
     * Get parent menu item
     */
    public function parent()
    {
        return $this->belongsTo(MenuItems::class, 'parent_id');
    }

    /**
     * Scope to get active menu items
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get menu items by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Check if user has permission to see this menu
     */
    public function canAccess($user)
    {
        if (!$this->permission) {
            return true;
        }

        return $user->role === 'admin' || $user->role === $this->permission;
    }

    /**
     * Get the link for this menu item
     */
    public function getLink()
    {
        if ($this->url) {
            return $this->url;
        }

        if ($this->route) {
            return route($this->route);
        }

        return '#';
    }
}
