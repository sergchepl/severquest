<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;
use App\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        $tasks = Task::all();
        $users = User::whereIsAdmin(false)->get();
        $best_user = User::whereIsAdmin(false)->orderBy('score', 'desc')->with('completed_tasks')->first();

        return view('admin.dashboard', compact('tasks', 'users', 'best_user'));
    }

    public function tasks()
    {
        $tasks = Task::all();

        return view('admin.tasks', compact('tasks'));
    }

    public function createTask()
    {
        return view('admin.create-task');
    }

    public function deleteTask(Task $task)
    {
        $task->delete();

        return back();
    }

    public function users()
    {
        $users = User::whereIsAdmin(false)->orderBy('score', 'desc')->get();

        return view('admin.users', compact('users'));
    }

    public function activateUser(User $user)
    {
        $user->update(['read_rules' => true]);

        return back();
    }

    public function deleteUser(User $user)
    {
        $user->delete();

        return back();
    }
}