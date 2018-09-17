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
        
        if($request->team_bool == "true")
        {
            $task->user_id = Auth::user()->id;
            $task->status = 1;
            $text = "Команда <b>"
            . $request->team
            . "</b> начала выполнять задание: <b>"
            . $request->title
            . "</b>!";
        } else 
        {
            $task->user_id = 0;
            $task->status = 0;
            $text = "Команда <b>"
            . $request->team
            . "</b> отменила задание: <b>"
            . $request->title
            . "</b>!";
        }
        $task->save();
        Telegram::sendMessage([
            'chat_id' => env('TELEGRAM_CHANNEL_ID', ''),
            'parse_mode' => 'HTML',
            'text' => $text
        ]);

        return $request; 
    }

    public function checkTakenTasks(Request $request) {
        $tasks = Task::all();
        $taskToSend = [];
        $temp_time = 0;
        foreach($tasks as $task)
        {
            if(strtotime($task->updated_at) > $request->timestamp) {    
                array_push($taskToSend, $task);
                if(strtotime($task->updated_at) > $temp_time) $temp_time = strtotime($task->updated_at);
            }
        }
        $timestamp = ($temp_time != 0) ? $temp_time : $request->timestamp;
        if(count($taskToSend) == 0) return NULL;
        array_push($taskToSend, $timestamp);
        return $taskToSend;

    }

    public function sendAnswer(Request $request)
    {
        $task = Task::find($request->task_id);
        $task->status = 2;
        $task->save();
        
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
        $entities = $updates->channel_post->entities[0]['length'];
        $command = substr($task, 0, $entities);
        $taskId = substr($task, $entities+1);
        
        Log::info($taskId);
        $task = Task::find($taskId);
        if($task != null)
        {
            if($command === '/done') {
                $task->status = 3;
                $text_to_admin = "<b>Задание №$taskId</b> отмечено как: Выполнено!\n";
                
                $text_to_users = "Команда <b>".$task->user()->name."</b> успешно выполнила <b>Задание №$taskId</b> и заработала <b>".$task->score."</b> баллов";
                Telegram::sendMessage([
                    'chat_id' => env('TELEGRAM_CHANNEL_ID', ''),
                    'parse_mode' => 'HTML',
                    'text' => $text_to_users
                ]);
            } else if($command === '/work') {
                $task->status = 1;
                $text_to_admin = "<b>Задание №$taskId</b> отмечено как: В работе!\n";
            } else if($command === '/clear') {
                $task->status = 0;
                $task->user_id = 0;
                $text_to_admin = "<b>Задание №$taskId</b> очищено!\n";
            } else {
                $text_to_admin = "<b>Несуществующая команда!</b>\n";
            }
            $task->save();
        } else {
            $text_to_admin = "<b>Задача №$taskId не существует!</b>\n";
        }

        Telegram::sendMessage([
            'chat_id' => '-1001308540909',
            'parse_mode' => 'HTML',
            'text' => $text_to_admin
        ]);
        return response('ok', 200);
    }
}
