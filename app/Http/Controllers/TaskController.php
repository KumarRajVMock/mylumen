<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\Addtask;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Registration;
use App\Models\Task;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\DB;
use App\Events\AddTaskEvent;
use DateTime;


class TaskController extends Controller
{

    /**
     * Instantiate a new UserController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function addTask(Request $request)
    {
        $this->validate($request, [
            'title'       => 'required|string',
            'assignee'    => 'required|email|max:255',
            'due_date'    => 'required|date',
            'description' => 'string'
        ]);
        
        if(Auth::user()->role != "Admin" && Auth::user()->email != $request->input('assignee'))
            return response()->json(['message' => 'Only admin can assign to other users!']);
        
        $addtask = new Task;
        $date = new DateTime($request->get('due_date'));
        $addtask->due_date    = $date->format('Y-m-d');
        $addtask->title       = $request->title;
        $addtask->assignee    = $request->assignee;
        $addtask->creator     = Auth::user()->id;
        $addtask->description = $request->description;
        $addtask->status      = "Assigned";
        $addtask->save();
        
        Mail::to($request->assignee)->send(new Addtask($addtask));

        // event(new AddTaskEvent($addtask->creator));
        
        return response()->json(['message' => 'Task Added', 'task' => $addtask]);
    }

    public function updateTaskStatus(Request $request)
    {
        $taskquery = Task::where('id', $request->input('id'))->first();
        if($taskquery->assignee != Auth::user()->email)
        {
            return response()->json(['message' => "Only assignee can update the tasks"],401);
        }
        $taskquery->status = $request->status;
        $taskquery->save();
        
        return response()->json(['message' => 'Task Updated']);
    }

    public function updateTask(Request $request)
    {
        $this->validate($request, [
            'id'          => 'required',
            'due_date'    => 'required|date',
            'description' => 'required|string'
        ]);
        $task = Task::where('id', $request->input('id'))->first();
        if($task->creator != Auth::user()->id)
        {
            return response()->json(['message' => "Only creator can update the tasks"],401);
        }
        $date = new DateTime($request->input('due_date'));
        $task->due_date = $date->format('Y-m-d');
        $task->description = $request->description;
        
        $task->save();
        return response()->json(['message' => 'Task Updated']);
    }

    public function viewTask()
    {
        $user = Auth::user();
        // $tasks = Task::all();
        $tasks = Task::where('status','<>', 'Deleted')->orderBy('due_date')->get();
        if($user->role == "Admin")
        {
            return response()->json(['tasks' =>  $tasks], 200);
        }
        else
        {
            $query = $tasks->where('assignee', $user->email);
            return response()->json(['tasks' =>  $query, ], 200);
        }
    }
    // public function MyTask()
    // {
    //     // $tasks = Task::where('status','<>', 'Deleted');
    //     $tasks = Task::all();
    //     $query = $tasks->where('assignee', $user->email)->orderBy('due_date');
    //     return response()->json(['tasks' =>  $query, ], 200);
    // }

    public function searchTask(Request $request)
    {
        $user = Auth::user();
        $tasks = Task::where('status','<>', 'Deleted')->get();
        // $tasks = Task::all();
        if($user->role != "Admin")
        {
            $tasks = $tasks->where('assignee', $user->email);
        }

        if ($request->has('title')) 
        {
            $tasks->where('title', $request->title);
        }
        
        if ($request->has('due_date')) 
        {
            $tasks->where('due_date', $request->due_date);
        }
        
        if ($request->has('assignee')) 
        {
            $tasks->where('assignee', $request->get('assignee'));
        }
        
        if ($request->has('creator')) 
        {
            $assignor = Registration::where('email', $request->creator);
            $tasks->where('creator', $assignor->id);
        }
        
        if($tasks == null)
        {
            return response()->json(['message' => 'Nothing to display']);
        }
        return $tasks->toArray();
    }
}