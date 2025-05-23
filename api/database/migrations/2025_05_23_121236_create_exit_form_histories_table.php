<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exit_form_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exit_form_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained();
            $table->string('field_name');
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->text('change_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exit_form_histories');
    }
};
