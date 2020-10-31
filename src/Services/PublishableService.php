<?php

namespace EdgarMendozaTech\Blog\Services;

use Carbon\Carbon;

class PublishableService
{
    public function setPublishedAt($model, ?string $publishedAt=null, string $timezone)
    {
        if ($model->status !== "published") {
            $model->published_at = null;
            return $model;
        }

        $isUtc = strpos($publishedAt, "T") > 0;
        if ($isUtc) {
            $model->published_at = Carbon::parse($publishedAt)->toDateTimeString();
        } else {
            $model->published_at = Carbon::createFromFormat(
                    "d/m/Y H:i",
                    $publishedAt,
                    $timezone
                )
                ->setTimezone('UTC')
                ->toDateTimeString();
        }

        return $model;
    }
}
