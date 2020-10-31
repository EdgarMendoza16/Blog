<?php

namespace EdgarMendozaTech\Blog\Services;

use EdgarMendozaTech\MediaResource\MediaResourceServiceDefaultImpl;
use EdgarMendozaTech\Meta\MetaService;
use EdgarMendozaTech\Blog\Models\Post;
use EdgarMendozaTech\Blog\Models\Category;
use EdgarMendozaTech\Blog\Events\PostStored;
use EdgarMendozaTech\Blog\Events\PostUpdated;
use EdgarMendozaTech\Blog\Events\PostDestroyed;

class PostService
{
    private $publishableService;
    private $mediaResourceService;
    private $metaService;

    public function __construct()
    {
        $this->publishableService = new PublishableService();
        $this->metaService = new MetaService();
        $this->mediaResourceService = new MediaResourceServiceDefaultImpl();
    }

    public function store(array $data, string $timezone): Post
    {
        $post = $this->setData(new Post(), $data, $timezone);

        event(new PostStored($post));

        return $post;
    }

    public function update(Post $post, array $data, string $timezone): Post
    {
        $post = $this->setData($post, $data, $timezone);

        event(new PostUpdated($post));

        return $post;
    }

    public function destroy(Post $post): array
    {
        $post->authors()->detach();
        $post->categories()->detach();
        $post->tags()->detach();

        $meta = $post->meta()->first();
        $post->delete();
        $this->metaService->destroy($meta);

        event(new PostDestroyed($post));

        return [
            'msg' => 'success',
        ];
    }

    private function setData(Post $post, array $data, string $timezone): Post
    {
        $post->title = $data['title'];
        $post->slug = $data['slug'];
        $post->description = $data['description'];
        $post->content = $data['content'];
        $post->status = $data['status'];
        $post->template = $data['template'];

        $post = $this->publishableService->setPublishedAt(
            $post,
            $data['published_at'],
            $timezone
        );
        $post = $this->setMeta($post, $data);
        $post = $this->setMediaResource($post, $data);

        $post->save();

        $post = $this->setRelations($post, $data);

        return $post;
    }

    private function setMediaResource(Post $post, array $data): Post
    {
        if (
            isset($data['media_resource_id']) &&
            $data['media_resource_id'] !== null
        ) {
            if ($post->media_resource_id !== $data['media_resource_id']) {
                $post->mediaResource()->associate($data['media_resource_id']);
            }
        } else {
            if ($post->media_resource_id !== null) {
                $post->mediaResource()->dissociate();
            }
        }

        return $post;
    }

    private function setMeta(Post $post, array $data): Post
    {
        $meta = $post->meta()->first();
        if ($meta !== null) {
            $meta = $this->metaService->update($meta, $data);
        } else {
            $meta = $this->metaService->store($data);
            $post->meta()->associate($meta);
        }

        return $post;
    }

    private function setRelations(Post $post, array $data): Post
    {
        $post->categories()->sync($data['categories']);
        $post->authors()->sync($data['authors']);
        $post->tags()->sync($data['tags']);

        return $post;
    }
}
