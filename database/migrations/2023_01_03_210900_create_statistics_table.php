<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Yumi\Models\User;

return new class extends Migration {
    public function up(): void
    {
        schema()->create('statistics', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'user_id')
                ->index()->constrained('users', 'id')->restrictOnDelete();
            $table->unsignedInteger('messages')->default(0);
            $table->date('date')->useCurrent();
        });
    }

    public function down(): void
    {
        schema()->drop('statistics');
    }
};