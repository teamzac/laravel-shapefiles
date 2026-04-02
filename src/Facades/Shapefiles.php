<?php

namespace TeamZac\LaravelShapefiles\Facades;

use Illuminate\Support\Facades\Facade;
use TeamZac\LaravelShapefiles\ShapefileManager;

/**
 * @method static \TeamZac\LaravelShapefiles\Fakes\FakeReader fake(?\TeamZac\LaravelShapefiles\Fakes\FakeReader $reader = null)
 * @method static \TeamZac\LaravelShapefiles\Contracts\ReaderContract reader(string $fileOrDirectory, array $options = [], $destinationProjection = null)
 * @method static \TeamZac\LaravelShapefiles\ReaderFactory factory()
 * @method static bool isFaking()
 * @method static void reset()
 *
 * @see \TeamZac\LaravelShapefiles\ShapefileManager
 */
class Shapefiles extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ShapefileManager::class;
    }
}
