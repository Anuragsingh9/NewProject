<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomValidationException;
use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\SetTaskTimingRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Http\Resources\TaskTimingResource;
use App\Task;
use App\TaskTiming;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    public function createTask(CreateTaskRequest $request){
        try{
            $param = [
                'title'     => $request->title,
                'status'    => $request->status,
                'user_id'   => Auth::user()->id,
            ];
            $task = Task::create($param);
            return new TaskResource($task);
        }catch (\Exception $e){
            return response()->json(['status' => FALSE, 'error' => $e->getMessage()],403);
        }
    }

    public function updateTasks(UpdateTaskRequest $request){
        try{
            $taskId = $request->task_id;
            $param = [
                'status'    => $request->status,
            ];
            Task::where('id',$taskId)->update($param);
            return (new TaskResource(Task::find($taskId)))->additional(['status' => TRUE]);
        }catch (\Exception $e){
            return response()->json(['status' => FALSE, 'error' => $e->getMessage()],403);
        }
    }

    public function getTasksByUser(){
        try{
            $task = Task::where('user_id',Auth::user()->id)
                ->orderBy('created_at', 'desc')->get();
            if (!$task){
                throw new CustomValidationException('Not Found');
            }
            return TaskResource::collection($task)->additional(['status' => TRUE]);
        } catch (CustomValidationException $exception){
            return response()->json(['status' => FALSE, 'error' => $exception->getMessage()],403);
        }
    }

    public function getAllTasks(){
        $task = Task::with('user')->orderBy('created_at', 'desc')->get();
        return TaskResource::collection($task)->additional(['status' => TRUE]);
    }

    public function deleteTask(Request $request){
        try{
            $taskId = $request->task_id;
            $task = Task::where('id',$taskId)->first();
            if (!$task){
              throw new CustomValidationException('Task id invalid');

            }
            $task->delete();
            return response()->json(['message'=>'Task Deleted'],200);
        }
        catch (CustomValidationException $exception) {
                return response()->json(['status' => FALSE, 'error' => $exception->getMessage()],403);
            }
    }

    public function getATask(Request $request){
        try{
            $taskId = $request->task_id;
            $task = Task::with('user')->where('id',$taskId)->first();
            if (!$task){
                throw new CustomValidationException('Task not found');
            }
            return response()->json(['status'=> TRUE, 'data' => $task],200);
        } catch (CustomValidationException $exception) {
            return response()->json(['status' => FALSE, 'error' => $exception->getMessage()],403);
        }
    }

    public function searchTask(Request $request){
        try{
            $title = $request->title;
            $task = Task::where('title', 'LIKE',"%$title%")->get();
            if (count($task) == 0){
                throw new CustomValidationException('No matching results founds');
            }
            return response()->json($task);
        } catch (CustomValidationException $exception) {
            return response()->json(['status' => FALSE, 'error' => $exception->getMessage()],403);
        }
    }

    public function setTaskTiming(SetTaskTimingRequest $request){
        try{
            $param = [
                'schedule_time' => $request->date,
                'task_id' => $request->task_id,
            ];
           $taskDate = TaskTiming::create($param);
           return new TaskTimingResource($taskDate);
        } catch (\Exception $exception) {
            return response()->json(['status' => FALSE, 'error' => $exception->getMessage()],403);
        }
    }

    public function getTodaysTasks(){
        try{
//            dd(Carbon::today());
            $todayTask = Task::with(['todayTaskTiming'=>function ($q){
                $q->where('schedule_time','=',Carbon::today()->toDateTimeString());
            }])->whereHas('todayTaskTiming')->get();
            if (!$todayTask){
                throw new CustomValidationException('No matching results founds');
            }
            return $todayTask;
        } catch (CustomValidationException $exception) {
            return response()->json(['status' => FALSE, 'error' => $exception->getMessage()],403);
        }

    }

    public function todayTaskCount(){
        try{
            $task = TaskTiming::where('schedule_time','=',Carbon::today()->toDateTimeString());
            $todayTaskCount = $task->count();
            if (!$todayTaskCount){
                throw new CustomValidationException('No task for today');
            }
            return response()->json(['Today' => $todayTaskCount, 'status'=>TRUE],200);

        } catch (CustomValidationException $exception) {
            return response()->json(['status' => FALSE, 'error' => $exception->getMessage()],403);
        }

    }

    public function getNextSevenDaysTasks(){
        try{
            $todayTask = Task::with(['sevenDaysTaskTiming'=>function ($q){
                $q->whereBetween('schedule_time', [Carbon::today()->toDateTimeString(), Carbon::today()->addDays(7)->toDateTimeString()])
                    ->orderBy('schedule_time');
            }])->whereHas('sevenDaysTaskTiming')->get();
            if (!$todayTask){
                throw new CustomValidationException('No Records Found');
            }
            return $todayTask;
        } catch (CustomValidationException $exception) {
            return response()->json(['status' => FALSE, 'error' => $exception->getMessage()],403);
        }
    }

    public function countSevenDaysTasks(){
        try{
            $task = TaskTiming::whereBetween('schedule_time', [Carbon::today()->toDateTimeString(), Carbon::today()->addDays(7)->toDateTimeString()]);
            $todayTaskCount = $task->count();
            if (!$todayTaskCount){
                throw new CustomValidationException('No task for today');
            }
            return response()->json(['Today' => $todayTaskCount, 'status'=>TRUE],200);

        } catch (CustomValidationException $exception) {
            return response()->json(['status' => FALSE, 'error' => $exception->getMessage()],403);
        }
    }

    public function countTask(){
            $task = Task::get();
            $totalTask = $task->count();
            $incomplete = Task::where('status','Incomplete')->count();
            return response()->json(['data'=>$incomplete .'/'.$totalTask,'status'=>TRUE],200);
    }
}
