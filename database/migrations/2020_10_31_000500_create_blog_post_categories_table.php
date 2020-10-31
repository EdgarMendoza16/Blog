<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogPostCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('blog_post_categories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('post_id')->unsigned();
            $table->bigInteger('category_id')->unsigned();
            $table
                ->foreign('post_id')
                ->references('id')
                ->on('blog_posts')
                ->onDelete('CASCADE');
            $table
                ->foreign('category_id')
                ->references('id')
                ->on('blog_categories')
                ->onDelete('CASCADE');
        });
    }

    public function down()
    {
        Schema::dropIfExists('blog_post_categories');
    }
}
