<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    public function up(): void
    {
        schema()->table('users', static function (Blueprint $table) {
            $table->string('custom_name', 24)->change();
        });
    }

    public function down(): void
    {
        schema()->table('users', static function (Blueprint $table) {
            $table->string('custom_name', 16)->change();
        });
    }
};