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
        Schema::create('model_has_files', function (Blueprint $table) {
        
            $table->unsignedBigInteger('file_id');
            $table->morphs('model'); // This will create model_id and model_type
            $table->foreign('file_id')->references('id')->on('files')->onDelete('cascade');
            $table->string('type');
   
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('model_has_files');
    }
};
