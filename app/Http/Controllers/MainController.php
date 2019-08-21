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

            $textToAdmin = "<b>Задание №" . $task->id . " пришло на проверку!</b>\n"
            . "Название : " . $task->name . "\n"
            . "Команда : " . $task->user->name . "\n"
            . "Сообщение от команды : " . $request->text;
            $textToUsers = "💡 Задание <b>" . $task->name . "</b> проверяется администратором, ожидайте результат проверки.";

            $replyButtons = [
                KeyboardButton::createSingleTaskButton('Выполнить', ['taskId' => $task->id, 'status' => 3]),
                KeyboardButton::createSingleTaskButton('В Работу', ['taskId' => $task->id, 'status' => 1]),
                KeyboardButton::createSingleTaskButton('Очистить', ['taskId' => $task->id, 'status' => 0]),
                KeyboardButton::createSingleTaskButton('Забанить', ['taskId' => $task->id, 'status' => 4]),
            ];
        }
        if ($task->type == 2) {
            $ban = Ban::banTask(Auth::user()->id, $task->id);

            event(new BanUpdate($ban, true));

            $textToAdmin = "<b>🔥 Общее задание №" . $task->id . " пришло на проверку!</b> 🔥\n"
            . "Название : " . $task->name . "\n"
            . "Команда : " . \Auth::user()->name . "\n"
            . "Сообщение от команды : " . $request->text;
            $textToUsers = "🔥 Общее задание <b>" . $task->name . "</b> команды <b>" . \Auth::user()->name . "</b> успешно сдано и проверяется администратором, ответ будет в конце игры SeverQuest.";

            $replyButtons = [
                KeyboardButton::createCommonTaskButton('Засчитать выполнение команде', ['taskId' => $task->id, 'userId' => Auth::user()->id]),
            ];
        }
        $message = $this->sendTelegramMessage($textToAdmin, $replyButtons, '-1001308540909');

        if (!is_null($photo)) {
            foreach ($photo as $ph) {
                $photo = InputFile::createFromContents(file_get_contents($ph->getRealPath()), str_random(10) . '.' . $ph->getClientOriginalExtension());
                $messageId = $message->message_id;

                $this->sendTelegramPhoto($photo, $messageId);
            }
        }
        $this->sendTelegramMessage($textToUsers);

        return response('ok', 200);
    }

    public function webhook(Request $request)
    {
        $callback = (new CallbackQuery($request->toArray()))->callback_query;
        \Log::info($callback);

        if (is_null($callback)) {
            \Log::info($request->toArray());
            return response('Nothing', 204);
        }
        $callbackId = $callback->id;
        $messageId = $callback->message->message_id;
        $callbackData = json_decode($callback->data);

        if ($callbackData->type == 'single-task') {
            $answer = $this->webhookSingleTaskKeyboard($callbackData->data);
        }
        if ($callbackData->type == 'common-task') {
            $answer = $this->webhookCommonTaskKeyboard($callbackData->data);
        }
        $this->sendTelegramMessage($answer['toUsers']);
        $this->clearMessageReplyMarkup($messageId);
        $this->sendAnswerCallbackQuery($callbackId, $answer['toAdmin']);

        return response('ok', 200);
    }
}
