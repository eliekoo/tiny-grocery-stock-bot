<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{
    public function sendMessage(
        int|string $chatId,
        string $message
    ) {

        return Http::post(
            "https://api.telegram.org/bot"
                . config('telegram.bot_token')
                . "/sendMessage",
            [
                'chat_id' => $chatId,
                'text' => $message,
            ]
        );
    }
}
