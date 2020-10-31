<?php

namespace EdgarMendozaTech\Blog;

use Illuminate\Support\ServiceProvider;

class BlogServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerRouteMacro();
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    protected function registerRouteMacro()
    {
        $router = $this->app['router'];
        $router->macro('blog', function ($baseUrl = '') use ($router) {
            $router->group(
                [
                    'prefix' => 'blog',
                    'namespace' => '\EdgarMendozaTech\Blog\Http\Controllers',
                ],
                function () use ($router) {
                    $router->group(
                        ['prefix' => 'publicaciones'],
                        function () use ($router) {
                            $router->get('/', 'PostController@index');
                            $router->get(
                                '/datos-secundarios',
                                'PostController@secondaryData'
                            );
                            $router->post('/crear', 'PostController@store');
                            $router->get(
                                '/editar/{post}',
                                'PostController@edit'
                            );
                            $router->put(
                                '/editar/{post}',
                                'PostController@update'
                            );
                            $router->delete(
                                '/eliminar/{post}',
                                'PostController@destroy'
                            );
                        }
                    );

                    $router->group(['prefix' => 'etiquetas'], function () use (
                        $router
                    ) {
                        $router->get('/', 'TagController@index');
                        $router->post('/crear', 'TagController@store');
                        $router->get('/editar/{tag}', 'TagController@edit');
                        $router->put('/editar/{tag}', 'TagController@update');
                        $router->delete(
                            '/eliminar/{tag}',
                            'TagController@destroy'
                        );
                    });

                    $router->group(['prefix' => 'categorias'], function () use (
                        $router
                    ) {
                        $router->get('/', 'CategoryController@index');
                        $router->get('/lista', 'CategoryController@list');
                        $router->post('/crear', 'CategoryController@store');
                        $router->get(
                            '/editar/{category}',
                            'CategoryController@edit'
                        );
                        $router->put(
                            '/editar/{category}',
                            'CategoryController@update'
                        );
                        $router->delete(
                            '/eliminar/{category}',
                            'CategoryController@destroy'
                        );
                    });

                    $router->group(['prefix' => 'autores'], function () use (
                        $router
                    ) {
                        $router->get('/', 'AuthorController@index');
                        $router->post('/crear', 'AuthorController@store');
                        $router->get(
                            '/editar/{author}',
                            'AuthorController@edit'
                        );
                        $router->put(
                            '/editar/{author}',
                            'AuthorController@update'
                        );
                        $router->delete(
                            '/eliminar/{author}',
                            'AuthorController@destroy'
                        );
                    });
                }
            );
        });
    }
}
