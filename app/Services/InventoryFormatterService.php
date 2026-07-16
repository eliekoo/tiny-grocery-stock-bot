<?php

namespace App\Services;

class InventoryFormatterService
{
    public function formatList($items): string
    {

        if ($items->isEmpty()) {

            return "📦 No inventory found.";
        }


        $lowStock = [];
        $available = [];


        foreach ($items as $item) {

            $stock = number_format(
                $item['quantity'],
                0
            );


            $line =
                "• "
                . $item['variant']
                . ": "
                . $stock
                . " "
                . $item['unit'];


            if (
                $item['quantity']
                <=
                $item['minimum_stock']
            ) {

                $lowStock[] = $line;
            } else {

                $available[] = $line;
            }
        }



        $message = "🛒 Grocery Stock\n";



        if (!empty($lowStock)) {

            $message .= "\n⚠️ LOW STOCK\n\n";

            foreach ($lowStock as $item) {

                $message .= $item . "\n";
            }
        }



        if (!empty($available)) {

            $message .= "\n✅ AVAILABLE\n\n";

            foreach ($available as $item) {

                $message .= $item . "\n";
            }
        }



        return trim($message);
    }
}
