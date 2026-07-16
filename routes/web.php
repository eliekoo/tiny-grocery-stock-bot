<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


use App\Http\Controllers\TelegramController;

Route::get('/test', function () {
    app(\App\Services\InventoryService::class)
        ->addStock('lotus', 6);

    return 'OK';
});

use App\Services\ProductSearchService;;

Route::get('/test-search', function () {

    $service = app(ProductSearchService::class);

    $results = $service->search('lotus');

    return response()->json($results);
});

use App\Services\InventoryService;


Route::get('/test-add', function () {

    $service = app(InventoryService::class);


    return response()->json(
        $service->addStock(
            'lotus',
            6,
            'Test purchase'
        )
    );
});

Route::get('/test-use', function () {

    return response()->json(

        app(InventoryService::class)
            ->useStock(
                'lotus',
                2,
                'Used for breakfast'
            )

    );
});


Route::get('/test-list', function () {

    return response()->json(

        app(InventoryService::class)
            ->listInventory()

    );
});

Route::get('/test-adjust', function () {

    return response()->json(

        app(\App\Services\InventoryService::class)
            ->adjustStock(
                'lotus',
                14,
                'Physical count'
            )

    );
});

use App\Services\CommandParser;


Route::get('/test-parser', function () {

    $parser = app(CommandParser::class);


    return [

        $parser->parse(
            '/add lotus fresh milk 6'
        ),


        $parser->parse(
            '/a lotus 6'
        ),


        $parser->parse(
            'lotus fresh milk +6'
        ),


        $parser->parse(
            'drypers xl -1'
        ),

    ];
});
