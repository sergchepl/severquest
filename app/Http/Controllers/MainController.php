<?php

namespace App\Http\Controllers;

use App\Ban;
use App\Events\BanUpdate;
use App\Events\TaskUpdate;
use App\Helpers\KeyboardButton;
use App\Helpers\Webhook;
use App\Task;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Objects\CallbackQuery;
use Telegram\Bot\Laravel\Facades\Telegram;

class MainController extends Controller
{
    use Webhook;

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

        $this->sendTelegramMessage("🚲 Команда <b>" . $task->user->name . "</b> приступила к выполнению задания <b>" . $task->name . "</b>.");

        return 200;
    }

    public function cancelTask(Task $task)
    {
        $task->clear();
        event(new TaskUpdate($task));

        $this->sendTelegramMessage("🎲 Задание <b>" . $task->name . "</b> снова доступно для выполнения всеми командами.");

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
        $photo = ($request->file('files')) ? $request->file('files') : null;

        if ($task->type == 1) {
            $task->check();

            event(new TaskUpdate($task));

            $text_to_admin = "<b>Задание №" . $task->id . " пришло на проверку!</b>\n"
            . "Название : " . $task->name . "\n"
            . "Команда : " . $task->user->name . "\n"
            . "Сообщение от команды : " . $request->text;
            $text_to_users = "💡 Задание <b>" . $task->name . "</b> проверяется администратором, ожидайте результат проверки.";

            $reply_buttons = [
                KeyboardButton::createSingleTaskButton('Выполнить', ['task_id' => $task->id, 'status' => 3]),
                KeyboardButton::createSingleTaskButton('В Работу', ['task_id' => $task->id, 'status' => 1]),
                KeyboardButton::createSingleTaskButton('Очистить', ['task_id' => $task->id, 'status' => 0]),
                KeyboardButton::createSingleTaskButton('Забанить', ['task_id' => $task->id, 'status' => 4]),
            ];
        }
        if ($task->type == 2) {
            $ban = Ban::banTask(Auth::user()->id, $task->id);

            event(new BanUpdate($ban, true));

            $text_to_admin = "<b>🔥 Общее задание №" . $task->id . " пришло на проверку!</b> 🔥\n"
            . "Название : " . $task->name . "\n"
            . "Команда : " . \Auth::user()->name . "\n"
            . "Сообщение от команды : " . $request->text;
            $text_to_users = "🔥 Общее задание <b>" . $task->name . "</b> команды <b>" . \Auth::user()->name . "</b> успешно сдано и проверяется администратором, ответ будет в конце игры SeverQuest.";

            $reply_buttons = [
                KeyboardButton::createCommonTaskButton('Засчитать выполнение команде', ['task_id' => $task->id, 'user_id' => Auth::user()->id]),
            ];
        }
        $message = $this->sendTelegramMessage($text_to_admin, $reply_buttons, '-1001308540909');

        if (!is_null($photo)) {
            foreach ($photo as $ph) {
                $photo = InputFile::createFromContents(file_get_contents($ph->getRealPath()), str_random(10) . '.' . $ph->getClientOriginalExtension());
                $message_id = $message->message_id;

                $this->sendTelegramPhoto($photo, $message_id);
            }
        }

        $this->sendTelegramMessage($text_to_users);

        return response('ok', 200);
    }

    public function webhook(Request $request)
    {
        $callback = new CallbackQuery($request->toArray());
        \Log::info($callback);

        if (is_null($request->callback_query)) {
            \Log::info($request->toArray());
            return response('Nothing', 204);
        }
        $callback_query_id = $callback->callback_query->id;
        $message_id = $callback->callback_query->message->message_id;
        $callback_data = json_decode($callback->callback_query->data);

        if ($callback_data->type == 'single-task') {
            $answer = $this->webhookSingleTaskKeyboard($callback_data->data);
        }
        if ($callback_data->type == 'common-task') {
            $answer = $this->webhookCommonTaskKeyboard($callback_data->data);
        }
        $this->sendTelegramMessage($answer['to_users']);
        $this->clearMessageReplyMarkup($message_id);
        $this->sendAnswerCallbackQuery($callback_query_id, $answer['to_admin']);

        return response('ok', 200);

        // if ($updates->channel_post == null || $updates->channel_post->chat->id != -1001308540909) {
        //     return response('Nothing', 204);
        // }

        // $commandText = $updates->channel_post->text;
        // $commandArray = explode(' ', $commandText);
        // $command = $commandArray[0];
        // $number = count($commandArray) > 1 ? (int) $commandArray[1] : 1;
        // $secondNumber = count($commandArray) > 2 ? (int) $commandArray[2] : 1;
        // $text_to_admin = "";

        // $task = Task::find($number);
        // switch ($command) {
        //     case '/list':
        //         $users = User::orderBy('score', 'desc')->get();
        //         foreach ($users as $k => $user) {
        //             switch ($k) {
        //                 case 0:$text_to_admin .= "------------------🥇------------------\n<b>ID команды:</b> " . $user->id
        //                     . "\n<b>Название команды:</b> " . $user->name . "\n<b>Количество баллов:</b> " . $user->score . "\n";
        //                     break;
        //                 case 1:$text_to_admin .= "------------------🥈------------------\n<b>ID команды:</b> " . $user->id
        //                     . "\n<b>Название команды:</b> " . $user->name . "\n<b>Количество баллов:</b> " . $user->score . "\n";
        //                     break;
        //                 case 2:$text_to_admin .= "------------------🥉------------------\n<b>ID команды:</b> " . $user->id
        //                     . "\n<b>Название команды:</b> " . $user->name . "\n<b>Количество баллов:</b> " . $user->score . "\n";
        //                     break;
        //                 default:$text_to_admin .= "--------------------------------------\n<b>ID команды:</b> " . $user->id
        //                     . "\n<b>Название команды:</b> " . $user->name . "\n<b>Количество баллов:</b> " . $user->score . "\n";
        //                     break;
        //             }
        //         }
        //         $text_to_admin .= "--------------------------------------\n";
        //         break;
        //     case '/clear_team':
        //         $user = User::find($number);

        //         if (is_null($user)) {
        //             $text_to_admin = "Команды <b>№" . $number . "</b> не существует!\n";
        //             break;
        //         }
        //         $user->score = 0;
        //         $user->save();
        //         $text_to_admin = "Прогресс команды <b>" . $user->name . "</b> обнулен!\n";

        //         event(new Score($user));
        //         break;
        //     case '/clear_ban':
        //         $user = User::find($number);
        //         $task = Task::find($secondNumber);
        //         $ban = Ban::where('user_id', $number)->where('task_id', $secondNumber)->first();
        //         if (is_null($ban)) {
        //             $text_to_admin = "Задание <b>" . $task->name . "</b> команды <b>" . $user->name . "</b> не было заблокировано!\n";
        //             break;
        //         }
        //         $ban->delete();

        //         $text_to_admin = "Задание <b>" . $task->name . "</b> команды <b>" . $user->name . "</b> разбанено!\n";
        //         $text_to_users = "🚦 Задание <b>" . $task->name . "</b> команды <b>" . $user->name . "</b> разбанено!\n";

        //         event(new BanUpdate($ban, false));

        //         Telegram::sendMessage([
        //             'chat_id' => config('telegram.channel'),
        //             'parse_mode' => 'HTML',
        //             'text' => $text_to_users,
        //         ]);
        //         break;
        //     case '/add':
        //         $user = User::find($number);
        //         if (is_null($user)) {
        //             $text_to_admin = "Команды с таким ID не существует!\n";
        //             break;
        //         }
        //         $score_to_save = $user->score;
        //         $user->score = $score_to_save + $secondNumber;
        //         $user->save();
        //         $text_to_admin = "Команде <b>" . $user->name . "</b> успешно добавлено <b>" . $secondNumber . "</b> очков!\n";
        //         $text_to_users = "⚡️ Команде <b>" . $user->name . "</b> добавлено <b>" . $secondNumber . "</b> очков!\n";

        //         event(new Score($user));

        //         Telegram::sendMessage([
        //             'chat_id' => config('telegram.channel'),
        //             'parse_mode' => 'HTML',
        //             'text' => $text_to_users,
        //         ]);
        //         break;
        //     case '/remove':
        //         $user = User::find($number);
        //         if (is_null($user)) {
        //             $text_to_admin = "Команды с таким ID не существует!\n";
        //             break;
        //         }
        //         $score_to_save = $user->score;
        //         $user->score = $score_to_save - $secondNumber;
        //         $user->save();
        //         $text_to_admin = "Команде <b>" . $user->name . "</b> успешно отнято <b>" . $secondNumber . "</b> очков!\n";
        //         $text_to_users = "⚡️ Команде <b>" . $user->name . "</b> отнято <b>" . $secondNumber . "</b> очков!\n";

        //         event(new Score($user));

        //         Telegram::sendMessage([
        //             'chat_id' => config('telegram.channel'),
        //             'parse_mode' => 'HTML',
        //             'text' => $text_to_users,
        //         ]);
        //         break;
        // }
        // Telegram::sendMessage([
        //     'chat_id' => '-1001308540909',
        //     'parse_mode' => 'HTML',
        //     'text' => $text_to_admin,
        // ]);
    }
}
