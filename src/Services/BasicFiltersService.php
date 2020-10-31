<?php

namespace EdgarMendozaTech\Blog\Services;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BasicFiltersService
{
    public function filter(
        $model,
        array $data,
        string $searchField,
        ?array $with=null,
        ?array $withCount = null
    ) {
        $orderBy = $data['order_by'];
        $order = $data['order'];

        $timezone = Auth::user()->timezone;

        $from = $data['from'];
        if ($from !== null && $from !== "") {
            $from = Carbon::parse($from, $timezone)
                ->setTimezone('UTC')
                ->toDateTimeString();
        }

        $to = $data['to'];
        if ($to !== null && $to !== "") {
            $to = Carbon::parse($to, $timezone)
                ->setTimezone('UTC')
                ->toDateTimeString();
        }

        $items = $model::filterByRangeDate($from, $to);
        $itemsCount = $model::filterByRangeDate($from, $to);

        $search = $data['search'];
        if ($search !== null && $search !== null) {
            $items = $items->where($searchField, 'like', "%{$search}%");
            $itemsCount = $itemsCount->where(
                $searchField,
                'like',
                "%{$search}%",
            );
        }

        if ($with !== null) {
            $items = $items->with($with);
        }

        if ($withCount !== null) {
            $items = $items->withCount($withCount);
        }

        $items = $items->orderBy($orderBy, $order);
        $itemsCount = $itemsCount->count();

        return [$items, $itemsCount];
    }
}
