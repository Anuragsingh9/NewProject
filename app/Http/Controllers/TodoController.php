<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomValidationException;
use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\DeleteTaskRequest;
use App\Http\Requests\SetTaskTimingRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Http\Resources\TaskTimingResource;
use App\Services\TodoService;
use App\Task;
use App\TaskTiming;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller

{

    private $todoService;

    public function __construct() {
        $this->todoService = TodoService::getInstance();
    }

    /**
     * check weather task belongs to logged in user or not
     * @param $taskId
     * @return mixed
     */
    public function checkAuth($taskId){
        return Task::where(function ($q) use ($taskId){
            $q->where('id',$taskId);
            $q->where('user_id',Auth::user()->id);
        })->first();
    }

    /**
     * @param CreateTaskRequest $request
     * @return TaskResource|\Illuminate\Http\JsonResponse
     */
    public function createTask(CreateTaskRequest $request){
        try{
            $param = [
                'title'     => $request->title,
                'status'    => $request->status,
                'user_id'   => Auth::user()->id,
                'schedule_time' => $request->schedule_time,
            ];
            $task = $this->todoService->taskCreate($param);
            $taskId = $task->id;
            $param = [
                'schedule_time' => $request->schedule_time,
                'task_id' => $taskId,
            ];
           TaskTiming::create($param);
            return (new TaskResource($task))->additional(['status' => TRUE]);
        } catch (CustomValidationException $exception) {
            return response()->json(['status' => FALSE, 'error' => $exception->getMessage()], 204);
        }
        catch (\Exception $e){
            return response()->json(['status' => FALSE, 'message' =>'Internal Server Error','error' => $e->getMessage()],500);
        }
    }

    /**
     * @param UpdateTaskRequest $request
     * @param $taskId
     * @return TaskResource|\Illuminate\Http\JsonResponse
     */
    public function updateTasks(UpdateTaskRequest $request,$taskId){
        try{
            $check = $this->checkAuth($taskId);
            if (!$check){
                throw new CustomValidationException('Unauthorized Action');
            }
            $param = [
                'status'    => $request->status,
            ];
            $this->todoService->taskUpdate($param,$taskId);
            return (new TaskResource(Task::find($taskId)))->additional(['status' => TRUE]);
        } catch (CustomValidationException $exception){
            return response()->json(['status' => FALSE, 'error' => $exception->getMessage()],403);
        } catch (\Exception $e){
            return response()->json(['status' => FALSE, 'message' =>'Internal Server Error','error' => $e->getMessage()],500);
        }
    }



    /**
     *  get all the task of logged in user
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getTasksByUser(){
        try{
            $task = Task::where('user_id',Auth::user()->id)
                ->orderBy('created_at', 'desc')->get();
            if (!$task){
                throw new CustomValidationException('Not Found');
            }
            return TaskResource::collection($task)->additional(['status' => TRUE]);
        } catch (CustomValidationException $exception){
            return response()->json(['status' => FALSE, 'error' => $exception->getMessage()],204);
        } catch (\Exception $e){
            return response()->json(['status' => FALSE, 'message' =>'Internal Server Error','error' => $e->getMessage()],500);
        }
    }

    /**
     * get all the task irrespective of user.
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getAllTasks(){
        $task = Task::with('user')->orderBy('created_at', 'desc')->get();
        return TaskResource::collection($task)->additional(['status' => TRUE]);
    }

    /**
     * delete a task accordin to task id
     * @param DeleteTaskRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteTask(DeleteTaskRequest $request){
        try{
            $taskId = $request->task_id;
            $check = $this->checkAuth($taskId);
            if (!$check){
                throw new CustomValidationException('Unauthorized Action');
            }
             $this->todoService->taskDelete($taskId);
            return response()->json(['message'=>'Task Deleted'],200);
        } catch (CustomValidationException $exception) {
                return response()->json(['status' => FALSE, 'error' => $exception->getMessage()],401);
        } catch (\Exception $e){
            return response()->json(['status' => FALSE, 'message' =>'Internal Server Error','error' => $e->getMessage()],500);
        }

    }

    /**
     * get a particular task by task id
     * @param Request $request
     * @return TaskResource|\Illuminate\Http\JsonResponse
     */
    public function getATask(Request $request){
        try{
            $taskId = $request->task_id;
            $check = $this->checkAuth($taskId);
            if (!$check){
                throw new CustomValidationException('Task not found');
            }
             $task = Task::with('particularDate')->where('id',$taskId)->first();
            return response()->json(['status'=> TRUE, 'data'=> $task],200 );
//            return (new TaskResource(Task::find($taskId)))->additional(['status' => TRUE]);
        } catch (CustomValidationException $exception) {
            return response()->json(['status' => FALSE, 'error' => $exception->getMessage()],204);
        } catch (\Exception $e){
            return response()->json(['status' => FALSE, 'message' =>'Internal Server Error','error' => $e->getMessage()],500);
        }
    }

//    public function searchTask(Request $request){
//        try{
//            $title = $request->title;
//            $date  = $request->date;
//            if ($title != null){
//                return $this->searchByTitle($title);
//            } elseif ($date != null){
//                return $this->searchByDate($date);
//            }
//            else{
//                return $this->searchByTitle($title);
//            }
//        } catch (\Exception $exception){
//            return response()->json(['status' => FALSE, 'error' => $exception->getMessage()],500);
//        }
//    }
    /**
     * search task of logged in user by title of the task
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchTask(Request $request){
        try{
            $title = $request->title;
            $task = Task::where(function ($q) use ($title){
                $q->where('title', 'LIKE',"%$title%");
                $q->where('user_id',Auth::user()->id);
                $q->orWhere('schedule_time','LIKE',"%$title");
            })->get();
            if (count($task) == 0){
                throw new CustomValidationException('No matching results founds');
            }
            return response()->json($task);
        } catch (CustomValidationException $exception) {
            return response()->json(['status' => FALSE, 'error' => $exception->getMessage()],200);
        } catch (\Exception $e){
            return response()->json(['status' => FALSE, 'message' =>'Internal Server Error','error' => $e->getMessage()],500);
        }
    }

    /**
     * get all the task of a given date from user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchByDate($date){
        try{
            $getDate = new Carbon($date);
            $task = $this->todoService->getDateTask($getDate);
            return response()->json(['status' =>TRUE,'data' => $task],200);
        } catch (CustomValidationException $exception) {
            return response()->json(['status' => FALSE, 'error' => $exception->getMessage()],500);
        } catch (\Exception $exception){
            return response()->json(['status' => FALSE,'error'=>$exception->getMessage()],500);
        }
    }

    /**
     * set a schedule timing for a task.
     * schedule date should be more than todays date
     * @param SetTaskTimingRequest $request
     * @return TaskTimingResource|\Illuminate\Http\JsonResponse
     */
    public function setTaskTiming(SetTaskTimingRequest $request){
        try{
            $taskId = $request->task_id;
            $check = $this->checkAuth($taskId);
            if (!$check){
                throw new CustomValidationException('Unauthorized Action');
            }
            $dateExist = TaskTiming::where('task_id',$taskId)
                ->where('schedule_time',$request->schedule_time)->count();
            if ($dateExist !== 0 ){
                throw new CustomValidationException('Task already added for date');
            }
            $param = [
                'schedule_time' => $request->schedule_time,
                'task_id' => $request->task_id,
            ];
            $taskDate = $this->todoService->createTaskTiming($param);
           return (new TaskTimingResource($taskDate))->additional(['status' => TRUE]);
        } catch (CustomValidationException $exception) {
            return response()->json(['status' => FALSE, 'error' => $exception->getMessage()],200);
        } catch (\Exception $exception) {
            return response()->json(['status' => FALSE, 'error' => $exception->getMessage()],403);
        }
    }

    public function updateTaskTiming(Request $request){
        try{
            $taskId = $request->task_id;
            $check = $this->checkAuth($taskId);
            if (!$check){
                throw new CustomValidationException('Unauthorized Action');
            }
            $id = $request->schedule_id; // Id of the row in schedule_timings table in which we need to update date
            $date = $request->date;
            $this->todoService->updateTaskDate($id,$date);
            return response()->json(['status' => true, 'message' => 'Date updated successfully'],200);
        } catch (CustomValidationException $exception) {
            return response()->json(['status' => FALSE, 'error' => $exception->getMessage()], 204);
        }
        catch (\Exception $exception){
            return response()->json(['status'=> FALSE, 'error'=>$exception->getMessage()],500);
        }
    }

    /**
     * get all task of logged in user of which have schedule date is equal to today
     */
    public function getTodaysTasks(){
        try{
            $todayTask = $this->todoService->getTodayTask();
            return response()->json(['status'=>TRUE, 'data' =>$todayTask ],200);
        } catch (CustomValidationException $exception) {
            return response()->json(['status' => FALSE, 'error' => $exception->getMessage()],204);
        } catch (\Exception $exception) {
            return response()->json(['status' => FALSE, 'error' => $exception->getMessage()],403);
        }
    }

    /**
     * count all the task of logged in user which have schedule date for today
     * @return \Illuminate\Http\JsonResponse
     */
    public function todayTaskCount(){
        try{
            $task = $this->todoService->getTodayTask();
            $todayTaskCount = $task->count();
            if ($todayTaskCount == 0){
                throw new CustomValidationException('No task for today');
            }
            return response()->json(['Today' => $todayTaskCount, 'status'=>TRUE],200);
        } catch (CustomValidationException $exception) {
            return response()->json(['status' => FALSE, 'error' => $exception->getMessage()],204);
        }
    }

    /**
     * get all the task of logged in user which have schedule date for next seven days
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNextSevenDaysTasks(){
        try{
            $sevenDayTask = $this->todoService->getNextSevenDaysTask();
            return response()->json(['status' => TRUE,'data'=>$sevenDayTask],200);
        } catch (CustomValidationException $exception) {
            return response()->json(['status' => FALSE, 'error' => $exception->getMessage()],204);
        } catch (\Exception $exception) {
            return response()->json(['status' => FALSE, 'error' => $exception->getMessage()],403);
        }
    }

    /**
     * count all the task of logged in user which have schedule date for next seven days
     * @return \Illuminate\Http\JsonResponse
     */
    public function countSevenDaysTasks(){
        try{
            $task = $this->todoService->getNextSevenDaysTask();
            $sevenDayTaskCount = $task->count();
            if ($sevenDayTaskCount == 0){
                throw new CustomValidationException('No task for today');
            }
            return response()->json(['Today' => $sevenDayTaskCount, 'status'=>TRUE],200);
        } catch (CustomValidationException $exception) {
            return response()->json(['status' => FALSE, 'error' => $exception->getMessage()],500);
        } catch (\Exception $exception) {
            return response()->json(['status' => FALSE, 'error' => $exception->getMessage()],500);
        }
    }

    /**
     * count all the task logged in user according to status and display in fraction
     * e.g- 3 complete/10 incomplete(3/10)
     * @return \Illuminate\Http\JsonResponse
     */
    public function countTask(){
            $task = Task::where('user_id',Auth::user()->id)->get();
            $totalTask = $task->count();
            $incomplete = Task::where('status','Incomplete')->where('user_id',Auth::user()->id)->count();
            return response()->json(['data'=>$incomplete .'/'.$totalTask,'status'=>TRUE],200);
    }







}
