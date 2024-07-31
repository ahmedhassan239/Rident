<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->longText('name');
            $table->longText('slug');
            $table->longText('overview')->nullable();
            $table->longText('description')->nullable();
            $table->longText('related_blogs')->nullable(); // Storing IDs or slugs as JSON
            $table->longText('seo_title')->nullable();
            $table->longText('seo_keywords')->nullable();
            $table->longText('seo_description')->nullable();
            $table->longText('og_title')->nullable();
            $table->longText('robots')->nullable();
            $table->boolean('status')->default(1); // Assuming 1 for active, 0 for inactive
            $table->boolean('featured')->default(0); // Assuming 0 for not featured, 1 for featured
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blogs');
    }
};
