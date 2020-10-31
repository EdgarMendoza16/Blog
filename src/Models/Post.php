<?php

namespace EdgarMendozaTech\Blog\Models;

use Illuminate\Database\Eloquent\Model;
use EdgarMendozaTech\MediaResource\MediaResource;
use EdgarMendozaTech\Meta\Meta;
use EdgarMendozaTech\Blog\Models\Traits\Publishable;
use EdgarMendozaTech\Blog\Models\Traits\FilterByRangeDate;

class Post extends Model
{
    use Publishable, FilterByRangeDate;

    protected $table = 'blog_posts';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'status',
        'published_at',
        'template',
        'views_count',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function mediaResource()
    {
        return $this->belongsTo(MediaResource::class);
    }

    public function meta()
    {
        return $this->belongsTo(Meta::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'blog_post_tags');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'blog_post_categories');
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'blog_post_authors');
    }
}
