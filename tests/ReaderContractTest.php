<?php

namespace TeamZac\LaravelShapefiles\Tests;

use Orchestra\Testbench\TestCase;
use TeamZac\LaravelShapefiles\Contracts\GeometryContract;
use TeamZac\LaravelShapefiles\Contracts\ReaderContract;
use TeamZac\LaravelShapefiles\Fakes\FakeGeometry;
use TeamZac\LaravelShapefiles\Fakes\FakeReader;
use TeamZac\LaravelShapefiles\Reader;

class ReaderContractTest extends TestCase
{
    public static function readerProvider(): array
    {
        return [
            'real' => [
                fn () => new Reader(__DIR__ . '/shp/Test.shp'),
            ],
            'fake' => [
                fn () => FakeReader::make()
                    ->withPrj('GEOGCS["GCS_WGS_1984"]')
                    ->withFieldNames(['ID', 'NAME'])
                    ->withRecords([
                        FakeGeometry::make(
                            ['ID' => 1, 'NAME' => 'Item 1'],
                            ['type' => 'Polygon', 'coordinates' => [[[-97, 32], [-97, 33], [-96, 33], [-96, 32], [-97, 32]]]]
                        ),
                        FakeGeometry::make(
                            ['ID' => 2, 'NAME' => 'Item 2'],
                            ['type' => 'Polygon', 'coordinates' => [[[-98, 32], [-98, 33], [-97, 33], [-97, 32], [-98, 32]]]]
                        ),
                    ]),
            ],
        ];
    }

    /**
     * @test
     * @dataProvider readerProvider
     */
    public function it_implements_reader_contract(\Closure $factory)
    {
        $reader = $factory();
        $this->assertInstanceOf(ReaderContract::class, $reader);
    }

    /**
     * @test
     * @dataProvider readerProvider
     */
    public function it_returns_count(\Closure $factory)
    {
        $reader = $factory();
        $this->assertEquals(2, $reader->count());
    }

    /**
     * @test
     * @dataProvider readerProvider
     */
    public function it_returns_prj_as_nullable_string(\Closure $factory)
    {
        $reader = $factory();
        $prj = $reader->getPrj();
        $this->assertTrue(is_string($prj) || is_null($prj));
    }

    /**
     * @test
     * @dataProvider readerProvider
     */
    public function it_returns_field_names_as_array(\Closure $factory)
    {
        $reader = $factory();
        $fields = $reader->getFieldNames();
        $this->assertIsArray($fields);
        $this->assertContains('ID', $fields);
        $this->assertContains('NAME', $fields);
    }

    /**
     * @test
     * @dataProvider readerProvider
     */
    public function first_returns_a_geometry_contract(\Closure $factory)
    {
        $reader = $factory();
        $first = $reader->first();
        $this->assertInstanceOf(GeometryContract::class, $first);
    }

    /**
     * @test
     * @dataProvider readerProvider
     */
    public function geometry_returns_data_by_key(\Closure $factory)
    {
        $reader = $factory();
        $first = $reader->first();

        $this->assertEquals(1, $first->getData('ID'));
        $this->assertEquals('Item 1', $first->getData('NAME'));
    }

    /**
     * @test
     * @dataProvider readerProvider
     */
    public function geometry_returns_all_data(\Closure $factory)
    {
        $reader = $factory();
        $first = $reader->first();
        $data = $first->getData();

        $this->assertIsArray($data);
        $this->assertArrayHasKey('ID', $data);
        $this->assertArrayHasKey('NAME', $data);
    }

    /**
     * @test
     * @dataProvider readerProvider
     */
    public function geometry_supports_property_access(\Closure $factory)
    {
        $reader = $factory();
        $first = $reader->first();

        $this->assertEquals(1, $first->ID);
        $this->assertEquals('Item 1', $first->NAME);
    }

    /**
     * @test
     * @dataProvider readerProvider
     */
    public function geometry_returns_geojson_string(\Closure $factory)
    {
        $reader = $factory();
        $first = $reader->first();

        $geoJson = $first->asGeoJson();
        $this->assertIsString($geoJson);

        $decoded = json_decode($geoJson);
        $this->assertNotNull($decoded);
        $this->assertObjectHasProperty('type', $decoded);
    }

    /**
     * @test
     * @dataProvider readerProvider
     */
    public function geometry_returns_geojson_as_object(\Closure $factory)
    {
        $reader = $factory();
        $first = $reader->first();

        $json = $first->asJson();
        $this->assertIsObject($json);
        $this->assertObjectHasProperty('type', $json);
    }

    /**
     * @test
     * @dataProvider readerProvider
     */
    public function transform_returns_self(\Closure $factory)
    {
        $reader = $factory();
        $first = $reader->first();

        $this->assertSame($first, $first->transform());
    }

    /**
     * @test
     * @dataProvider readerProvider
     */
    public function it_supports_collection_iteration(\Closure $factory)
    {
        $reader = $factory();
        $ids = $reader->map(fn ($g) => $g->ID)->all();

        $this->assertEquals([1, 2], $ids);
    }
}
