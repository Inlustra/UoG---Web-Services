<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePosts extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('location',8);
            $table->string('title', 45);
            $table->decimal('lat',18,12);
            $table->decimal('lon',18,12);
            $table->string('content');
            $table->boolean('pinned')->default(false);
            $table->boolean('removed')->default(false);
            $table->boolean('locked')->default(false);
            $table->timestamps();
        });
        Schema::create('post_sitter_types', function (Blueprint $table) {
            $table->integer('post_id')->unsigned();
            $table->foreign('post_id')->references('id')->on('posts');
            $table->integer('sitter_type_id')->unsigned();
            $table->foreign('sitter_type_id')->references('id')->on('sitter_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('post_sitter_types');
        Schema::drop('posts');
    }

}
