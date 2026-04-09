<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'location',
        'is_active',
        'items',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'items' => 'array',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('sort_order');
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('sort_order');
    }

    public function activeItems(): HasMany
    {
        return $this->items()->where('is_active', true)->orderBy('sort_order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByLocation($query, $location)
    {
        return $query->where('location', $location);
    }

    public function getHtml()
    {
        $items = $this->activeItems()->whereNull('parent_id')->get();
        $html = '<ul class="menu menu-' . $this->location . '">';
        
        foreach ($items as $item) {
            $html .= $this->renderMenuItem($item);
        }
        
        $html .= '</ul>';
        
        return $html;
    }

    private function renderMenuItem(MenuItem $item, $level = 0)
    {
        $html = '<li class="menu-item menu-item-' . $item->id . ' menu-level-' . $level . '">';
        $html .= '<a href="' . $item->url . '" target="' . $item->target . '">';
        
        if ($item->icon) {
            $html .= '<i class="' . $item->icon . '"></i> ';
        }
        
        $html .= $item->title . '</a>';
        
        // Render children
        $children = $this->items()->where('parent_id', $item->id)->where('is_active', true)->get();
        if ($children->count() > 0) {
            $html .= '<ul class="submenu submenu-' . $item->id . '">';
            foreach ($children as $child) {
                $html .= $this->renderMenuItem($child, $level + 1);
            }
            $html .= '</ul>';
        }
        
        $html .= '</li>';
        
        return $html;
    }
}
