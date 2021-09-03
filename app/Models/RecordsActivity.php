<?php


namespace App\Models;


use Illuminate\Support\Facades\Redis;

trait RecordsActivity       //Means a model can be Recorded on create or update or... and it stores in Activity table
{
    protected static function bootRecordsActivity()
    {
        if (auth()->guest()) return;

        foreach (static::getActivitiesToRecords() as $event) {
            static::$event(function ($model) use ($event) {
                $model->recordActivity($event);
            });
        }
        static::deleting(function ($model) {
            $model->activity()->delete();
        });
    }

    protected static function getActivitiesToRecords()
    {
        return ['created'];
    }

    protected function recordActivity($event)
    {
        $this->activity()->create([
            'user_id' => auth()->id(),
            'type' => $this->getActivityType($event)
        ]);
    }

    public function activity()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    protected function getActivityType($event)
    {
        $type = strtolower((new \reflectionclass($this))->getshortname());
        return "{$event}_{$type}";
    }
}
