<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;
use App\Group;
use Carbon\Carbon;

class TaskController extends Controller
{
    protected $task;
    protected $group;
    public function __construct(Task $task, Group $group)
    {
        $this->task = $task;
        $this->group = $group;

        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $group = $this->group->find(auth()->user()->active_group);
        $todayTasks = $group->tasks()->today()->notExpired()->confirmed()->latest()->get();
        $otherTasks = $group->tasks()->confirmed()->notExpired()->get()->diff($todayTasks);
        return view('task.task')->withTasks(collect(['todayTasks' => $todayTasks, 'otherTasks' => $otherTasks]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $active_group = auth()->user()->active_group;
        $group = $this->group->find($active_group);
        if($group->users()->where('users.id', auth()->user()->id)->first()->pivot->authority == 1) {
            return view('task.taskAddEdit')->withGroup($group);
        } else {
            return redirect('/task');
        }
    }

    /**
     * Display the history tasks.
     *
     * @return \Illuminate\Http\Response
     */
    public function history()
    {
        $active_group = auth()->user()->active_group;
        return view('task.taskHistory')->withGroup(Group::find($active_group));
    }

    /**
     * View for verifying tasks in the group
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function verify()
    {
        $active_group = auth()->user()->active_group;
        $tasks = $this->group->find($active_group)->tasks;

        $confirmedTasks = $tasks->filter(function($value) {
            if($value->users->count() > 0){
                foreach($value->users as $user) {
                    if($user->pivot->confirmed == 1) {
                        return true;
                    }
                }
            }
            return false;
        })
        ->values();
        $notConfirmedTasks = $tasks->filter(function($value) {
            if($value->users->count() > 0){
                foreach($value->users as $user) {
                    if($user->pivot->confirmed == 0) {
                        return true;
                    }
                }
            }
            return false;
        })
        ->values();

        $group = $this->group->find($active_group);
        if($group->users()->where('users.id', auth()->user()->id)->first()->pivot->authority == 1) {
            return view('task.taskVerify')->withTasks(collect([
                'confirmedTasks' => $confirmedTasks,
                'notConfirmedTasks' => $notConfirmedTasks
            ]));
        } else {
            return redirect('/task');
        }
    }

    public function propose()
    {
        $active_group = auth()->user()->active_group;
        $tasks = $this->group->find($active_group)->tasks()->where('expired_at', '>', Carbon::now())->orderBy('confirmed')->get();
        $proposed_tasks = $tasks->filter(function($task) {
            if($task->confirmed != 1 && $task->creator_id == auth()->user()->id) {
                return true;
            }
            return false;
        })
        ->values();
        $passed_tasks = $tasks->filter(function($task) {
            if($task->confirmed == 1 && $task->creator_id == auth()->user()->id) {
                return true;
            }
            return false;
        })
        ->values();
        return view('task.taskPropose')->withTasks(collect([
            'proposed_tasks' => $proposed_tasks,
            'passed_tasks' => $passed_tasks
        ]));
    }
}
