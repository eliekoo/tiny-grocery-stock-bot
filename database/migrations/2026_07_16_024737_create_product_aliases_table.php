<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_aliases', function (Blueprint $table) {

            $table->id();

            $table->foreignId('variant_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('alias', 100);

            $table->timestamps();

            $table->unique([
                'variant_id',
                'alias'
            ]);

            $table->index('alias');
            $table->unsignedTinyInteger('priority')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_aliases');
    }
};
