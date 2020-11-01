<?php

namespace EdgarMendozaTech\Blog\Services;

use Illuminate\Support\Collection;
use Carbon\Carbon;

class StatService
{
    private $days;
    private $dates;

    private $from;
    private $to;

    public function getStats($data, string $from, string $to, string $timezone): array
    {
        $this->setRange($from, $to, $timezone);

        $stats = [];

        foreach($data as $itemData) {
            $title = $itemData['title'];
            $items = $itemData['items'];

            $stats[$title] = $this->getStatsFrom($items);
        }

        return [
            'days' => $this->days,
            'stats' => $stats,
        ];
    }

    private function setRange(string $from, string $to, string $timezone): void
    {
        $this->from = Carbon::parse($from, $timezone)->utc();
        $this->to = Carbon::parse($to, $timezone)->utc();

        $this->generateDatesArray();
    }

    private function getStatsFrom(Collection $items): array
    {
        $stats = [];

        foreach ($this->dates as $day) {
            $stats[] = $items
                ->filter(function ($value, $key) use ($day) {
                    return Carbon::parse($value->created_at)->toDateString() ===
                        $day;
                })
                ->count();
        }

        return $stats;
    }

    private function generateDatesArray(): void
    {
        $this->days = [];
        $this->dates = [];

        $fromDay = $this->from->clone();

        while ($fromDay <= $this->to) {
            $this->days[] = $fromDay->format('d/m');
            $this->dates[] = $fromDay->toDateString();
            $fromDay->addDays(1);
        }
    }
}
