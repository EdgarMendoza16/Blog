<?php

namespace EdgarMendozaTech\Blog\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use EdgarMendozaTech\Blog\Services\BasicFiltersService;
use EdgarMendozaTech\Blog\Http\Requests\AuthorRequest;
use EdgarMendozaTech\Blog\Models\Author;
use EdgarMendozaTech\Blog\Services\AuthorService;

class AuthorController extends Controller
{
    private $authorService;
    private $basicFiltersService;

    public function __construct()
    {
        $this->authorService = new AuthorService();
        $this->basicFiltersService = new BasicFiltersService();
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
            Author::class,
            $request->all(),
            'nick_name',
            null,
            $withCount
        );

        $items = $items->paginate(28);

        return [
            'items' => $items,
            'items_count' => $itemsCount,
        ];
    }

    public function store(AuthorRequest $request)
    {
        $author = $this->authorService->store(
            $request->all(),
            Auth::user()->timezone
        );

        $author->load([
            'meta',
            'meta.mediaResource',
            'mediaResource',
        ]);

        return [
            'author' => $author,
        ];
    }

    public function edit(Request $request, Author $author)
    {
        $author->load([
            'meta',
            'meta.mediaResource',
            'mediaResource',
        ]);

        return [
            'author' => $author,
        ];
    }

    public function update(AuthorRequest $request, Author $author)
    {
        $author = $this->authorService->update(
            $author,
            $request->all(),
            Auth::user()->timezone
        );

        $author->load([
            'meta',
            'meta.mediaResource',
            'mediaResource',
        ]);

        return [
            'author' => $author,
        ];
    }

    public function destroy(Request $request, Author $author)
    {
        return $this->authorService->destroy($author);
    }
}
