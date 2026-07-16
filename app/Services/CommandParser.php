<?php

namespace App\Services;

class CommandParser
{
    public function parse(string $text): array
    {
        $text = trim(mb_strtolower($text));

        /*
    |--------------------------------------------------------------------------
    | Telegram Keyboard Buttons
    |--------------------------------------------------------------------------
    */

        if ($text === '📦 stock list') {
            return [
                'action' => 'LIST',
                'keyword' => null,
                'quantity' => null,
            ];
        }

        if ($text === '➕ add stock') {
            return [
                'action' => 'ADD_MODE',
                'keyword' => null,
                'quantity' => null,
            ];
        }

        if ($text === '➖ use stock') {
            return [
                'action' => 'USE_MODE',
                'keyword' => null,
                'quantity' => null,
            ];
        }

        if ($text === '❓ help') {
            return [
                'action' => 'HELP',
                'keyword' => null,
                'quantity' => null,
            ];
        }

        /*
    |--------------------------------------------------------------------------
    | Remove leading "/"
    |--------------------------------------------------------------------------
    */

        $text = ltrim($text, '/');

        /*
    |--------------------------------------------------------------------------
    | Simple Commands
    |--------------------------------------------------------------------------
    */

        if (in_array($text, ['list', 'stock'])) {
            return [
                'action' => 'LIST',
                'keyword' => null,
                'quantity' => null,
            ];
        }

        /*
    |--------------------------------------------------------------------------
    | Natural Language
    |--------------------------------------------------------------------------
    |
    | milk +2
    | milk -2
    | milk =20
    |
    */

        if (preg_match('/^(.+?)\s*([+\-=])\s*(\d+(?:\.\d+)?)$/', $text, $matches)) {

            $keyword = trim($matches[1]);
            $operator = $matches[2];
            $quantity = (float) $matches[3];

            return [
                'action' => match ($operator) {
                    '+' => 'ADD',
                    '-' => 'USE',
                    '=' => 'ADJUST',
                },
                'keyword' => $keyword,
                'quantity' => $quantity,
            ];
        }

        /*
    |--------------------------------------------------------------------------
    | /add milk 2
    | /use milk 1
    | /adjust milk 20
    |--------------------------------------------------------------------------
    */

        $parts = explode(' ', $text);

        $command = array_shift($parts);

        if (in_array($command, ['add', 'a'])) {

            $quantity = (float) array_pop($parts);

            return [
                'action' => 'ADD',
                'keyword' => implode(' ', $parts),
                'quantity' => $quantity,
            ];
        }

        if (in_array($command, ['use', 'u'])) {

            $quantity = (float) array_pop($parts);

            return [
                'action' => 'USE',
                'keyword' => implode(' ', $parts),
                'quantity' => $quantity,
            ];
        }

        if (in_array($command, ['adjust', 'adj'])) {

            $quantity = (float) array_pop($parts);

            return [
                'action' => 'ADJUST',
                'keyword' => implode(' ', $parts),
                'quantity' => $quantity,
            ];
        }

        return [
            'action' => 'UNKNOWN',
            'keyword' => null,
            'quantity' => null,
        ];
    }
}
