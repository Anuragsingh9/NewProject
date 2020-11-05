<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Task;
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

    public function updateTasks(Request $request){
        try{
            $taskId = $request->task_id;
            $param = [
                'status'    => $request->status,
            ];
            $taskUpdate = Task::where('id',$taskId)->update($param);
//            return new TaskResource($taskUpdate);
            return (new TaskResource(Task::find($taskId)))->additional(['status' => TRUE]);
//            $taskUpdate = $task->update($param);
        }catch (\Exception $e){
            return response()->json(['status' => FALSE, 'error' => $e->getMessage()],403);
        }
    }
}
