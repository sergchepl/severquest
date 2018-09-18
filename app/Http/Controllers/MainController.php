<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;
use App\User;
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
            $text = "Команда <b>".$request->team."</b> приступила к выполнению задания <b>".$request->title."</b>.";
        } else 
        {
            $task->user_id = 0;
            $task->status = 0;
            $text = "Задание <b>".$request->title."</b> снова доступно для выполнения всеми командами.";
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
        return response('ok', 200);
    }
    public function webhook() 
    {
        $updates = Telegram::getWebhookUpdates();
        if($updates->channel_post == NULL) {
            return response('ok', 200);
        }
        Log::info($updates);

        $task = $updates->channel_post->text;
        $entities = $updates->channel_post->entities[0]['length'] ? $updates->channel_post->entities[0]['length'] : 0;
        $command = substr($task, 0, $entities);
        $number = substr($task, $entities+1);
        
        $text_to_admin = "";

        $task = Task::find($number);
        if($command === '/list' || $command === '/clear_team') {
            if($command === '/list') {
                $users = User::all();
                foreach ($users as $user) {
                    $text_to_admin .= "----------------------------\n<b>ID команды:</b> ".$user->id."\n<b>Название команды:</b> ".$user->name."\n<b>Количество баллов:</b> ".$user->score."\n"; 
                }
                $text_to_admin .= "----------------------------\n";
            } else {
                $user = User::find($number); 
                $user->score = 0;
                $user->save();
                $text_to_admin = "Прогресс команды <b>".$user->name."</b> обнулен!\n";
            }
        } else if($task != null) {
            switch($command) {
                case '/done': 
                    $task->status = 3;
                    $text_to_admin = "Теперь статус задания <b>№$number</b> : Выполнено!\n";
                    
                    $score_to_save = $task->user->score;
                    $user = $task->user;
                    $user->score = $score_to_save + $task->score;
                    $user->save();
                    
                    $text_to_users = "Задание <b>".$task->name."</b> успешно выполнено командой <b>".$task->user->name."</b>.";
                    Telegram::sendMessage([
                        'chat_id' => env('TELEGRAM_CHANNEL_ID', ''),
                        'parse_mode' => 'HTML',
                        'text' => $text_to_users
                    ]);
                    break;
                case '/work': 
                    $task->status = 1;
                    $text_to_admin = "Теперь статус задания <b>№$number</b> : В работе!\n";
                    $text_to_users = "Задание <b>".$task->name."</b> выполняемое командой <b>".$task->user->name."</b> требует доработки. Внимательно " 
                                    ."проверьте требования к заданию и повторите загрузку соответствующих материалов.";
                    Telegram::sendMessage([
                        'chat_id' => env('TELEGRAM_CHANNEL_ID', ''),
                        'parse_mode' => 'HTML',
                        'text' => $text_to_users
                    ]);
                    break;
                case '/clear': 
                    $task->status = 0;
                    $task->user_id = 0;
                    $text_to_admin = "Теперь статус задания <b>№$number</b> : Открыто!\n";
                    break;
                default: $text_to_admin = "<b>Несуществующая команда!</b>\n";
                break;
            }
            $task->save();
        } else {
            $text_to_admin = "<b>Задача №$number не существует!</b>\n";
        }

        Telegram::sendMessage([
            'chat_id' => '-1001308540909',
            'parse_mode' => 'HTML',
            'text' => $text_to_admin
        ]);
        return response('ok', 200);
    }
}
