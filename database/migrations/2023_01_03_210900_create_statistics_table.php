<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    public function up(): void
    {
        schema()->create('statistics', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->unsignedInteger('messages')->default(0);
            $table->date('date')->useCurrent();
        });
    }

    public function down(): void
    {
        schema()->drop('statistics');
    }
};