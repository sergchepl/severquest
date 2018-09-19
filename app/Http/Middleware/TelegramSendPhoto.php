<?php

namespace App\Http\Middleware;

use Closure;
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
        
    
    if($request->task_type == 1) {
        $text = "<b>Задание №".$request->task_id." пришло на проверку!</b>\n"
        . "Название : ".$request->task."\n"
        . "Описание задания: ".$request->task_text."\n"
        . "Команда : ".$request->team."\n"
        . "Сообщение от команды : ".$request->text;
        $text_to_users = "💡 Задание <b>".$request->task."</b> проверяется администратором, ожидайте результат проверки.";
    }
    if($request->task_type == 2) {
        $text = "<b>🔥 Общее задание №".$request->task_id." пришло на проверку!</b> 🔥\n"
        . "Название : ".$request->task."\n"
        . "Описание задания: ".$request->task_text."\n"
        . "Команда : ".$request->team."\n"
        . "Сообщение от команды : ".$request->text;
        $text_to_users = "🔥 Общее задание <b>".$request->task."</b> команды <b>".$request->team."</b>успешно сдано и проверяется администратором, ответ будет в конце игры SeverQuest.";
    }

    Telegram::sendMessage([
        'chat_id' => '-1001308540909',
        'parse_mode' => 'HTML',
        'text' => $text
    ]);
    if($photo != NULL) {
        foreach($photo as $ph){
            Telegram::sendPhoto([
                'chat_id' => '-1001308540909',
                'photo' => InputFile::createFromContents(file_get_contents($ph->getRealPath()), str_random(10) . '.' . $ph->getClientOriginalExtension())
            ]);
        }
    }
    
    Telegram::sendMessage([
        'chat_id' => env('TELEGRAM_CHANNEL_ID', ''),
        'parse_mode' => 'HTML',
        'text' => $text_to_users
    ]);
  }
}
