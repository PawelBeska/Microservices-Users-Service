<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('verification_tokens', static function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained('users');
            $table->string('token');
            $table->string('type')->index();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_tokens');
    }
};
