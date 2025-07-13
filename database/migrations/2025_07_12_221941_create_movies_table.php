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
            $table->string('title');          // عنوان اصلی
            $table->string('persian_title')->nullable(); // عنوان فارسی
            $table->string('director')->nullable();
            $table->date('release_date')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('file_path');
            $table->string('category')->default('iranian');
            $table->string('duration')->nullable();
            $table->json('genres')->nullable();
            $table->decimal('imdb_rating', 3, 1)->nullable();
            $table->integer('your_rating')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('movies');
    }
};
