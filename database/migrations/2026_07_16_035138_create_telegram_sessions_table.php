<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {
        Schema::create('telegram_sessions', function (Blueprint $table) {

            $table->id();

            $table->string('telegram_id')
                ->unique();

            $table->string('state')
                ->nullable();

            $table->json('data')
                ->nullable();

            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('telegram_sessions');
    }
};
