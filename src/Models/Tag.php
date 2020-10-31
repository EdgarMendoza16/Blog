<?php

namespace EdgarMendozaTech\Blog\Models;

use Illuminate\Database\Eloquent\Model;
use EdgarMendozaTech\MediaResource\MediaResource;
use EdgarMendozaTech\Meta\Meta;
use EdgarMendozaTech\Blog\Models\Traits\Publishable;
use EdgarMendozaTech\Blog\Models\Traits\FilterByRangeDate;

class Tag extends Model
{
    use Publishable, FilterByRangeDate;

    protected $table = 'blog_tags';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
        'published_at',
        'template',
        'views_count',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function meta()
    {
        return $this->belongsTo(Meta::class);
    }

    public function mediaResource()
    {
        return $this->belongsTo(MediaResource::class);
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'blog_post_tags');
    }
}
