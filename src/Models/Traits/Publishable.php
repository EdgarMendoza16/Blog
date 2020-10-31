<?php

namespace EdgarMendozaTech\Blog\Models\Traits;

use Carbon\Carbon;

trait Publishable
{
    public function scopeDrafts($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopePublisheds($query)
    {
        return $query
            ->where('status', 'published')
            ->where('published_at', '<=', Carbon::now()->toDateTimeString());
    }
}
