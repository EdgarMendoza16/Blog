<?php

namespace EdgarMendozaTech\Blog\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use EdgarMendozaTech\Blog\Services\BasicFiltersService;
use EdgarMendozaTech\Blog\Http\Requests\TagRequest;
use EdgarMendozaTech\Blog\Services\TagService;
use EdgarMendozaTech\Blog\Models\Tag;

class TagController extends Controller
{
    private $basicFiltersService;
    private $tagService;

    public function __construct()
    {
        $this->basicFiltersService = new BasicFiltersService();
        $this->tagService = new TagService();
    }

    public function index(Request $request)
    {
        $withCount = [
            'posts',
            'posts as post_views_count' => function ($q) {
                $q->select(DB::raw('sum(views_count)'));
            },
        ];

        [$items, $itemsCount] = $this->basicFiltersService->filter(
            Tag::class,
            $request->all(),
            'name',
            null,
            $withCount
        );

        return [
            'items' => $items->paginate(28),
            'items_count' => $itemsCount,
        ];
    }

    public function store(TagRequest $request)
    {
        $tag = $this->tagService->store(
            $request->all(),
            Auth::user()->timezone
        );

        $tag->load(['meta', 'meta.mediaResource', 'mediaResource']);

        return [
            'tag' => $tag,
        ];
    }

    public function edit(Request $request, Tag $tag)
    {
        $tag->load(['meta', 'meta.mediaResource', 'mediaResource']);

        return [
            'tag' => $tag,
        ];
    }

    public function update(TagRequest $request, Tag $tag)
    {
        $tag = $this->tagService->update(
            $tag,
            $request->all(),
            Auth::user()->timezone
        );

        $tag->load(['meta', 'meta.mediaResource', 'mediaResource']);

        return [
            'tag' => $tag,
        ];
    }

    public function destroy(Request $request, Tag $tag)
    {
        return $this->tagService->destroy($tag);
    }
}
