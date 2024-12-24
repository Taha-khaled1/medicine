<?php

namespace App\Traits\Api;

trait HasTags
{
    // Define the reusable scope for filtering by tags
    public function scopeWithTag($query, $tagName)
    {
        return $query->whereHas('tags', function ($q) use ($tagName) {
            $q->where('title_ar', 'like', $tagName);
        });
    }
    public function scopeWithTagIds($query, $tagId)
    {
        return $query->whereHas('tags', function ($q) use ($tagId) {
            $q->whereIn();
        });
    }
    public function scopeWithTags($query, $tagNames)
    {
        return $query->whereHas('tags', function ($q) use ($tagNames) {
            $q->where('title_ar', 'like', $tagNames);
        });
    }
}
