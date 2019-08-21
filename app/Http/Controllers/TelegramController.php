<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\TelegramHelper;
use Telegram\Bot\Objects\CallbackQuery;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramController extends Controller
{
    use TelegramHelper;

    public function setWebhook() {
        $response = Telegram::setWebhook(['url' => url('/AAG1RIo_ym-2We-yuTsN8IWg8Jlex7lEY4s/webhook')]);
        dd($response);
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