<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('externals', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')
                ->constrained('services')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('external_id')->comment('ID of the external service')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('externals');
    }
};
