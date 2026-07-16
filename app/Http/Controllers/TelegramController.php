<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TelegramController extends Controller
{
    public function webhook(Request $request)
    {
        $message = $request->input('message.text');
        $chatId = $request->input('message.chat.id');

        Http::post(
            "https://api.telegram.org/bot" . config('telegram.bot_token') . "/sendMessage",
            [
                'chat_id' => $chatId,
                'text' => "Hello Elie 👋\n\nLaravel received:\n" . $message,
            ]
        );

        return response()->json([
            'ok' => true,
        ]);
    }
}
