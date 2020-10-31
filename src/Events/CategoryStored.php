<?php

namespace EdgarMendozaTech\Blog\Events;

use Illuminate\Queue\SerializesModels;
use EdgarMendozaTech\Blog\Models\Category;

class CategoryStored
{
    use SerializesModels;

    public $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }
}
