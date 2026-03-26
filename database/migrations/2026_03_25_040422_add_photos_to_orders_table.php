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
        Schema::table('orders', function (Blueprint $table) {
            // Campos opcionales (nullable) para guardar las rutas de las imágenes
            $table->string('loaded_unit_photo')->nullable()->after('status');
            $table->string('delivered_material_photo')->nullable()->after('loaded_unit_photo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['loaded_unit_photo', 'delivered_material_photo']);
        });
    }
};