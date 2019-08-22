<?php

namespace App\Http\Controllers\API;

use App\Helpers\TelegramHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\CallbackQuery;

class TelegramController extends Controller
{
    use TelegramHelper;

    public function getWebhookInfo()
    {
        $response = Telegram::getWebhookInfo();
        dd($response);
    }

    public function setWebhook()
    {
        $response = Telegram::setWebhook(['url' => route('webhook')]);
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
