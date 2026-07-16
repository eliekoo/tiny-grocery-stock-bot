<?php

namespace App\Services;

class CommandParser
{
    public function parse(string $text): array
    {
        $text = trim(
            mb_strtolower($text)
        );


        // remove telegram command /
        $text = ltrim($text, '/');


        // detect action by command

        $action = null;


        if (
            str_starts_with($text, 'add ')
            || str_starts_with($text, 'a ')
        ) {

            $action = 'ADD';
        } elseif (
            str_starts_with($text, 'use ')
            || str_starts_with($text, 'u ')
        ) {

            $action = 'USE';
        }


        // Natural format:
        // lotus milk +6
        // drypers -1

        preg_match(
            '/(.+)\s([+-]\d+)$/',
            $text,
            $matches
        );


        if (!$action && $matches) {

            $keyword = trim($matches[1]);

            $quantity = (int) $matches[2];


            return [
                'action' => $quantity > 0
                    ? 'ADD'
                    : 'USE',

                'keyword' => $keyword,

                'quantity' => abs($quantity),
            ];
        }


        if ($action) {

            $parts = explode(
                ' ',
                $text
            );


            // remove command
            array_shift($parts);


            $quantity = array_pop($parts);


            return [

                'action' => $action,

                'keyword' => implode(
                    ' ',
                    $parts
                ),

                'quantity' => abs(
                    (int) $quantity
                ),

            ];
        }


        return [
            'action' => 'UNKNOWN',
            'keyword' => null,
            'quantity' => null,
        ];
    }
}
