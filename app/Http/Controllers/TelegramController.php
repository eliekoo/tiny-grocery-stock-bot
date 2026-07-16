<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\TelegramService;
use App\Services\CommandParser;
use App\Services\InventoryService;
use App\Services\InventoryFormatterService;
use App\Services\TelegramKeyboard;
use App\Services\TelegramSessionService;

class TelegramController extends Controller
{

    public function __construct(
        protected TelegramService $telegram,
        protected CommandParser $parser,
        protected InventoryService $inventory,
        protected InventoryFormatterService $formatter,
        protected TelegramKeyboard $keyboard,
        protected TelegramSessionService $session
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

        $session = $this->session->get($chatId);


        if ($session->state) {

            $reply = $this->handleSession(
                $session,
                $text,
                $chatId
            );
        } else {

            $command = $this->parser->parse($text);

            $reply = $this->handleCommand(
                $command,
                $chatId
            );
        }


        $this->telegram->sendMessage(
            $chatId,
            $reply,
            $this->keyboard->main()
        );


        return response()->json([
            'ok' => true,
        ]);
    }



    private function handleCommand(array $command, $chatId): string
    {
        if ($command['action'] === 'HELP') {

            return
                "🤖 Tiny Grocery Bot\n\n"
                . "You can type:\n\n"
                . "➕ Add:\n"
                . "lotus milk +6\n\n"
                . "➖ Use:\n"
                . "/use drypers 1\n\n"
                . "📦 View:\n"
                . "/list";
        }


        if ($command['action'] === 'ADD_MODE') {


            $this->session->set(
                $chatId,
                'ADD_MODE'
            );


            return
                "➕ Add Stock\n\n"
                . "Type:\n"
                . "product + quantity\n\n"
                . "Example:\n"
                . "Lotus Milk +2\n\n"
                . "Or just type product for +1";
        }


        if ($command['action'] === 'USE_MODE') {

            return
                "➖ Use Stock\n\n"
                . "Please type:\n\n"
                . "Product name - quantity\n\n"
                . "Example:\n"
                . "Drypers XL -1";
        }

        if ($command['action'] === 'LIST') {

            $items = $this->inventory
                ->listInventory();


            return $this->formatter
                ->formatList($items);
        }

        if ($command['action'] === 'ADJUST') {

            $result = $this->inventory->adjustStock(
                $command['keyword'],
                $command['quantity'],
                'Telegram Physical Count'
            );

            if (!$result['success']) {
                return "❌ " . $result['message'];
            }

            $difference = $result['difference'];

            if ($difference > 0) {
                $difference = '+' . $difference;
            }

            return
                "📝 Stock Adjusted\n\n"
                . $result['variant']['name']
                . "\n\n"
                . "Previous : {$result['old_quantity']} {$result['variant']['unit']}\n"
                . "Current  : {$result['current_stock']} {$result['variant']['unit']}\n"
                . "Difference : {$difference}";
        }

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

    private function handleSession(
        $session,
        string $text,
        $chatId
    ): string {


        if ($session->state === 'ADD_WAIT_PRODUCT') {


            $this->session->set(
                $chatId,
                'ADD_WAIT_QTY',
                [
                    'keyword' => $text
                ]
            );


            return
                "📦 Product:\n"
                . $text
                . "\n\n"
                . "How many did you buy?";
        }



        if ($session->state === 'ADD_WAIT_QTY') {


            $keyword = $session->data['keyword'];


            $result = $this->inventory->addStock(
                $keyword,
                (float)$text,
                'Telegram'
            );


            $this->session->clear(
                $chatId
            );


            if (!$result['success']) {

                return "❌ " . $result['message'];
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


        return "❓ Unknown session";
    }

    private function handleAddMode(
        string $text,
        $chatId
    ): string {

        preg_match(
            '/^(.*?)(?:\s*\+(\d+))?$/',
            $text,
            $matches
        );


        $keyword = trim($matches[1]);

        $quantity = isset($matches[2])
            ? (float)$matches[2]
            : 1;



        $result = $this->inventory->addStock(
            $keyword,
            $quantity,
            'Telegram'
        );


        $this->session->clear(
            $chatId
        );


        if (!$result['success']) {

            return "❌ " . $result['message'];
        }


        return
            "✅ Added\n\n"
            . $result['variant']['name']
            . "\n+"
            . $quantity
            . " "
            . $result['variant']['unit']
            . "\n\nCurrent stock: "
            . $result['current_stock']
            . " "
            . $result['variant']['unit'];
    }

    private function handleUseMode(
        string $text,
        $chatId
    ): string {

        preg_match(
            '/^(.*?)(?:\s*-(\d+))?$/',
            $text,
            $matches
        );


        $keyword = trim($matches[1]);


        $quantity = isset($matches[2])
            ? (float)$matches[2]
            : 1;



        $result = $this->inventory->useStock(
            $keyword,
            $quantity,
            'Telegram'
        );


        $this->session->clear(
            $chatId
        );


        if (!$result['success']) {

            return "❌ " . $result['message'];
        }


        return
            "➖ Used\n\n"
            . $result['variant']['name']
            . "\n-"
            . $quantity
            . " "
            . $result['variant']['unit']
            . "\n\nCurrent stock: "
            . $result['current_stock']
            . " "
            . $result['variant']['unit'];
    }
}
