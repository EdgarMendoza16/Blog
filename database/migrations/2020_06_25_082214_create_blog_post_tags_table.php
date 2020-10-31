<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogPostTagsTable extends Migration
{
    public function up()
    {
        Schema::create('blog_post_tags', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('post_id')->unsigned();
            $table->bigInteger('tag_id')->unsigned();
            $table
                ->foreign('post_id')
                ->references('id')
                ->on('blog_posts')
                ->onDelete('CASCADE');
            $table
                ->foreign('tag_id')
                ->references('id')
                ->on('blog_tags')
                ->onDelete('CASCADE');
        });
    }

    public function down()
    {
        Schema::dropIfExists('blog_post_tags');
    }
}
