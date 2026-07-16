<?php

namespace App\Services;

use App\Models\TelegramSession;


class TelegramSessionService
{

    public function get($telegramId)
    {
        return TelegramSession::firstOrCreate(
            [
                'telegram_id' => $telegramId
            ]
        );
    }


    public function set(
        $telegramId,
        string $state,
        array $data = []
    ) {

        return TelegramSession::updateOrCreate(
            [
                'telegram_id' => $telegramId
            ],
            [
                'state' => $state,
                'data' => $data,
            ]
        );
    }


    public function clear($telegramId)
    {

        return TelegramSession::where(
            'telegram_id',
            $telegramId
        )
            ->update([
                'state' => null,
                'data' => null,
            ]);
    }
}
