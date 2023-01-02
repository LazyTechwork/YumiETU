<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    public function up(): void
    {
        schema()->create('marriages', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('husband_id')->index();
            $table->unsignedBigInteger('wife_id')->index();
            $table->dateTime('married_since')->useCurrent();
            $table->dateTime('divorced_since')->nullable();
        });
    }

    public function down(): void
    {
        schema()->drop('marriages');
    }
};