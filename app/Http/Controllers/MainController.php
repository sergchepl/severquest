<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;
use Illuminate\Support\Facades\Auth;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\FileUpload\InputFile;
use Illuminate\Support\Facades\Log;

class MainController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except', 'rules']);
    }

    public function rules()
    {
        return view('rules');
    }
    
    public function index()
    {
        $tasks = Task::all();
        Log::info($tasks);
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

    public function sendAnswer(Request $request)
    {
        $photo = $request->file('files');
        
        $text = "<b>Пришло задание на проверку!</b> \n"
            . "Название : ".$request->task."\n "
            . "Описание задания: ".$request->task_text."\n"
            . "Команда : ".$request->team."\n"
            . "Сообщение от команды : ".$request->text;
        
        Telegram::sendMessage([
            'chat_id' => env('TELEGRAM_CHANNEL_ID', ''),
            'parse_mode' => 'HTML',
            'text' => $text
        ]);
        foreach($photo as $ph){
            Telegram::sendPhoto([
                'chat_id' => env('TELEGRAM_CHANNEL_ID', ''),
                'photo' => InputFile::createFromContents(file_get_contents($ph->getRealPath()), str_random(10) . '.' . $ph->getClientOriginalExtension())
            ]);
        }
        return redirect()->back();
    }
    public function webhook(Request $request) 
    {
        $updates = Telegram::getWebhookUpdates();
        Log::info($updates);
        return response('ok', 200);
    }
}
