<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;
use Illuminate\Support\Facades\Auth;
use Telegram\Bot\Laravel\Facades\Telegram;

class MainController extends Controller
{
    public function rules()
    {
        return view('rules');
    }
    
    public function index()
    {
        $tasks = Task::all();
        return view('index')->with('tasks', $tasks);
    }

    public function takeTask(Request $request)
    {
               
        $task = Task::find($request->task);
        $task->user_id = ($request->team_bool == "true") ? Auth::user()->id : 0;
        $task->save();
        if($request->team_bool == "true")
        {
            $text = "Команда <b>"
            . $request->team
            . "</b> начала выполнять задание: <b>"
            . $request->title
            . "</b>!";
        } else 
        {
            $text = "Команда <b>"
            . $request->team
            . "</b> отменила задание: <b>"
            . $request->title
            . "</b>!";
        }
        Telegram::sendMessage([
            'chat_id' => env('TELEGRAM_CHANNEL_ID', ''),
            'parse_mode' => 'HTML',
            'text' => $text
        ]);

        return $request; 
    }
    public function checkTakenTasks() {
        $tasks = Task::all();
        $dataToSend = [];
        foreach($tasks as $k => $task)
        {
            $dataToSend[$k] = [$task->id => $task->user_id];
        }
        return $dataToSend;

    }
}
