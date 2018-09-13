<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;
use Illuminate\Support\Facades\Auth;
use Telegram\Bot\Laravel\Facades\Telegram;

class MainController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
        return view('index')->with('tasks', $tasks);
    }
    public function takeTask(Request $request)
    {
        $task = Task::find($request->task);
        $task->user_id = Auth::user()->id;
        $task->save();
        $text = "Команда <b>"
            . $request->team
            . "</b> начала выполнять задание: <b>"
            . $request->title
            . "</b>!";
 
        Telegram::sendMessage([
            'chat_id' => env('TELEGRAM_CHANNEL_ID', ''),
            'parse_mode' => 'HTML',
            'text' => $text
        ]);

        return Auth::user()->id; 
    }
    public function checkTakenTasks() {
        $tasks = Task::where('user_id', '!=', 0)->get();
        $dataToSend = [];
        foreach($tasks as $k => $task)
        {
            $dataToSend[$k] = [$task->id => $task->user_id];
        }
        return $dataToSend;

    }
}
