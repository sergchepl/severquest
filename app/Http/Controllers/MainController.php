<?php

namespace App\Http\Controllers;

use App\Ban;
use App\Events\Score;
use App\Events\TaskUpdate;
use App\Events\BanUpdate;
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

    public function takeTask(Task $task)
    {
        $teamTasks = Task::whereUserId(Auth::user()->id)->whereStatus(1)->get();

        if (count($teamTasks) > 0) {
            return response('Have another task', 409);
        }
        $task->take(Auth::user()->id);
        event(new TaskUpdate($task));
 
        static::sendTelegramMessage("🚲 Команда <b>" . $task->user->name . "</b> приступила к выполнению задания <b>" . $task->name . "</b>.");

        return 200;
    }

    public function cancelTask(Task $task)
    {
        $task->clear();
        event(new TaskUpdate($task));
 
        static::sendTelegramMessage("🎲 Задание <b>" . $task->name . "</b> снова доступно для выполнения всеми командами.");

        return 200;
    }

    public function setScore(Request $request)
    {
        $user = User::find(Auth::user()->id);

        $user->readRules($request->score);

        return response($request->score, 200);
    }

    public function checkTask(Request $request, Task $task)
    {
        if ($task->type == 1) {
            $task->check();

            event(new TaskUpdate($task));
        }
        if ($task->type == 2) {
            $ban = Ban::banTask($task->id); 

            event(new BanUpdate($ban, true));
        }

        return response('ok', 200);
    }

    public function webhook(Request $request)
    {
        \Log::debug($request->toArray());
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

                if (is_null($user)) {
                    $text_to_admin = "Команды <b>№" . $number . "</b> не существует!\n";
                    break;
                }
                $user->score = 0;
                $user->save();
                $text_to_admin = "Прогресс команды <b>" . $user->name . "</b> обнулен!\n";
                
                event(new Score($user));
                break;
            case '/clear_ban':
                $user = User::find($number);
                $task = Task::find($secondNumber);
                $ban = Ban::where('user_id', $number)->where('task_id', $secondNumber)->first();
                if (is_null($ban)) {
                    $text_to_admin = "Задание <b>" . $task->name . "</b> команды <b>" . $user->name . "</b> не было заблокировано!\n";
                    break;
                }
                $ban->delete();
                
                $text_to_admin = "Задание <b>" . $task->name . "</b> команды <b>" . $user->name . "</b> разбанено!\n";
                $text_to_users = "🚦 Задание <b>" . $task->name . "</b> команды <b>" . $user->name . "</b> разбанено!\n";
                
                event(new BanUpdate($ban, false));
                
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
                            break;
                        case '/ban':
                            if ($task->user_id == 0) 
                                return response('Nothing', 204);
                                
                            $ban = new Ban;
                            $ban->user_id = $task->user_id;
                            $ban->task_id = $task->id;
                            $ban->save();

                            event(new BanUpdate($ban, true));

                            $text_to_admin = "Теперь статус задания <b>№$number</b> : Открыто!\nДля команды <b>" . $task->user->name . "</b> доступ к заданию закрыт!";
                            $text_to_users = "🎲 Задание <b>" . $task->name . "</b> снова доступно для выполнения всеми командами.\n🚧 Команда <b>" . $task->user->name . "</b> провалила выполнение этого задания.";
                            $task->status = 0;
                            $task->user_id = 0;
                            break;
                        case '/clear':
                            $task->user_id = 0;
                            $task->status = 0;

                            $text_to_admin = "Теперь статус задания <b>№$number</b> : Открыто!\n";
                            $text_to_users = "🎲 Задание <b>" . $task->name . "</b> снова доступно для выполнения всеми командами.";
                            break;
                        default:
                            $text_to_admin = "<b>Такая задача не существует!</b>\n";
                            break;
                    }
                    $task->save();
                    
                    event(new TaskUpdate($task));
                    
                    Telegram::sendMessage([
                        'chat_id' => config('telegram.channel'),
                        'parse_mode' => 'HTML',
                        'text' => $text_to_users,
                    ]);
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

    private function sendTelegramMessage($text, $chat_id = NULL)
    {
        return Telegram::sendMessage([
            'chat_id' => $chat_id ?? config('telegram.channel'),
            'parse_mode' => 'HTML',
            'text' => $text,
        ]);
    }
    
}
