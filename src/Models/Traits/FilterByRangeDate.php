<?php

namespace EdgarMendozaTech\Blog\Models\Traits;

trait FilterByRangeDate
{
	public function scopeFilterByRangeDate($query, $from = null, $to = null, $field='created_at') {
	    return $query
	        ->when($from, function ($q) use ($from, $field) {
	            $q->where($field, '>=', $from);
	        })
	        ->when($to, function ($q) use ($to, $field) {
	            $q->where($field, '<', $to);
	        });
	}
}
