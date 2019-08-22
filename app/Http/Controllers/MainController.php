<?php

namespace App\Http\Controllers;

use App\Task;

class MainController extends Controller
{
    public function rules()
    {
        return view('rules');
    }

    public function game()
    {
        $tasks = Task::all();
        return view('index', compact('tasks'));
    }
}
