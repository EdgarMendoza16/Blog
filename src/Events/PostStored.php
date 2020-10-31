<?php

namespace EdgarMendozaTech\Blog\Events;

use Illuminate\Queue\SerializesModels;
use EdgarMendozaTech\Blog\Models\Post;

class PostStored
{
    use SerializesModels;

    public $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }
}
