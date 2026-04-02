<?php

namespace TeamZac\LaravelShapefiles;

use Illuminate\Support\ServiceProvider;

class ShapefileServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ShapefileManager::class);
    }
}
