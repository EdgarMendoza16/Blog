<?php

namespace EdgarMendozaTech\Blog\Models;

use Illuminate\Database\Eloquent\Model;
use EdgarMendozaTech\MediaResource\MediaResource;
use EdgarMendozaTech\Meta\Meta;
use EdgarMendozaTech\Blog\Models\Traits\Publishable;
use EdgarMendozaTech\Blog\Models\Traits\FilterByRangeDate;

class Category extends Model
{
    use Publishable, FilterByRangeDate;

    protected $table = 'blog_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'order',
        'show_in_navbar',
        'status',
        'published_at',
        'template',
        'views_count',
    ];

    protected $with = [
        'category',
    ];


    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function meta()
    {
        return $this->belongsTo(Meta::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function mediaResource()
    {
        return $this->belongsTo(MediaResource::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'blog_post_categories');
    }

    public static function getNavbarCategories()
    {
        return Category::with([
                'categories' => function($q) {
                    $q->where(function($q) {
                        $q->whereHas('category', function($q) {
                            $q->publisheds();
                        })
                        ->publisheds()
                        ->where('show_in_navbar', true)
                        ->orderBy('order', 'asc');
                    })
                    ->orWhere(function($q) {
                        $q->publisheds()
                        ->where('show_in_navbar', true)
                        ->orderBy('order', 'asc');
                    });
                },
            ])
            ->doesntHave('category')
            ->publisheds()
            ->where('show_in_navbar', true)
            ->orderBy('order', 'asc')
            ->get();
    }
}
