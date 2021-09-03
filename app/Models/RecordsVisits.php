<?php

namespace App\Models;

use Illuminate\Support\Facades\Redis;

trait RecordsVisits
{
    public function recordVisit()
    {
        Redis::incr($this->VisitsCacheKey());
        return $this;
    }

    public function visits()
    {
        return Redis::get($this->VisitsCacheKey()) ?? 0;
    }

    public function resetVisits()
    {
        Redis::del($this->VisitsCacheKey());
        return $this;
    }

    protected function visitsCacheKey()
    {
        return "threads.{$this->id}.visits";
    }
}
