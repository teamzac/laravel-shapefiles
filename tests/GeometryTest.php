<?php

namespace TeamZac\LaravelShapefiles\Tests;

use Orchestra\Testbench\TestCase;
use TeamZac\LaravelShapefiles\Geometry;
use TeamZac\LaravelShapefiles\Projection;
use TeamZac\LaravelShapefiles\Reader;

class GeometryTest extends TestCase
{
    protected function getProjection(): Projection
    {
        $prj = file_get_contents(__DIR__ . '/shp/Test.prj');
        return new Projection($prj);
    }

    /** @test */
    public function transform_coordinates_handles_point()
    {
        $geometry = $this->makeGeometry('Point', [-97.0, 30.0]);

        $json = json_decode($geometry->transform()->asGeoJson());

        $this->assertEquals('Point', $json->type);
        $this->assertIsFloat($json->coordinates[0]);
        $this->assertIsFloat($json->coordinates[1]);
        // Transformed coordinates should differ from the originals
        $this->assertNotEquals(-97.0, $json->coordinates[0]);
    }

    /** @test */
    public function transform_coordinates_handles_linestring()
    {
        $geometry = $this->makeGeometry('LineString', [[-97.0, 30.0], [-96.0, 31.0]]);

        $json = json_decode($geometry->transform()->asGeoJson());

        $this->assertCount(2, $json->coordinates);
        $this->assertNotEquals(-97.0, $json->coordinates[0][0]);
        $this->assertNotEquals(-96.0, $json->coordinates[1][0]);
    }

    /** @test */
    public function transform_coordinates_handles_polygon()
    {
        $coords = [[[-97.0, 30.0], [-96.0, 30.0], [-96.0, 31.0], [-97.0, 31.0], [-97.0, 30.0]]];
        $geometry = $this->makeGeometry('Polygon', $coords);

        $json = json_decode($geometry->transform()->asGeoJson());

        $this->assertCount(1, $json->coordinates);
        $this->assertCount(5, $json->coordinates[0]);
        $this->assertNotEquals(-97.0, $json->coordinates[0][0][0]);
    }

    /** @test */
    public function transform_coordinates_handles_multipolygon()
    {
        $ring = [[-97.0, 30.0], [-96.0, 30.0], [-96.0, 31.0], [-97.0, 31.0], [-97.0, 30.0]];
        $coords = [[[$ring[0], $ring[1], $ring[2], $ring[3], $ring[4]]]];
        $geometry = $this->makeGeometry('MultiPolygon', $coords);

        $json = json_decode($geometry->transform()->asGeoJson());

        $this->assertCount(1, $json->coordinates);
        $this->assertNotEquals(-97.0, $json->coordinates[0][0][0][0]);
    }

    /** @test */
    public function as_json_returns_transformed_coordinates_when_transform_called()
    {
        $geometry = $this->makeGeometry('Point', [-97.0, 30.0]);

        $json = $geometry->transform()->asJson();

        $this->assertIsObject($json);
        $this->assertNotEquals(-97.0, $json->coordinates[0]);
    }

    /** @test */
    public function as_json_returns_raw_coordinates_without_transform()
    {
        $geometry = $this->makeGeometry('Point', [-97.0, 30.0]);

        $json = $geometry->asJson();

        $this->assertEquals(-97.0, $json->coordinates[0]);
        $this->assertEquals(30.0, $json->coordinates[1]);
    }

    /** @test */
    public function as_geo_json_returns_raw_when_no_transform()
    {
        $geometry = $this->makeGeometry('Point', [-97.0, 30.0]);

        $geoJson = $geometry->asGeoJson();
        $json = json_decode($geoJson);

        $this->assertEquals(-97.0, $json->coordinates[0]);
    }

    /** @test */
    public function it_transforms_coordinates_from_shapefile()
    {
        $dest = new Projection('EPSG:3857');

        $reader = new Reader(__DIR__ . '/shp/Test.shp', [], $dest);
        $item = $reader->first();

        $raw = $item->asJson();
        $transformed = $item->transform()->asJson();

        // The raw and transformed coordinates should differ
        $this->assertNotEquals(
            $raw->coordinates[0][0][0],
            $transformed->coordinates[0][0][0]
        );
    }

    protected function makeGeometry(string $type, array $coordinates): Geometry
    {
        $prj = file_get_contents(__DIR__ . '/shp/Test.prj');
        $source = new Projection($prj);
        $dest = new Projection('EPSG:3857');

        $geoJson = json_encode(['type' => $type, 'coordinates' => $coordinates]);

        $mock = $this->createMock(\Shapefile\Geometry\Geometry::class);
        $mock->method('getGeoJSON')->willReturn($geoJson);
        $mock->method('getDataArray')->willReturn([]);

        return new Geometry($mock, $source, $dest);
    }
}
