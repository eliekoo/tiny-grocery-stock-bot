<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{

    public function sendMessage(
        int|string $chatId,
        string $message,
        array $keyboard = null
    ) {

        $data = [
            'chat_id' => $chatId,
            'text' => $message,
        ];


        if ($keyboard) {

            $data['reply_markup'] = json_encode([
                'keyboard' => $keyboard,
                'resize_keyboard' => true,
                'one_time_keyboard' => false,
            ]);
        }


        return Http::post(
            "https://api.telegram.org/bot"
                . config('telegram.bot_token')
                . "/sendMessage",
            $data
        );
    }
}
