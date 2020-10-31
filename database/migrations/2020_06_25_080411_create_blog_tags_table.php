<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogTagsTable extends Migration
{
    public function up()
    {
        Schema::create('blog_tags', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('meta_id')->unsigned();
            $table
                ->bigInteger('media_resource_id')
                ->unsigned()
                ->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('status')->default('draft'); // draft, published
            $table->timestamp('published_at')->nullable();
            $table->string('template');
            $table->integer('views_count')->default(0);
            $table->timestamps();
            $table
                ->foreign('meta_id')
                ->references('id')
                ->on('metas');
            $table
                ->foreign('media_resource_id')
                ->references('id')
                ->on('media_resources');
        });
    }

    public function down()
    {
        Schema::dropIfExists('blog_tags');
    }
}
