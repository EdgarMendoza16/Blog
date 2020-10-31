<?php

namespace EdgarMendozaTech\Blog\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use EdgarMendozaTech\Blog\Services\BasicFiltersService;
use EdgarMendozaTech\Blog\Http\Requests\PostRequest;
use EdgarMendozaTech\Blog\Services\PostService;
use EdgarMendozaTech\Blog\Models\Author;
use EdgarMendozaTech\Blog\Models\Category;
use EdgarMendozaTech\Blog\Models\Post;
use EdgarMendozaTech\Blog\Models\Tag;

class PostController extends Controller
{
    private $basicFiltersService;
    private $postService;

    public function __construct()
    {
        $this->basicFiltersService = new BasicFiltersService();
        $this->postService = new PostService();
    }

    public function index(Request $request)
    {
        $withRelations = [
            'meta',
            'meta.mediaResource',
            'mediaResource',
            'categories',
            'tags',
            'authors',
        ];

        [$items, $items_count] = $this->basicFiltersService->filter(
            Post::class,
            $request->all(),
            'title',
            $withRelations,
            null
        );

        return [
            'items' => $items->paginate(28),
            'items_count' => $items_count,
        ];
    }

    public function secondaryData()
    {
        $authors = Author::orderBy('nick_name', 'asc')->get();
        $tags = Tag::orderBy('name', 'asc')->get();
        $categories = Category::orderBy('name', 'asc')->get();

        /** TODO: Será ineficiente en algún momento, hay que hacer consultas en cuando las necesite el frontend. */

        return [
            'authors' => $authors,
            'tags' => $tags,
            'categories' => $categories,
        ];
    }

    public function store(PostRequest $request)
    {
        $post = $this->postService->store(
            $request->all(),
            Auth::user()->timezone
        );

        $post->load([
            'meta',
            'meta.mediaResource',
            'mediaResource',
            'categories',
            'tags',
            'authors',
        ]);

        $data = $this->secondaryData();

        $data['post'] = $post;

        return $data;
    }

    public function edit(Request $request, Post $post)
    {
        $post->load([
            'meta',
            'meta.mediaResource',
            'mediaResource',
            'categories',
            'tags',
            'authors',
        ]);

        $data = $this->secondaryData();

        $data['post'] = $post;

        return $data;
    }

    public function update(PostRequest $request, Post $post)
    {
        $post = $this->postService->update(
            $post,
            $request->all(),
            Auth::user()->timezone
        );

        $post->load([
            'meta',
            'meta.mediaResource',
            'mediaResource',
            'categories',
            'tags',
            'authors',
        ]);

        $data = $this->secondaryData();
        $data['post'] = $post;

        return $data;
    }

    public function destroy(Request $request, Post $post)
    {
        return $this->postService->destroy($post);
    }
}
