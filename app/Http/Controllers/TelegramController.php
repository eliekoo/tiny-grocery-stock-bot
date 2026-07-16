<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    public function webhook(Request $request)
    {
        Log::info('Telegram Update', $request->all());

        $chatId = $request->input('message.chat.id');
        $text = $request->input('message.text');

        Http::post(
            "https://api.telegram.org/bot" . config('telegram.bot_token') . "/sendMessage",
            [
                'chat_id' => $chatId,
                'text' => "✅ Tiny Grocery Bot is online!\n\nYou sent: {$text}",
            ]
        );

        return response()->json([
            'ok' => true,
        ]);
    }
}
