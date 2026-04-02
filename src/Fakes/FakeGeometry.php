<?php

namespace TeamZac\LaravelShapefiles\Fakes;

use TeamZac\LaravelShapefiles\Contracts\GeometryContract;

class FakeGeometry implements GeometryContract
{
    protected array $data;

    protected \stdClass $geoJson;

    public function __construct(array $data = [], array|object $geoJson = [])
    {
        $this->data = $data;
        $this->geoJson = is_array($geoJson) ? (object) $geoJson : (object) (array) $geoJson;
    }

    public static function make(array $data = [], array|object $geoJson = []): static
    {
        return new static($data, $geoJson);
    }

    public function transform(): static
    {
        return $this;
    }

    public function asGeoJson(): string
    {
        return json_encode($this->geoJson);
    }

    public function asJson(): \stdClass
    {
        return clone $this->geoJson;
    }

    public function getData(?string $key = null): mixed
    {
        if (is_null($key)) {
            return $this->data;
        }

        return $this->data[$key] ?? null;
    }

    public function __get($key)
    {
        return $this->getData($key);
    }
}
