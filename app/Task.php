<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['user_id', 'title', 'status','schedule_time'];
    protected $hidden = ['created_at','updated_at','schedule_time'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }



//    public function getTask()
//    {
//        return $this->hasMany(TaskTiming::class,'task_id');
//    }

    public function todayTaskTiming()
    {
        return $this->hasMany(TaskTiming::class,'task_id')
            ->where('schedule_time','=',Carbon::today()->toDateTimeString());
    }

    public function sevenDaysTaskTiming()
    {
        return $this->hasMany(TaskTiming::class,'task_id')
            ->whereBetween('schedule_time',
                [Carbon::today()->toDateTimeString(), Carbon::today()->addDays(7)->toDateTimeString()]);
    }

    public function scheduleTime()
    {
        return $this->hasMany(TaskTiming::class,'task_id');
    }
}
