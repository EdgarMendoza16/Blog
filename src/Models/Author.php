<?php

namespace EdgarMendozaTech\Blog\Models;

use Illuminate\Database\Eloquent\Model;
use EdgarMendozaTech\MediaResource\MediaResource;
use EdgarMendozaTech\Meta\Meta;
use EdgarMendozaTech\Blog\Models\Traits\Publishable;
use EdgarMendozaTech\Blog\Models\Traits\FilterByRangeDate;

class Author extends Model
{
    use Publishable, FilterByRangeDate;

    protected $table = 'blog_authors';

    protected $fillable = [
        'nick_name',
        'first_name',
        'last_name',
        'slug',
        'email',
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

    public function mediaResource()
    {
        return $this->belongsTo(MediaResource::class);
    }

    public function meta()
    {
        return $this->belongsTo(Meta::class);
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'blog_post_authors');
    }
}
