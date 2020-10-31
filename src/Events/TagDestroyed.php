<?php

namespace EdgarMendozaTech\Blog\Events;

use Illuminate\Queue\SerializesModels;
use EdgarMendozaTech\Blog\Models\Tag;

class TagDestroyed
{
    use SerializesModels;

    public $tag;

    public function __construct(Tag $tag)
    {
        $this->tag = $tag;
    }
}
