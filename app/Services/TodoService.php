<?php


namespace App\Services;


use App\Exceptions\CustomValidationException;
use App\Task;
use App\TaskTiming;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TodoService
{
    /**
     * @return static|null
     */
    public static function getInstance() {

        static $instance = NULL;
        if (NULL === $instance) {
            $instance = new static();
        }
        return $instance;
    }

    /**
     * @param $param
     * @return mixed
     * @throws CustomValidationException
     */
    public function taskCreate($param){
        $task =  Task::create($param);
        if (!$task){
            throw new CustomValidationException('Task not created');
        }
        return $task;
    }

    /**
     * @param $param
     * @param $taskId
     * @return mixed
     * @throws CustomValidationException
     */
    public function taskUpdate($param,$taskId){
       $task = Task::where('id',$taskId)->update($param);
        if (!$task){
            throw new CustomValidationException('Task not updated');
        }
        return $task;
    }

    /**
     * @param $taskId
     * @return mixed
     */
    public function taskDelete($taskId){
        $task = Task::where('id',$taskId )->first();
        return  $task->delete();
    }

    /**
     * @param $param
     * @return mixed
     * @throws CustomValidationException
     */
    public function createTaskTiming($param){
        $taskDate = TaskTiming::create($param);
        if (!$taskDate){
            throw new CustomValidationException('Task not created');
        }
        return $taskDate;

    }

    /**
     * @param $id
     * @param $date
     * @return mixed
     * @throws CustomValidationException
     */
    public function updateTaskDate($id,$date){
       $taskDate = TaskTiming::where('id',$id)->update(['schedule_time' => $date]);
        if (!$taskDate){
            throw new CustomValidationException('Task not updated');
        }
        return $taskDate;
    }

    /**
     * @return Task[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     * @throws CustomValidationException
     */
    public function getTodayTask(){
        $todayTask = Task::with(['todayTaskTiming'=>function ($q){
            $q->where('schedule_time','=',Carbon::today()->toDateTimeString());
        }])->whereHas('todayTaskTiming')
            ->where('user_id',Auth::user()->id)->get();

        if ($todayTask == NULL){
            throw new CustomValidationException('No matching results founds');
        }
        return $todayTask;
    }

    /**
     * @return Task[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     * @throws CustomValidationException
     */
    public function getNextSevenDaysTask(){
        $sevenDayTask = Task::with(['sevenDaysTaskTiming'=>function ($q){
            $q->whereBetween('schedule_time', [Carbon::today()->toDateTimeString(), Carbon::today()->addDays(7)->toDateTimeString()])
                ->orderBy('schedule_time');
        }])->whereHas('sevenDaysTaskTiming')
            ->where('user_id',Auth::user()->id)
            ->get();
        if ($sevenDayTask == NULL){
            throw new CustomValidationException('No Records Found');
        }
        return $sevenDayTask;
    }

    public function getDateTask($getDate){
        $task = Task::with(['scheduleTime' =>function($q) use ($getDate){
            $q->where('schedule_time','=',$getDate->toDateTimeString());
        }])->whereHas('scheduleTime', function($q) use ($getDate){
            $q->where('schedule_time','=',$getDate->toDateTimeString());
        })->where('user_id',Auth::user()->id)
//            ->orWhere('schedule_time','=',$getDate->toDateTimeString())
            ->get();
        if ($task->count() == 0){
            throw new CustomValidationException('No records for '.$getDate->toDateString());
        }
        return $task;
    }

    public function deleteDate($dateId){
        $deleteDate = TaskTiming::where('id',$dateId)->first();
        return $deleteDate->delete();
    }
}