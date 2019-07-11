<?php

namespace App\Http\Controllers;

use App\Ban;
use App\Events\Score;
use App\Events\TaskUpdate;
use App\Task;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

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
        return view('index')->with('tasks', $tasks);
    }

    public function takeTask(Request $request)
    {
        $task = Task::find($request->task_id);

        if ($request->is_taking == "true") {
            $teamTasks = Task::whereUserId(Auth::user()->id)->whereStatus(1)->get();

            if (count($teamTasks) > 0) {
                return response('Have another task', 409);
            }

            $task->update([
                'user_id' => Auth::user()->id,
                'status' => 1,
            ]);
            $text = "🚲 Команда <b>" . $task->user->name . "</b> приступила к выполнению задания <b>" . $task->name . "</b>.";
        } else {
            $task->update([
                'user_id' => 0,
                'status' => 0,
            ]);
            $text = "🎲 Задание <b>" . $task->name . "</b> снова доступно для выполнения всеми командами.";
        }
        event(new TaskUpdate($task));
 
        Telegram::sendMessage([
            'chat_id' => config('telegram.channel'),
            'parse_mode' => 'HTML',
            'text' => $text,
        ]);

        return $request;
    }

    public function setScore(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $score_to_save = $user->score;
        $newScore = json_decode($request);
        $user->score = $request->score + $score_to_save;
        $user->read_rules = true;
        $user->save();
        return response($request->score, 200);
    }

    public function checkTakenTasks(Request $request)
    {
        $bannedTasks = User::find(Auth::user()->id)->bans;

        $tasks = Task::all();
        $taskToSend = [];
        $temp_time = 0;
        foreach ($tasks as $task) {
            if (strtotime($task->updated_at) > $request->timestamp) {
                array_push($taskToSend, $task);
                if (strtotime($task->updated_at) > $temp_time) {
                    $temp_time = strtotime($task->updated_at);
                }

            }
        }
        $timestamp = ($temp_time != 0) ? $temp_time : $request->timestamp;

        if (count($bannedTasks) != $request->banned_tasks) {
            array_push($taskToSend, $timestamp);
            array_push($taskToSend, $bannedTasks);
            return $taskToSend;
        }
        if (count($taskToSend) != 0) {
            array_push($taskToSend, $timestamp);
            return $taskToSend;
        }
        return response('ok', 204);
    }

    public function sendAnswer(Request $request)
    {
        $task = Task::find($request->task_id);

        if ($task->type == 1) {
            $task->update([
                'status' => 2,
            ]);
        }
        if ($task->type == 2) {
            $ban = Ban::create([
                'user_id' => Auth::user()->id,
                'task_id' => $request->task_id,
            ]); //TODO: make BAN work
        }
        event(new TaskUpdate($task));

        return response('ok', 200);
    }

    public function webhook()
    {
        $updates = Telegram::getWebhookUpdates();
        Log::info($updates);
        if ($updates->channel_post == null || $updates->channel_post->chat->id != -1001308540909) {
            return response('Nothing', 204);
        }

        $commandText = $updates->channel_post->text;
        $commandArray = explode(' ', $commandText);
        $command = $commandArray[0];
        $number = count($commandArray) > 1 ? (int) $commandArray[1] : 1;
        $secondNumber = count($commandArray) > 2 ? (int) $commandArray[2] : 1;
        $text_to_admin = "";

        $task = Task::find($number);
        switch ($command) {
            case '/list':
                $users = User::orderBy('score', 'desc')->get();
                foreach ($users as $k => $user) {
                    switch ($k) {
                        case 0:$text_to_admin .= "------------------🥇------------------\n<b>ID команды:</b> " . $user->id
                            . "\n<b>Название команды:</b> " . $user->name . "\n<b>Количество баллов:</b> " . $user->score . "\n";
                            break;
                        case 1:$text_to_admin .= "------------------🥈------------------\n<b>ID команды:</b> " . $user->id
                            . "\n<b>Название команды:</b> " . $user->name . "\n<b>Количество баллов:</b> " . $user->score . "\n";
                            break;
                        case 2:$text_to_admin .= "------------------🥉------------------\n<b>ID команды:</b> " . $user->id
                            . "\n<b>Название команды:</b> " . $user->name . "\n<b>Количество баллов:</b> " . $user->score . "\n";
                            break;
                        default:$text_to_admin .= "--------------------------------------\n<b>ID команды:</b> " . $user->id
                            . "\n<b>Название команды:</b> " . $user->name . "\n<b>Количество баллов:</b> " . $user->score . "\n";
                            break;
                    }
                }
                $text_to_admin .= "--------------------------------------\n";
                break;
            case '/clear_team':
                $user = User::find($number);
                $user->score = 0;
                $user->save();
                $text_to_admin = "Прогресс команды <b>" . $user->name . "</b> обнулен!\n";
                break;
            case '/clear_ban':
                $bans = Ban::where('user_id', $number)->where('task_id', $secondNumber)->delete();
                $user = User::find($number);
                $task = Task::find($secondNumber);
                $text_to_admin = "Задание <b>" . $task->name . "</b> команды <b>" . $user->name . "</b> разбанено!\n";
                $text_to_users = "🚦 Задание <b>" . $task->name . "</b> команды <b>" . $user->name . "</b> разбанено!\n";
                Telegram::sendMessage([
                    'chat_id' => config('telegram.channel'),
                    'parse_mode' => 'HTML',
                    'text' => $text_to_users,
                ]);
                break;
            case '/add':
                $user = User::find($number);
                if (is_null($user)) {
                    $text_to_admin = "Команды с таким ID не существует!\n";
                    break;
                }
                $score_to_save = $user->score;
                $user->score = $score_to_save + $secondNumber;
                $user->save();
                $text_to_admin = "Команде <b>" . $user->name . "</b> успешно добавлено <b>" . $secondNumber . "</b> очков!\n";
                $text_to_users = "⚡️ Команде <b>" . $user->name . "</b> добавлено <b>" . $secondNumber . "</b> очков!\n";

                event(new Score($user));

                Telegram::sendMessage([
                    'chat_id' => config('telegram.channel'),
                    'parse_mode' => 'HTML',
                    'text' => $text_to_users,
                ]);
                break;
            case '/remove':
                $user = User::find($number);
                if (is_null($user)) {
                    $text_to_admin = "Команды с таким ID не существует!\n";
                    break;
                }
                $score_to_save = $user->score;
                $user->score = $score_to_save - $secondNumber;
                $user->save();
                $text_to_admin = "Команде <b>" . $user->name . "</b> успешно отнято <b>" . $secondNumber . "</b> очков!\n";
                $text_to_users = "⚡️ Команде <b>" . $user->name . "</b> отнято <b>" . $secondNumber . "</b> очков!\n";

                event(new Score($user));

                Telegram::sendMessage([
                    'chat_id' => config('telegram.channel'),
                    'parse_mode' => 'HTML',
                    'text' => $text_to_users,
                ]);
                break;
            default:
                if ($task != null) {
                    switch ($command) {
                        case '/done':
                            if ($task->user_id == 0) {
                                $text_to_admin = "Статус задания <b>№$number</b> не может быть Выполнено: Задание не закреплено ни за какой командой!\n";
                                break;
                            }
                            $task->status = 3;
                            $text_to_admin = "Теперь статус задания <b>№$number</b> : Выполнено!\n";

                            $score_to_save = $task->user->score;
                            $user = $task->user;
                            $user->score = $score_to_save + $task->score;
                            $user->save();

                            event(new Score($user));

                            $text_to_users = "🎉 Задание <b>" . $task->name . "</b> успешно выполнено командой <b>" . $task->user->name . "</b>.";
                            Telegram::sendMessage([
                                'chat_id' => config('telegram.channel'),
                                'parse_mode' => 'HTML',
                                'text' => $text_to_users,
                            ]);
                            break;
                        case '/work':
                            if ($task->user_id == 0) {
                                $text_to_admin = "Статус задания <b>№$number</b> не может быть В работе: Задание не закреплено ни за какой командой!\n";
                                break;
                            }
                            $task->status = 1;
                            $text_to_admin = "Теперь статус задания <b>№$number</b> : В работе!\n";
                            $text_to_users = "⚠️ Задание <b>" . $task->name . "</b> выполняемое командой <b>" . $task->user->name . "</b> требует доработки. Внимательно "
                                . "проверьте требования к заданию и повторите загрузку соответствующих материалов.️";

                            Telegram::sendMessage([
                                'chat_id' => config('telegram.channel'),
                                'parse_mode' => 'HTML',
                                'text' => $text_to_users,
                            ]);
                            break;
                        case '/ban':
                            $ban = new Ban;
                            $ban->user_id = $task->user_id;
                            $ban->task_id = $task->id;
                            $ban->save();

                            $text_to_admin = "Теперь статус задания <b>№$number</b> : Открыто!\nДля команды <b>" . $task->user->name . "</b> доступ к заданию закрыт!";
                            $text_to_users = "🎲 Задание <b>" . $task->name . "</b> снова доступно для выполнения всеми командами.\n🚧 Команда <b>" . $task->user->name . "</b> провалила выполнение этого задания.";
                            $task->status = 0;
                            $task->user_id = 0;

                            Telegram::sendMessage([
                                'chat_id' => config('telegram.channel'),
                                'parse_mode' => 'HTML',
                                'text' => $text_to_users,
                            ]);
                            break;
                        case '/clear':
                            $task->user_id = 0;
                            $task->status = 0;
                            $task->save();

                            $text_to_admin = "Теперь статус задания <b>№$number</b> : Открыто!\n";
                            $text_to_users = "🎲 Задание <b>" . $task->name . "</b> снова доступно для выполнения всеми командами.";
                            $task->status = 0;
                            $task->user_id = 0;

                            Telegram::sendMessage([
                                'chat_id' => config('telegram.channel'),
                                'parse_mode' => 'HTML',
                                'text' => $text_to_users,
                            ]);
                            break;
                        default:$text_to_admin = "<b>Такая задача не существует!</b>\n";
                            break;
                    }
                    $task->save();
                    
                    event(new TaskUpdate($task));
                } else {
                    $text_to_admin = "<b>Несуществующая команда!</b>\n";
                }
                break;
        }
        Telegram::sendMessage([
            'chat_id' => '-1001308540909',
            'parse_mode' => 'HTML',
            'text' => $text_to_admin,
        ]);
        return response('ok', 200);
    }
}
