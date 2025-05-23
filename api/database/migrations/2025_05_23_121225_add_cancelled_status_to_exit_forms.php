<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('exit_forms', function (Blueprint $table) {
            // Modifier l'énumération pour inclure 'cancelled'
            DB::statement("ALTER TABLE exit_forms MODIFY COLUMN status ENUM('draft', 'pending', 'completed', 'cancelled') DEFAULT 'draft'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exit_forms', function (Blueprint $table) {
            // Restaurer l'énumération à sa valeur d'origine
            DB::statement("ALTER TABLE exit_forms MODIFY COLUMN status ENUM('draft', 'pending', 'completed') DEFAULT 'draft'");
        });
    }
};
