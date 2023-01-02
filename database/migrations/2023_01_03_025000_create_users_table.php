<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('telegram_id')->nullable()->comment(
                'Telegram ID'
            );
            $table->unsignedBigInteger('vk_id')->nullable()->comment('VK ID');
            $table->string('custom_name', 16)->nullable()->comment(
                'Custom admin name for Telegram'
            );
        });
    }

    public function down(): void
    {
        Schema::drop('users');
    }
};