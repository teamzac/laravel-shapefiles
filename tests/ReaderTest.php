<?php

namespace TeamZac\LaravelShapefiles\Tests;

use Orchestra\Testbench\TestCase;
use TeamZac\LaravelShapefiles\LaravelShapefilesServiceProvider;
use TeamZac\LaravelShapefiles\Reader;

class ReaderTest extends TestCase
{
    /** @test */
    public function it_can_get_the_file_count()
    {
    	$reader = new Reader(__DIR__ . '/shp/Test.shp');

    	$this->assertEquals(2, $reader->count());
    }

    /** @test */
    public function it_can_open_using_a_directory_instead_of_a_shp_file()
    {
    	$reader = new Reader(__DIR__ . '/shp');

    	$this->assertEquals(2, $reader->count());
    }

    /** @test */
    public function it_can_get_the_first_item()
    {
    	$reader = new Reader(__DIR__ . '/shp');

    	$itemOne = $reader->first();

    	$this->assertNotNull($itemOne->getData());
    	$this->assertEquals(1, $itemOne->ID);
    	$this->assertEquals('Item 1', $itemOne->NAME);
    	$this->assertTrue(is_string($itemOne->asGeoJson()));
    	$this->assertTrue(is_object($itemOne->asJson()));
    	$this->assertTrue($itemOne->getRaw() instanceof \Shapefile\Geometry\Geometry);
    }
}
