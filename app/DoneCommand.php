<?php

namespace Vendor\App\Commands;

use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Illuminate\Support\Facades\Log;

class DoneCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "done";

    /**
     * @var string Command Description
     */
    protected $description = "Команда для завершения задачи";

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        Log::info($arguments);
        $text = "<b>Задание №".$taskId."</b> успешно отмечено как выполненное!\n";
        $this->replyWithMessage(['text' => $text]);
    }
}