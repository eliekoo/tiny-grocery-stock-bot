<?php

namespace App\Services;

class TelegramKeyboard
{

    public function main(): array
    {
        return [

            [
                "📦 Stock List",
                "➕ Add Stock"
            ],

            [
                "➖ Use Stock",
                "❓ Help"
            ]

        ];
    }
}
