<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pin_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pin_id')->constrained()->onDelete('cascade');
            $table->string('status');
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pin_updates');
    }
};
