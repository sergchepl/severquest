<?php

namespace App\Helpers;

class KeyboardButton
{
    
    public static function createSingleTaskButton(string $buttonText, array $buttonData = [])
    {
        return self::create($buttonText, $buttonData, 'single-task');
    }
    
    public static function createCommonTaskButton(string $buttonText, array $buttonData = [])
    {
        return self::create($buttonText, $buttonData, 'common-task');
    }
    
    public static function create(string $buttonText, array $buttonData = [], string $type = '')
    {
        return [
            'text' => $buttonText,
            'callback_data' => json_encode([
                'type' => $type,
                'data' => $buttonData,
            ]),
        ];
    }
}
