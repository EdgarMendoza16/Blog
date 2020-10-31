<?php

namespace EdgarMendozaTech\Blog\Services;

use EdgarMendozaTech\MediaResource\MediaResourceServiceDefaultImpl;
use EdgarMendozaTech\Meta\MetaService;
use EdgarMendozaTech\Blog\Models\Author;
use EdgarMendozaTech\Blog\Events\AuthorStored;
use EdgarMendozaTech\Blog\Events\AuthorUpdated;
use EdgarMendozaTech\Blog\Events\AuthorDestroyed;

class AuthorService
{
    private $publishableService;
    private $mediaResourceService;
    private $metaService;

    public function __construct()
    {
        $this->publishableService = new PublishableService();
        $this->mediaResourceService = new MediaResourceServiceDefaultImpl();
        $this->metaService = new MetaService();
    }

    public function store(array $data, string $timezone): Author
    {
        $author = $this->setData(new Author(), $data, $timezone);

        event(new AuthorStored($author));

        return $author;
    }

    public function update(
        Author $author,
        array $data,
        string $timezone
    ): Author {
        $author = $this->setData($author, $data, $timezone);

        event(new AuthorUpdated($author));

        return $author;
    }

    public function destroy(Author $author): array
    {
        $meta = $author->meta()->first();
        $author->delete();
        $this->metaService->destroy($meta);

        event(new AuthorDestroyed($author));

        return [
            'msg' => 'success',
        ];
    }

    private function setData(
        Author $author,
        array $data,
        string $timezone
    ): Author {
        $author->nick_name = $data['nick_name'];
        $author->first_name = $data['first_name'];
        $author->last_name = $data['last_name'];
        $author->slug = $data['slug'];
        $author->email = $data['email'];
        $author->description = $data['description'];
        $author->status = $data['status'];
        $author->template = $data['template'];

        $author = $this->publishableService->setPublishedAt(
            $author,
            $data['published_at'],
            $timezone
        );
        $author = $this->setMeta($author, $data);
        $author = $this->setMediaResource($author, $data);

        $author->save();

        return $author;
    }

    private function setMediaResource(Author $author, array $data): Author
    {
        if (
            isset($data['media_resource_id']) &&
            $data['media_resource_id'] !== null
        ) {
            if ($author->media_resource_id !== $data['media_resource_id']) {
                $author->mediaResource()->associate($data['media_resource_id']);
            }
        } else {
            if ($author->media_resource_id !== null) {
                $author->mediaResource()->dissociate();
            }
        }

        return $author;
    }

    private function setMeta(Author $author, array $data): Author
    {
        $meta = $author->meta()->first();
        if($meta !== null) {
            $meta = $this->metaService->update($meta, $data);
        }
        else {
            $meta = $this->metaService->store($data);
            $author->meta()->associate($meta);
        }

        return $author;
    }
}
