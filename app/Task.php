<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['user_id', 'title', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

//    public function scheduleTiming()
//    {
//        return $this->hasOne(TaskTiming::class,'task_id');
//    }
}
