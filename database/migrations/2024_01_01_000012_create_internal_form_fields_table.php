<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internal_form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('internal_forms')->cascadeOnDelete();
            $table->string('type');
            $table->string('label');
            $table->text('placeholder')->nullable();
            $table->boolean('required')->default(false);
            $table->json('options')->nullable();
            $table->integer('order')->default(0);
            $table->json('validation_rules')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internal_form_fields');
    }
};
