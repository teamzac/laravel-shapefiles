<?php

namespace TeamZac\LaravelShapefiles\Tests;

use Orchestra\Testbench\TestCase;
use TeamZac\LaravelShapefiles\Contracts\ReaderContract;
use TeamZac\LaravelShapefiles\Facades\Shapefiles;
use TeamZac\LaravelShapefiles\Fakes\FakeGeometry;
use TeamZac\LaravelShapefiles\Fakes\FakeReader;
use TeamZac\LaravelShapefiles\ShapefileManager;
use TeamZac\LaravelShapefiles\ShapefileServiceProvider;

class ShapefileManagerTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ShapefileServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return ['Shapefiles' => Shapefiles::class];
    }

    /** @test */
    public function it_resolves_manager_from_container()
    {
        $manager = app(ShapefileManager::class);

        $this->assertInstanceOf(ShapefileManager::class, $manager);
    }

    /** @test */
    public function it_is_a_singleton()
    {
        $a = app(ShapefileManager::class);
        $b = app(ShapefileManager::class);

        $this->assertSame($a, $b);
    }

    /** @test */
    public function it_returns_real_reader_by_default()
    {
        $manager = new ShapefileManager;
        $reader = $manager->reader(__DIR__ . '/shp/Test.shp');

        $this->assertInstanceOf(ReaderContract::class, $reader);
        $this->assertEquals(2, $reader->count());
    }

    /** @test */
    public function it_returns_fake_reader_when_faking()
    {
        $manager = new ShapefileManager;

        $fakeReader = $manager->fake(
            FakeReader::make()->withRecords([
                FakeGeometry::make(['ID' => 1]),
            ])
        );

        $this->assertTrue($manager->isFaking());

        $reader = $manager->reader('/any/path.shp');
        $this->assertInstanceOf(FakeReader::class, $reader);
        $this->assertEquals(1, $reader->count());
    }

    /** @test */
    public function fake_without_argument_returns_empty_fake_reader()
    {
        $manager = new ShapefileManager;
        $fakeReader = $manager->fake();

        $this->assertInstanceOf(FakeReader::class, $fakeReader);
        $this->assertEquals(0, $fakeReader->count());
    }

    /** @test */
    public function reset_returns_to_real_mode()
    {
        $manager = new ShapefileManager;
        $manager->fake();
        $this->assertTrue($manager->isFaking());

        $manager->reset();
        $this->assertFalse($manager->isFaking());
    }

    /** @test */
    public function facade_fake_works()
    {
        Shapefiles::fake(
            FakeReader::make()->withRecords([
                FakeGeometry::make(['ID' => 99, 'NAME' => 'Fake']),
            ])
        );

        $reader = Shapefiles::reader('/does/not/exist.shp');
        $this->assertEquals(1, $reader->count());
        $this->assertEquals(99, $reader->first()->ID);

        Shapefiles::reset();
    }

    /** @test */
    public function factory_via_facade_respects_fake_mode()
    {
        Shapefiles::fake(
            FakeReader::make()->withRecords([
                FakeGeometry::make(['ID' => 1]),
            ])
        );

        $reader = Shapefiles::factory()->make('/any/path.shp');
        $this->assertInstanceOf(FakeReader::class, $reader);

        Shapefiles::reset();
    }
}
