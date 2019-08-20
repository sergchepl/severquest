<?php

namespace App\Http\Middleware;

use Closure;
use App\Task;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\FileUpload\InputFile;

class TelegramSendPhoto
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
    public function terminate($request, $response)
    {
    $photo = ($request->file('files')) ? $request->file('files') : NULL;
    $task = Task::find($request->task_id);

    if($task->type == 1) {
        $text = "<b>Задание №".$task->id." пришло на проверку!</b>\n"
        . "Название : ".$task->name."\n"
        . "Команда : ".$task->user->name."\n"
        . "Сообщение от команды : ".$request->text;
        $text_to_users = "💡 Задание <b>".$task->name."</b> проверяется администратором, ожидайте результат проверки.";
    }
    if($task->type == 2) {
        $text = "<b>🔥 Общее задание №".$task->id." пришло на проверку!</b> 🔥\n"
        . "Название : ".$task->name."\n"
        . "Команда : ".\Auth::user()->name."\n"
        . "Сообщение от команды : ".$request->text;
        $text_to_users = "🔥 Общее задание <b>".$task->name."</b> команды <b>".\Auth::user()->name."</b> успешно сдано и проверяется администратором, ответ будет в конце игры SeverQuest.";
    }

    $message = Telegram::sendMessage([
        'chat_id' => '-1001308540909',
        'parse_mode' => 'HTML',
        'text' => $text,
        'reply_markup' => json_encode(
            [
                'inline_keyboard' => [[
                    [
                        'text' => 'Выполнить',
                        'callback_data' => json_encode([
                            'type' => 'task',
                            'data' => [
                                'task_id' => $task->id,
                                'status' => 3,
                            ]
                        ]),
                    ],
                    [
                        'text' => 'В Работу',
                        'callback_data' => json_encode([
                            'type' => 'task',
                            'data' => [
                                'task_id' => $task->id,
                                'status' => 1,
                            ]
                        ]),
                    ],
                    [
                        'text' => 'Очистить',
                        'callback_data' => json_encode([
                            'type' => 'task',
                            'data' => [
                                'task_id' => $task->id,
                                'status' => 0,
                            ]
                        ]),
                    ],
                    [
                        'text' => 'Забанить',
                        'callback_data' => json_encode([
                            'type' => 'task',
                            'data' => [
                                'task_id' => $task->id,
                                'status' => 4,
                            ]
                        ]),
                    ]
            
                ]]
            ])
    ]);

    if(!is_null($photo)) {
        foreach($photo as $ph){
            Telegram::sendPhoto([
                'chat_id' => '-1001308540909',
                'photo' => InputFile::createFromContents(file_get_contents($ph->getRealPath()), str_random(10) . '.' . $ph->getClientOriginalExtension()),
                'reply_to_message_id' => $message->message_id,
            ]);
        }
    }
    
    Telegram::sendMessage([
        'chat_id' => config('telegram.channel'),
        'parse_mode' => 'HTML',
        'text' => $text_to_users
    ]);
  }
}
