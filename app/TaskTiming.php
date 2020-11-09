<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskTiming extends Model
{
    protected $table = 'schudeule_timings';
    protected $fillable = ['task_id','schedule_time'];


//    public function scheduleTime()
//    {
//        return $this->hasOne(Task::class,'id');
//    }
    public function scheduleTiming()
    {
        return $this->hasOne(Task::class,'id');
    }

    
}
