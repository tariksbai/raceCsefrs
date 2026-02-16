<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_access_logs', function (Blueprint $table) {
            $table->id();
            $table->string('form_type');
            $table->unsignedBigInteger('form_id');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->ipAddress('ip_address')->nullable();
            $table->string('action')->default('view');
            $table->timestamps();

            $table->index(['form_type', 'form_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_access_logs');
    }
};
