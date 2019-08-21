<?php

namespace App\Helpers;
use App\User;
use App\Task;
use App\Ban;
use App\Events\TaskUpdate;
use App\Events\BanUpdate;
use App\Events\ScoreUpdate;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Exceptions\TelegramResponseException;

trait Webhook
{
    private function sendTelegramMessage(string $text, array $buttons = [], $chat_id = null)
    {
        return Telegram::sendMessage([
            'chat_id' => $chat_id ?? config('telegram.channel'),
            'parse_mode' => 'HTML',
            'text' => $text,
            'reply_markup' => json_encode(
                [
                    'inline_keyboard' => [
                        $buttons
                    ]
                ])
        ]);
    }

    private function sendTelegramPhoto($photo, int $replyMessageId)
    {
        return Telegram::sendPhoto([
            'chat_id' => '-1001308540909',
            'photo' => $photo,
            'reply_to_message_id' => $replyMessageId,
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
        } catch (TelegramResponseException $e) {}
    }

    private function clearMessageReplyMarkup($message, $chat = '-1001308540909')
    {
        return Telegram::editMessageReplyMarkup([
           'chat_id' => $chat,
           'message_id' => $message,
           'reply_markup' => json_encode(['inline_keyboard' => [[]]])
        ]);
    }

    private function webhookSingleTaskKeyboard($button)
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
                $text_to_admin = "✅ Задание отправлено обратно в работу!\n";
                break;
            case 3:
                $task->done();
                $user = $task->user->addScore($task->score);

                event(new ScoreUpdate($task->user));

                $text_to_users = "🎉 Задание <b>" . $task->name . "</b> успешно выполнено командой <b>" . $task->user->name . "</b>.";
                $text_to_admin = "✅ Задание успешно выполнено!\n";
                break;
            case 4:
                $ban = Ban::banTask($task->user->id, $task->id);
                $task->clear();

                event(new BanUpdate($ban, true));

                $text_to_users = "🎲 Задание <b>" . $task->name . "</b> снова доступно для выполнения всеми командами.\n🚧 Команда <b>" . $task->user->name . "</b> провалила выполнение этого задания.";
                $text_to_admin = "✅ Задание успешно забанено!\n";
                break;
        }
        event(new TaskUpdate($task));

        return [
            'to_users' => $text_to_users,
            'to_admin' => $text_to_admin
        ];
    }

    private function webhookCommonTaskKeyboard($button)
    {
        $task = Task::find($button->task_id);
        $user = User::find($button->user_id);

        $user->addScore($task->score);
        event(new ScoreUpdate($user));

        return [
            'to_users' => "🎉 Выполнение общего задания <b>" . $task->name . "</b> засчитано команде <b>" . $user->name . "</b>.",
            'to_admin' => "✅ Задание успешно засчитано!\n"
        ];
    }
}
