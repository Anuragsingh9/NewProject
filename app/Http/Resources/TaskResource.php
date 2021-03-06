<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class TaskResource extends Resource
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
            'id'        => $this->id,
            'title'     => $this->title,
            'status'    => $this->status,
            'created_by'      => new UserResource($this->whenLoaded('user')),
            'schedule_date' => $this->scheduleTime,
//            'schedule_time' => $this->schedule_time,
//            $this->mergeWhen($this->task_id != null, ['schedule_time' => new TaskTimingResource($this->task_id)]),
        ];
    }
}
//new TaskTimingResource($this->task_id),