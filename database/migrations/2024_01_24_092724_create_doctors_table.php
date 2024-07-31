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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->longText('name');
            $table->longText('slug');
            // $table->foreignId('country_id')->constrained()->onDelete('cascade');
            // $table->foreignId('governorate_id')->constrained()->onDelete('cascade');
            // $table->foreignId('area_id')->constrained()->onDelete('cascade');
            // $table->longText('specialtie_id');
            // $table->longText('sub_specialtie');
            // $table->longText('healthcare_provider_id');
            // $table->longText('insurance');
            // $table->longText('fees');
            // $table->longText('waiting_time');
            // $table->longText('address');
            $table->longText('description')->nullable();
            $table->longText('overview')->nullable();
            $table->longText('seo_title')->nullable();
            $table->longText('seo_keywords')->nullable();
            $table->longText('seo_description')->nullable();
            $table->longText('robots')->nullable();
            $table->boolean('status')->default(1);
            $table->boolean('featured')->default(0);
            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
