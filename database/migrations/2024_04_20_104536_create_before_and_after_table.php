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
        Schema::create('before_and_after', function (Blueprint $table) {
            $table->id();
            $table->longText('description')->nullable();
            $table->integer('service_id')->nullable();
            $table->boolean('status')->default(1); // Assuming 1 for active, 0 for inactive
            $table->boolean('featured')->default(0); // Assuming 0 for not featured, 1 for featured
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('before_and_after');
    }
};
