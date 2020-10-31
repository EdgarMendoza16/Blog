<?php

namespace EdgarMendozaTech\Blog\Services;

use EdgarMendozaTech\MediaResource\MediaResourceServiceDefaultImpl;
use EdgarMendozaTech\Meta\MetaService;
use EdgarMendozaTech\Blog\Models\Tag;
use EdgarMendozaTech\Blog\Events\TagStored;
use EdgarMendozaTech\Blog\Events\TagUpdated;
use EdgarMendozaTech\Blog\Events\TagDestroyed;

class TagService
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

    public function store(array $data, string $timezone): Tag
    {
        $tag = $this->setData(new Tag(), $data, $timezone);

        event(new TagStored($tag));

        return $tag;
    }

    public function update(Tag $tag, array $data, string $timezone): Tag
    {
        $tag = $this->setData($tag, $data, $timezone);

        event(new TagUpdated($tag));

        return $tag;
    }

    public function destroy(Tag $tag): array
    {
        $meta = $tag->meta()->first();
        $tag->delete();
        $this->metaService->destroy($meta);

        event(new TagDestroyed($tag));

        return [
            'msg' => 'success',
        ];
    }

    private function setData(Tag $tag, array $data, string $timezone): Tag
    {
        $tag->name = $data['name'];
        $tag->slug = $data['slug'];
        $tag->description = $data['description'];
        $tag->status = $data['status'];
        $tag->template = $data['template'];

        $tag = $this->publishableService->setPublishedAt(
            $tag,
            $data['published_at'],
            $timezone
        );
        $tag = $this->setMeta($tag, $data);
        $tag = $this->setMediaResource($tag, $data);

        $tag->save();

        return $tag;
    }

    private function setMediaResource(Tag $tag, array $data): Tag
    {
        if (
            isset($data['media_resource_id']) &&
            $data['media_resource_id'] !== null
        ) {
            if ($tag->media_resource_id !== $data['media_resource_id']) {
                $tag->mediaResource()->associate($data['media_resource_id']);
            }
        } else {
            if ($tag->media_resource_id !== null) {
                $tag->mediaResource()->dissociate();
            }
        }

        return $tag;
    }

    private function setMeta(Tag $tag, array $data): Tag
    {
        $meta = $tag->meta()->first();
        if ($meta !== null) {
            $meta = $this->metaService->update($meta, $data);
        } else {
            $meta = $this->metaService->store($data);
            $tag->meta()->associate($meta);
        }

        return $tag;
    }
}
