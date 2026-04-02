<?php

namespace TeamZac\LaravelShapefiles\Tests;

use Orchestra\Testbench\TestCase;
use TeamZac\LaravelShapefiles\Contracts\GeometryContract;
use TeamZac\LaravelShapefiles\Fakes\FakeGeometry;

class FakeGeometryTest extends TestCase
{
    /** @test */
    public function it_implements_geometry_contract()
    {
        $geometry = FakeGeometry::make();

        $this->assertInstanceOf(GeometryContract::class, $geometry);
    }

    /** @test */
    public function it_returns_data_by_key()
    {
        $geometry = FakeGeometry::make(['ID' => 1, 'NAME' => 'Test']);

        $this->assertEquals(1, $geometry->getData('ID'));
        $this->assertEquals('Test', $geometry->getData('NAME'));
    }

    /** @test */
    public function it_returns_all_data_when_no_key_given()
    {
        $data = ['ID' => 1, 'NAME' => 'Test'];
        $geometry = FakeGeometry::make($data);

        $this->assertEquals($data, $geometry->getData());
    }

    /** @test */
    public function it_supports_property_access()
    {
        $geometry = FakeGeometry::make(['ID' => 1, 'NAME' => 'Test']);

        $this->assertEquals(1, $geometry->ID);
        $this->assertEquals('Test', $geometry->NAME);
    }

    /** @test */
    public function it_returns_geojson_string()
    {
        $geoJson = ['type' => 'Point', 'coordinates' => [-97.0, 32.0]];
        $geometry = FakeGeometry::make([], $geoJson);

        $this->assertIsString($geometry->asGeoJson());
        $decoded = json_decode($geometry->asGeoJson());
        $this->assertEquals('Point', $decoded->type);
    }

    /** @test */
    public function it_returns_geojson_as_object()
    {
        $geoJson = ['type' => 'Point', 'coordinates' => [-97.0, 32.0]];
        $geometry = FakeGeometry::make([], $geoJson);

        $json = $geometry->asJson();
        $this->assertIsObject($json);
        $this->assertEquals('Point', $json->type);
    }

    /** @test */
    public function transform_is_a_noop()
    {
        $geometry = FakeGeometry::make();

        $this->assertSame($geometry, $geometry->transform());
    }
}
