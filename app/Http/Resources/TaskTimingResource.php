<?php

namespace App\Http\Resources;

use App\Task;
use Illuminate\Http\Resources\Json\Resource;

class TaskTimingResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'task'          => new TaskResource(Task::find($this->task_id)),
            'schedule_time' => $this->schedule_time,
        ];
    }
}
