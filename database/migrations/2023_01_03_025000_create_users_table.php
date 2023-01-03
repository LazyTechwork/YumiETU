<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    public function up(): void
    {
        schema()->create('users', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('telegram_id')->nullable()->comment(
                'Telegram ID'
            )->unique()->index();
            $table->unsignedBigInteger('vk_id')->unique()->nullable()->comment(
                'VK ID'
            );
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('custom_name', 16)->nullable()->comment(
                'Custom admin name for Telegram'
            );
            $table->unsignedTinyInteger('admin_level')->default(0);
            $table->date('birthday')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        schema()->drop('users');
    }
};