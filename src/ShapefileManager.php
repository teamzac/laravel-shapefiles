<?php

namespace TeamZac\LaravelShapefiles;

use TeamZac\LaravelShapefiles\Contracts\ReaderContract;
use TeamZac\LaravelShapefiles\Fakes\FakeReader;

class ShapefileManager
{
    protected ?FakeReader $fakeReader = null;

    protected bool $faking = false;

    /**
     * Enable fake mode. Optionally provide a pre-configured FakeReader.
     */
    public function fake(?FakeReader $reader = null): FakeReader
    {
        $this->faking = true;
        $this->fakeReader = $reader ?? FakeReader::make();

        return $this->fakeReader;
    }

    /**
     * Get a reader for the given file/directory.
     * Returns the fake reader if in fake mode.
     */
    public function reader(string $fileOrDirectory, array $options = [], $destinationProjection = null): ReaderContract
    {
        if ($this->faking) {
            return $this->fakeReader;
        }

        return new Reader($fileOrDirectory, $options, $destinationProjection);
    }

    /**
     * Get a ReaderFactory for fluent configuration.
     */
    public function factory(): ReaderFactory
    {
        return new ReaderFactory($this);
    }

    /**
     * Check if currently in fake mode.
     */
    public function isFaking(): bool
    {
        return $this->faking;
    }

    /**
     * Reset back to real mode.
     */
    public function reset(): void
    {
        $this->faking = false;
        $this->fakeReader = null;
    }
}
