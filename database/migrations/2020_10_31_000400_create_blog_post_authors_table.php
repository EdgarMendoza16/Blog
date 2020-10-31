<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogPostAuthorsTable extends Migration
{
    public function up()
    {
        Schema::create('blog_post_authors', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('post_id')->unsigned();
            $table->bigInteger('author_id')->unsigned();
            $table
                ->foreign('post_id')
                ->references('id')
                ->on('blog_posts')
                ->onDelete('CASCADE');
            $table
                ->foreign('author_id')
                ->references('id')
                ->on('blog_authors')
                ->onDelete('CASCADE');
        });
    }

    public function down()
    {
        Schema::dropIfExists('blog_post_authors');
    }
}
