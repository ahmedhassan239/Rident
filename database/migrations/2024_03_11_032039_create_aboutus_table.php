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
        Schema::create('aboutus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->text('mission')->nullable();
            $table->text('vision')->nullable();
            $table->text('wy_choose_us')->nullable();
            $table->text('title1')->nullable();
            $table->text('des1')->nullable();
            $table->text('title2')->nullable();
            $table->text('des2')->nullable();
            $table->text('about_us')->nullable();
            $table->integer('num1')->nullable();
            $table->integer('num2')->nullable();
            $table->integer('num3')->nullable();
            $table->integer('num4')->nullable(); 
            $table->string('seo_title')->nullable();
            $table->string('seo_keywords')->nullable();
            $table->string('seo_description')->nullable();
            $table->string('robots')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aboutus');
    }
};
