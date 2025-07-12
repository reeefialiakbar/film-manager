<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('director')->nullable();
            $table->integer('year')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('file_path');
            $table->string('category')->default('iranian');
            $table->string('duration')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('movies');
    }
};
