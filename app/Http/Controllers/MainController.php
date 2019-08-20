<?php

namespace App\Http\Controllers;

use App\Ban;
use App\Events\BanUpdate;
use App\Events\Score;
use App\Events\TaskUpdate;
use App\Task;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Exceptions\TelegramResponseException;

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

        if (is_null($request->callback_query)) {
            // \Log::info($request->toArray());
            return response('Nothing', 204);
        }
        $callback_query_id = $request->callback_query['id'];
        $message_id = $request->callback_query['message']['message_id'];
        $callback_data = json_decode($request->callback_query['data']);

        if ($callback_data->type == 'task') {
            static::webhookTaskKeyboard($callback_data->data, $callback_query_id);
            static::clearMessageReplyMarkup($message_id);
        }

        if ($callback_data->type == 'command') {
            static::webhookCommandKeyboard($callback_data->data, $callback_query_id);
        }
        return response('ok', 200);

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
        }
        Telegram::sendMessage([
            'chat_id' => '-1001308540909',
            'parse_mode' => 'HTML',
            'text' => $text_to_admin,
        ]);
    }

    private function sendTelegramMessage($text, $chat_id = null)
    {
        return Telegram::sendMessage([
            'chat_id' => $chat_id ?? config('telegram.channel'),
            'parse_mode' => 'HTML',
            'text' => $text,
        ]);
    }

    private function sendAnswerCallbackQuery($query_id, $text)
    {
        try {
            $response = Telegram::answerCallbackQuery([
                'callback_query_id' => $query_id,
                'text' => $text,
            ]);

            return $response;
        } catch (TelegramResponseException $e) {
            \Log::debug($e);
        }
    }

    private function clearMessageReplyMarkup($message, $chat = '-1001308540909')
    {
        return Telegram::editMessageReplyMarkup([
           'chat_id' => $chat,
           'message_id' => $message,
           'reply_markup' => json_encode(['inline_keyboard' => [[]]])
        ]);
    }

    private function webhookTaskKeyboard($button, $callback_query_id)
    {
        $task = Task::find($button->task_id);

        switch ($button->status) {
            case 0:
                $task->clear();

                $text_to_users = "🎲 Задание <b>" . $task->name . "</b> снова доступно для выполнения всеми командами.";
                $text_to_admin = "✅ Задание успешно очищено!\n";
                break;
            case 1:
                $task->work();

                $text_to_users = "⚠️ Задание <b>" . $task->name . "</b> выполняемое командой <b>" . $task->user->name . "</b> требует доработки. Внимательно "
                    . "проверьте требования к заданию и повторите загрузку соответствующих материалов.️";
                $text_to_admin = "✅ Задание успешно очищено!\n";
                break;
            case 3:
                $task->done();
                $user = $task->user->addScore($task->score);

                event(new Score($task->user));

                $text_to_users = "🎉 Задание <b>" . $task->name . "</b> успешно выполнено командой <b>" . $task->user->name . "</b>.";
                $text_to_admin = "✅ Задание успешно выполнено!\n";
                break;
            case 4:
                $task->clear();
                Ban::banTask($task->id);

                event(new BanUpdate($ban, true));

                $text_to_users = "🎲 Задание <b>" . $task->name . "</b> снова доступно для выполнения всеми командами.\n🚧 Команда <b>" . $task->user->name . "</b> провалила выполнение этого задания.";
                $text_to_admin = "✅ Задание успешно забанено!\n";
                break;
        }
        event(new TaskUpdate($task));

        static::sendTelegramMessage($text_to_users);
        static::sendAnswerCallbackQuery($callback_query_id, $text_to_admin);
    }
}
