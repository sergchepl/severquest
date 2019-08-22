<?php

namespace App\Http\Controllers;

use App\Task;

class MainController extends Controller
{
    public function rules()
    {
        return view('rules');
    }

    public function index()
    {
        $tasks = Task::all();
        return view('index', compact('tasks'));
    }
}
