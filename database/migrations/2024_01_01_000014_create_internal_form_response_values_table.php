<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internal_form_response_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('response_id')->constrained('internal_form_responses')->cascadeOnDelete();
            $table->foreignId('field_id')->constrained('internal_form_fields')->cascadeOnDelete();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internal_form_response_values');
    }
};
