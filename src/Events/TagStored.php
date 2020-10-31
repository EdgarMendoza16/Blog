<?php

namespace EdgarMendozaTech\Blog\Events;

use Illuminate\Queue\SerializesModels;
use EdgarMendozaTech\Blog\Models\Tag;

class TagStored
{
    use SerializesModels;

    public $tag;

    public function __construct(Tag $tag)
    {
        $this->tag = $tag;
    }
}
