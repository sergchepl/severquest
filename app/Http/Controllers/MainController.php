<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;
use Illuminate\Support\Facades\Auth;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\FileUpload\InputFile;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class MainController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['rules', 'webhook']]);
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
        
        $text = "<b>Задание №".$request->task_id." пришло на проверку!</b>\n"
            . "Название : ".$request->task."\n "
            . "Описание задания: ".$request->task_text."\n"
            . "Команда : ".$request->team."\n"
            . "Сообщение от команды : ".$request->text;
        
        Telegram::sendMessage([
            'chat_id' => '-1001308540909',
            'parse_mode' => 'HTML',
            'text' => $text
        ]);
        foreach($photo as $ph){
            Telegram::sendPhoto([
                'chat_id' => '-1001308540909',
                'photo' => InputFile::createFromContents(file_get_contents($ph->getRealPath()), str_random(10) . '.' . $ph->getClientOriginalExtension())
            ]);
        }
        return redirect()->back();
    }
    public function webhook() 
    {
        $updates = Telegram::getWebhookUpdates();
        if($updates->channel_post == NULL) {
            return response('ok', 200);
        }
        Log::info($updates);
        $task = $updates->channel_post->text;
        $command = substr($task, 0, 5);
        $taskId = substr($task, 6);
        
        Log::info($taskId);
        $task = Task::find($taskId);
        if($task != null)
        {
            if($command === '/done') {
                $task->done = 1;
                $task->save();
                $text = "<b>Задание №".$taskId."</b> успешно отмечено как: Выполненно!\n";
            } else if($command === '/work') {
                $task->done = 0;
                $task->save();
                $text = "<b>Задание №".$taskId."</b> успешно отмечено как: В работе!\n";
            } else {
                $text = "<b>Неправильная команда!</b>\n";
            }
            
        }

        Telegram::sendMessage([
            'chat_id' => '-1001308540909',
            'parse_mode' => 'HTML',
            'text' => $text
        ]);
        return response('ok', 200);
    }
}
