<?php

namespace EdgarMendozaTech\Blog\Services;

use EdgarMendozaTech\MediaResource\MediaResourceServiceDefaultImpl;
use EdgarMendozaTech\Meta\MetaService;
use EdgarMendozaTech\Blog\Models\Category;
use EdgarMendozaTech\Blog\Events\CategoryStored;
use EdgarMendozaTech\Blog\Events\CategoryUpdated;
use EdgarMendozaTech\Blog\Events\CategoryDestroyed;

class CategoryService
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

    public function store(array $data, string $timezone): Category
    {
        $category = $this->setData(new Category(), $data, $timezone);

        event(new CategoryStored($category));

        return $category;
    }

    public function update(
        Category $category,
        array $data,
        string $timezone
    ): Category {
        $category = $this->setData($category, $data, $timezone);

        event(new CategoryUpdated($category));

        return $category;
    }

    public function destroy(Category $category): array
    {
        $childCategoriesCount = $category->categories()->count();
        if ($childCategoriesCount !== 0) {
            return [
                'msg' =>
                    "La categoría no puede eliminarse si tiene categorías hijas",
            ];
        }

        $meta = $category->meta()->first();
        $category->delete();
        $this->metaService->destroy($meta);

        event(new CategoryDestroyed($category));

        return [
            'msg' => 'success',
        ];
    }

    private function setData(
        Category $category,
        array $data,
        string $timezone
    ): Category {
        $category->name = $data['name'];
        $category->slug = $data['slug'];
        $category->description = $data['description'];
        $category->order = $data['order'];
        $category->show_in_navbar = $data['show_in_navbar'];
        $category->status = $data['status'];
        $category->template = $data['template'];

        $category = $this->publishableService->setPublishedAt(
            $category,
            $data['published_at'],
            $timezone
        );
        $category = $this->setMeta($category, $data);
        $category = $this->setMediaResource($category, $data);

        $category->save();

        $category = $this->setCategory($category, $data);

        return $category;
    }

    private function setMediaResource(Category $category, array $data): Category
    {
        if (
            isset($data['media_resource_id']) &&
            $data['media_resource_id'] !== null
        ) {
            if ($category->media_resource_id !== $data['media_resource_id']) {
                $category
                    ->mediaResource()
                    ->associate($data['media_resource_id']);
            }
        } else {
            if ($category->media_resource_id !== null) {
                $category->mediaResource()->dissociate();
            }
        }

        return $category;
    }

    private function setMeta(Category $category, array $data): Category
    {
        $meta = $category->meta()->first();
        if ($meta !== null) {
            $meta = $this->metaService->update($meta, $data);
        } else {
            $meta = $this->metaService->store($data);
            $category->meta()->associate($meta);
        }

        return $category;
    }

    private function setCategory(Category $category, array $data): Category
    {
        if (isset($data['category_id']) && $data['category_id'] !== null) {
            if (
                $data['category_id'] !== $category->id &&
                $data['category_id'] !== $category->category_id
            ) {
                $category->category()->associate($data['category_id']);
                $category->save();
            }
        } else {
            if ($category->category_id !== null) {
                $category->category()->dissociate();
                $category->save();
            }
        }

        return $category;
    }
}
