<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TaskTiming extends Model
{
    protected $table = 'schudeule_timings';
    protected $fillable = ['task_id','schedule_time'];


//    public function scheduleTime()
//    {
//        return $this->hasOne(Task::class,'id');
//    }
//    public function task()
//    {
//        return $this->hasMany(Task::class,'id');
//    }
//    public function task()
//    {
//        return $this->hasMany(Task::class,'id')
//
//            ;
//    }
    
}
