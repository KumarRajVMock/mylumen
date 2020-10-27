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
use App\Events\NotifyEvent;
use App\Models\Notify;
use DateTime;
use App\Jobs\AddtaskJob;
use Pusher;


class TaskController extends Controller
{
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
        
        $person = Registration::where('email',$request->input('assignee'))->first();
        $addtask = new Task;
        $date = new DateTime($request->get('due_date'));
        $addtask->due_date    = $date->format('Y-m-d');
        $addtask->title       = $request->title;
        $addtask->assignee    = $person->id;
        $addtask->creator     = Auth::user()->id;
        $addtask->description = $request->description;
        $addtask->status      = "Assigned";
        $addtask->save();
        
        $this->dispatch((new AddtaskJob($request->assignee, $addtask)));
        
        $newtask = ['title'=> $request->title,
                    'assignee'=> $request->assignee,
                    'due_date'=> $date->format('Y-m-d'),
                    'status'=> "Assigned",
                    'description'=>$request->description,
                    'creator'=>Auth::user()->id,
                    'id'=>$addtask->id,
                    ];
        return response()->json(['message' => 'Task Added', 'task' => $newtask]);
    }
    
    public function updateTaskStatus(Request $request)
    {
        $this->validate($request, [
            'id'          => 'required',
            'status'    => 'required|string',
        ]);
        $task = Task::where('id', $request->input('id'))->first();
        if($task->assignee != Auth::user()->id)
        {
            return response()->json(['message' => "Only assignee can update the tasks"],401);
        }
        $task->status = $request->status;
        $task->save();
        
        $data = new Notify;
        $data->message = 'Status of Task: ' . $task->title . ' has been updated';
        $data->messageHead = 'Task Updated';
        $data->assignee = $task->assignee;
        $data->channel = 'my-channel';
        $data->event = 'updateStatus';
        event(new NotifyEvent($data));
        
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
        $tasks = DB::table('registration')
                    ->join('tasks', 'registration.id', '=', 'tasks.assignee')
                    ->select('tasks.id as id', 
                            'tasks.creator as creator', 
                            'tasks.title as title', 
                            'tasks.description as description',
                            'tasks.due_date as due_date',
                            'registration.email as assignee',
                            'tasks.status as status')
                    ->where('tasks.status','<>', 'Deleted')
                    ->orderBy('due_date')
                    ->get();
        
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
    
    public function searchTask(Request $request)
    {
        $user = Auth::user();
        $tasks = DB::table('registration')
                    ->join('tasks', 'registration.id', '=', 'tasks.assignee')
                    ->select('tasks.id as id', 
                            'tasks.creator as creator', 
                            'tasks.title as title', 
                            'tasks.description as description',
                            'tasks.due_date as due_date',
                            'registration.email as assignee',
                            'tasks.status as status')
                    ->where('tasks.status','<>', 'Deleted')
                    ->orderBy('id')
                    ->paginate(6);
                    // ->get();
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
            $tasks->where('assignee', $request->assignee);
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