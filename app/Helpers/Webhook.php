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
    private function sendTelegramMessage(string $text, array $buttons = [], $chatId = null)
    {
        return Telegram::sendMessage([
            'chat_id' => $chatId ?? config('telegram.channel'),
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

    private function sendAnswerCallbackQuery($queryId, $text)
    {
        try {
            return Telegram::answerCallbackQuery([
                'callback_query_id' => $queryId,
                'text' => $text,
            ]);
        } catch (TelegramResponseException $e) {}
    }

    private function clearMessageReplyMarkup($message, $chat = '-1001308540909')
    {
        try {
            return Telegram::editMessageReplyMarkup([
               'chat_id' => $chat,
               'message_id' => $message,
               'reply_markup' => json_encode(['inline_keyboard' => [[]]])
            ]);
        } catch (TelegramResponseException $e) {}
    }

    private function webhookSingleTaskKeyboard($button)
    {
        $task = Task::find($button->taskId);

        switch ($button->status) {
            case 0:
                $task->clear();

                $textToUsers = "🎲 Задание <b>" . $task->name . "</b> снова доступно для выполнения всеми командами.";
                $textToAdmin = "🎲 Задание успешно очищено!\n";
                break;
            case 1:
                $task->work();

                $textToUsers = "⚠️ Задание <b>" . $task->name . "</b> выполняемое командой <b>" . $task->user->name . "</b> требует доработки. Внимательно "
                    . "проверьте требования к заданию и повторите загрузку соответствующих материалов.️";
                $textToAdmin = "⚠️ Задание отправлено обратно в работу!\n";
                break;
            case 3:
                $task->done();
                $user = $task->user->addScore($task->score);

                event(new ScoreUpdate($task->user));

                $textToUsers = "🎉 Задание <b>" . $task->name . "</b> успешно выполнено командой <b>" . $task->user->name . "</b>.";
                $textToAdmin = "🎉 Задание успешно выполнено!\n";
                break;
            case 4:
                $ban = Ban::banTask($task->user->id, $task->id);
                $task->clear();

                event(new BanUpdate($ban, true));

                $textToUsers = "🎲 Задание <b>" . $task->name . "</b> снова доступно для выполнения всеми командами.\n🚧 Команда <b>" . $task->user->name . "</b> провалила выполнение этого задания.";
                $textToAdmin = "🚧 Задание успешно забанено!\n";
                break;
        }
        event(new TaskUpdate($task));

        return [
            'toUsers' => $textToUsers,
            'toAdmin' => $textToAdmin
        ];
    }

    private function webhookCommonTaskKeyboard($button)
    {
        $task = Task::find($button->taskId);
        $user = User::find($button->userId);

        $user->addScore($task->score);
        event(new ScoreUpdate($user));

        return [
            'toUsers' => "🎉 Выполнение общего задания <b>" . $task->name . "</b> засчитано команде <b>" . $user->name . "</b>.",
            'toAdmin' => "✅ Задание успешно засчитано!\n"
        ];
    }
}
