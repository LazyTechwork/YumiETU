<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    public function up(): void
    {
        schema()->table('users', static function (Blueprint $table) {
            $table->string('first_name')->nullable()->collation(
                'utf16_unicode_ci'
            )->charset('utf16')->change();
            $table->string('last_name')->nullable()->collation(
                'utf16_unicode_ci'
            )->charset('utf16')->change();
            $table->string('custom_name', 16)->nullable()->comment(
                'Custom admin name for Telegram'
            )->collation('utf16_unicode_ci')->charset('utf16')->change();
        });
    }

    public function down(): void
    {
    }
};