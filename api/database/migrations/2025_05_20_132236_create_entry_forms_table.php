<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {        Schema::create('entry_forms', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->date('date');
            $table->foreignId('supplier_id')->constrained();
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'pending', 'completed'])->default('draft');
            $table->decimal('total', 10, 2);
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('entry_forms');
    }
};
