<?php

namespace TeamZac\LaravelShapefiles\Tests;

use Orchestra\Testbench\TestCase;
use TeamZac\LaravelShapefiles\Contracts\GeometryContract;
use TeamZac\LaravelShapefiles\Contracts\ReaderContract;
use TeamZac\LaravelShapefiles\Fakes\FakeGeometry;
use TeamZac\LaravelShapefiles\Fakes\FakeReader;

class FakeReaderTest extends TestCase
{
    /** @test */
    public function it_implements_reader_contract()
    {
        $reader = FakeReader::make();

        $this->assertInstanceOf(ReaderContract::class, $reader);
    }

    /** @test */
    public function it_returns_the_record_count()
    {
        $reader = FakeReader::make()->withRecords([
            FakeGeometry::make(['ID' => 1]),
            FakeGeometry::make(['ID' => 2]),
        ]);

        $this->assertEquals(2, $reader->count());
    }

    /** @test */
    public function it_stores_prj_and_field_names()
    {
        $reader = FakeReader::make()
            ->withPrj('EPSG:4326')
            ->withFieldNames(['ID', 'NAME']);

        $this->assertEquals('EPSG:4326', $reader->getPrj());
        $this->assertEquals(['ID', 'NAME'], $reader->getFieldNames());
    }

    /** @test */
    public function it_defaults_to_null_prj_and_empty_field_names()
    {
        $reader = FakeReader::make();

        $this->assertNull($reader->getPrj());
        $this->assertEquals([], $reader->getFieldNames());
    }

    /** @test */
    public function it_forwards_collection_methods()
    {
        $reader = FakeReader::make()->withRecords([
            FakeGeometry::make(['ID' => 1, 'NAME' => 'First']),
            FakeGeometry::make(['ID' => 2, 'NAME' => 'Second']),
        ]);

        $first = $reader->first();
        $this->assertInstanceOf(GeometryContract::class, $first);
        $this->assertEquals(1, $first->ID);
        $this->assertEquals('First', $first->NAME);
    }

    /** @test */
    public function it_supports_iteration()
    {
        $reader = FakeReader::make()->withRecords([
            FakeGeometry::make(['ID' => 1]),
            FakeGeometry::make(['ID' => 2]),
            FakeGeometry::make(['ID' => 3]),
        ]);

        $ids = $reader->map(fn ($g) => $g->ID)->all();
        $this->assertEquals([1, 2, 3], $ids);
    }
}
