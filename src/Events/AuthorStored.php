<?php

namespace EdgarMendozaTech\Blog\Events;

use Illuminate\Queue\SerializesModels;
use EdgarMendozaTech\Blog\Models\Author;

class AuthorStored
{
    use SerializesModels;

    public $author;

    public function __construct(Author $author)
    {
        $this->author = $author;
    }
}
