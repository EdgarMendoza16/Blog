<?php

namespace EdgarMendozaTech\Blog\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use EdgarMendozaTech\Trackables\Trackable;
use EdgarMendozaTech\Blog\Services\StatService;
use EdgarMendozaTech\Blog\Models\Post;
use EdgarMendozaTech\Blog\Models\Category;
use EdgarMendozaTech\Blog\Models\Author;
use EdgarMendozaTech\Blog\Models\Tag;
use Carbon\Carbon;

class StatsController extends Controller
{
    public function counters(Request $request)
    {
        $postsCount = Post::publisheds()->count();
        $categoriesCount = Category::count();
        $tagsCount = Tag::count();
        $authorsCount = Author::count();

        return [
            'posts_count' => $postsCount,
            'categories_count' => $categoriesCount,
            'authors_count' => $authorsCount,
            'tags_count' => $tagsCount,
        ];
    }

    private function getTrackablesStat(string $from, string $to, array $types, array $titles): array
    {
        $data = [];
        for($i=0; $i<count($types); $i++) {
            $type = $types[$i];
            $title = $titles[$i];

            $data[] = [
                'title' => $title,
                'items' => Trackable::filterByDateAndType($from, $to, $type)->get(),
            ];
        }

        return $data;
    }

    public function visualizationStats(Request $request)
    {
        $from = $request->from;
        $to = $request->to;

        $types = [Post::class, Category::class, Author::class, Tag::class];
        $titles = ["Publicaciones", "Categorías", "Autores", "Etiquetas"];

        $data = $this->getTrackablesStat($from, $to, $types, $titles);

        $statService = new StatService();
        $stats = $statService->getStats($data, $from, $to, Auth::user()->timezone);

        return $stats;
    }

    private function getDatesArray($from, $to) {
        $days = [];
        $dates = [];

        while($from <= $to) {
            $days[] = $from->format('d/m');
            $dates[] = $from->toDateString();
            $from->addDays(1);
        }

        return [
            'days' => $days,
            'dates' => $dates,
        ];
    }
}
