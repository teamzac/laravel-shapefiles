<?php

namespace TeamZac\LaravelShapefiles\Tests;

use Orchestra\Testbench\TestCase;
use TeamZac\LaravelShapefiles\LaravelShapefilesServiceProvider;
use TeamZac\LaravelShapefiles\Reader;

class ProjectionTest extends TestCase
{
    /** @test */
    public function it_runs()
    {
        $reader = Reader::factory()
            ->forceAllCaps(false)
            ->transformTo('EPSG:4326')
            ->make('/Users/chad/Downloads/Magnolia_Buffer_Erase_NoOLap');

        $reader->each(function($geom) {
            dd($geom->transform()->asGeoJson());
        });
        dd($reader->getProjection());
    }
}
