<?php

namespace App\Helpers;

class KeyboardButton
{
    public static function create(string $buttonText, array $buttonData = [], string $type = '')
    {
        return [
            'text' => $buttonText,
            'callback_data' => json_encode([
                'type' => $type,
                'data' => $buttonData,
            ])
        ];
    }

    public static function createSingleTaskButton(string $buttonText, array $buttonData = [])
    {
        return [
            'text' => $buttonText,
            'callback_data' => json_encode([
                'type' => 'single-task',
                'data' => $buttonData,
            ])
        ];
    }

    public static function createCommonTaskButton(string $buttonText, array $buttonData = [])
    {
        return [
            'text' => $buttonText,
            'callback_data' => json_encode([
                'type' => 'common-task',
                'data' => $buttonData,
            ])
        ];
    }
}
