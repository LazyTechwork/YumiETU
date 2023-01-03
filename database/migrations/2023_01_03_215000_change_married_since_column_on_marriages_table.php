<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    public function up(): void
    {
        schema()->table('marriages', static function (Blueprint $table) {
            $table->dateTime('married_since')->nullable()->change();
        });
    }

    public function down(): void
    {
    }
};