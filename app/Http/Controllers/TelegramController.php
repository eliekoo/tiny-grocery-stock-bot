<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\TelegramService;
use App\Services\CommandParser;
use App\Services\InventoryService;

class TelegramController extends Controller
{

    public function __construct(
        protected TelegramService $telegram,
        protected CommandParser $parser,
        protected InventoryService $inventory
    ) {}


    public function webhook(Request $request)
    {

        Log::info(
            'Telegram Update',
            $request->all()
        );


        $chatId = $request->input(
            'message.chat.id'
        );


        $text = $request->input(
            'message.text'
        );


        if (!$chatId || !$text) {

            return response()->json([
                'ok' => true,
            ]);
        }


        $command = $this->parser->parse($text);


        Log::info(
            'Parsed Command',
            $command
        );


        $reply = $this->handleCommand($command);


        $this->telegram->sendMessage(
            $chatId,
            $reply
        );


        return response()->json([
            'ok' => true,
        ]);
    }



    private function handleCommand(array $command): string
    {

        if ($command['action'] === 'UNKNOWN') {

            return
                "❓ I don't understand.\n\n"
                . "Try:\n"
                . "/add lotus milk 6\n"
                . "/use drypers 1\n"
                . "/list";
        }


        if ($command['action'] === 'ADD') {


            $result = $this->inventory
                ->addStock(
                    $command['keyword'],
                    $command['quantity'],
                    'Telegram'
                );


            if (!$result['success']) {

                return "❌ "
                    . $result['message'];
            }


            return
                "✅ Added\n\n"
                . $result['variant']['name']
                . "\n+"
                . $result['quantity_added']
                . " "
                . $result['variant']['unit']
                . "\n\nCurrent stock: "
                . $result['current_stock']
                . " "
                . $result['variant']['unit'];
        }



        if ($command['action'] === 'USE') {


            $result = $this->inventory
                ->useStock(
                    $command['keyword'],
                    $command['quantity'],
                    'Telegram'
                );


            if (!$result['success']) {

                return "❌ "
                    . $result['message'];
            }


            return
                "➖ Used\n\n"
                . $result['variant']['name']
                . "\n-"
                . $result['quantity_used']
                . " "
                . $result['variant']['unit']
                . "\n\nCurrent stock: "
                . $result['current_stock']
                . " "
                . $result['variant']['unit'];
        }


        return "Coming soon";
    }
}
