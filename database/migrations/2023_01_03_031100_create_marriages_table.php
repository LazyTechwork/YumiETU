<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Yumi\Models\User;

return new class extends Migration {
    public function up(): void
    {
        schema()->create('marriages', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'husband_id')->index()
                ->constrained('users', 'id')->restrictOnDelete();
            $table->foreignIdFor(User::class, 'wife_id')->index()
                ->constrained('users', 'id')->restrictOnDelete();
            $table->dateTime('married_since')->useCurrent();
            $table->dateTime('divorced_since')->nullable();
        });
    }

    public function down(): void
    {
        schema()->drop('marriages');
    }
};