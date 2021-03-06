<?php

namespace App\Http\Controllers;

use App\Ban;
use App\Events\TaskUpdate;
use App\Task;
use App\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $weather = json_decode(file_get_contents("https://api.openweathermap.org/data/2.5/weather?q=SIEVIERODONETSK&appid=8ec388c04f920dbec4234d96e9be6623"));
        $temperature = $weather->main->temp - 273.15;
        $iconUrl = "https://openweathermap.org/img/wn/" . $weather->weather[0]->icon . "@2x.png";

        $tasks = Task::all();
        $users = User::whereIsAdmin(false)->get();
        $bestUser = User::whereIsAdmin(false)->orderBy('score', 'desc')->with('completed_tasks')->first();

        return view('admin.dashboard', compact('tasks', 'users', 'bestUser', 'temperature', 'iconUrl'));
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

    public function editTask(Task $task)
    {
        return view('admin.edit-task', compact('task'));
    }

    public function saveTask(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|min:5',
            'type' => 'required',
            'score' => 'required|integer',
            'description' => 'required',
        ]);

        Task::create($data);

        return \redirect(route('tasks'));
    }

    public function clearTask(Task $task)
    {
        $task->clear();
        event(new TaskUpdate($task));

        return back();
    }

    public function updateTask(Request $request, Task $task)
    {
        $data = $request->validate([
            'name' => 'required|min:5',
            'type' => 'required',
            'score' => 'required|integer',
            'description' => 'required',
        ]);

        $task->update($data);

        return \redirect(route('tasks'));
    }

    public function deleteTask(Task $task)
    {
        $task->delete();

        return back();
    }

    public function bans()
    {
        $bans = Ban::with(['user', 'task'])->get();

        return view('admin.bans', compact('bans'));
    }

    public function deleteBan(Ban $ban)
    {
        $ban->delete();

        return back();
    }

    public function users()
    {
        $users = User::whereIsAdmin(false)->orderBy('score', 'desc')->get();

        return view('admin.users', compact('users'));
    }

    public function clearUser(User $user)
    {
        $user->clear();

        return back();
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
