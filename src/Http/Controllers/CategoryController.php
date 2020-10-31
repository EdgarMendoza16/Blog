<?php

namespace EdgarMendozaTech\Blog\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use EdgarMendozaTech\Blog\Services\BasicFiltersService;
use EdgarMendozaTech\Blog\Http\Requests\CategoryRequest;
use EdgarMendozaTech\Blog\Services\CategoryService;
use EdgarMendozaTech\Blog\Models\Category;

class CategoryController extends Controller
{
    private $categoryService;
    private $basicFiltersService;

    public function __construct()
    {
        $this->categoryService = new CategoryService();
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
            Category::class,
            $request->all(),
            'name',
            null,
            $withCount
        );

        $items = $items->paginate(28);

        return [
            'items' => $items,
            'items_count' => $itemsCount,
        ];
    }

    public function list(Request $request)
    {
        return [
            'categories' => $this->getCategoriesList(),
        ];
    }

    public function store(CategoryRequest $request)
    {
        $category = $this->categoryService->store(
            $request->all(),
            Auth::user()->timezone
        );

        $category->load([
            'meta',
            'meta.mediaResource',
            'mediaResource',
            'category',
        ]);

        return [
            'category' => $category,
        ];
    }

    public function edit(Request $request, Category $category)
    {
        $category->load([
            'meta',
            'meta.mediaResource',
            'mediaResource',
            'category',
        ]);

        return [
            'category' => $category,
            'categories' => $this->getCategoriesList($category->id),
        ];
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $category = $this->categoryService->update(
            $category,
            $request->all(),
            Auth::user()->timezone
        );

        $category->load([
            'meta',
            'meta.mediaResource',
            'mediaResource',
            'category',
        ]);

        return [
            'category' => $category,
            'categories' => $this->getCategoriesList($category->id),
        ];
    }

    public function destroy(Request $request, Category $category)
    {
        return $this->categoryService->destroy($category);
    }

    private function getCategoriesList(?int $excludeId = null)
    {
        return Category::when($excludeId, function ($q) use ($excludeId) {
            $q
                ->where('id', '!=', $excludeId)
                ->where(function ($q) use ($excludeId) {
                    $q
                        ->where('category_id', '!=', $excludeId)
                        ->orWhere('category_id', null);
                });
        })
            ->orderBy('name', 'asc')
            ->get();
    }
}
