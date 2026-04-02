<?php

namespace TeamZac\LaravelShapefiles\Fakes;

use TeamZac\LaravelShapefiles\Contracts\GeometryContract;

class FakeGeometry implements GeometryContract
{
    protected array $data;

    protected \stdClass $geoJson;

    protected ?array $boundingBox;

    public function __construct(array $data = [], array|object $geoJson = [], ?array $boundingBox = null)
    {
        $this->data = $data;
        $this->geoJson = is_array($geoJson) ? (object) $geoJson : (object) (array) $geoJson;
        $this->boundingBox = $boundingBox;
    }

    public static function make(array $data = [], array|object $geoJson = [], ?array $boundingBox = null): static
    {
        return new static($data, $geoJson, $boundingBox);
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

    public function getBoundingBox(): ?array
    {
        if ($this->boundingBox !== null) {
            return $this->boundingBox;
        }

        return $this->deriveBoundingBox();
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

    protected function deriveBoundingBox(): ?array
    {
        if (! isset($this->geoJson->coordinates)) {
            return null;
        }

        $points = $this->flattenCoordinates($this->geoJson->coordinates);

        if (empty($points)) {
            return null;
        }

        $xs = array_column($points, 0);
        $ys = array_column($points, 1);

        return [
            'xmin' => min($xs),
            'xmax' => max($xs),
            'ymin' => min($ys),
            'ymax' => max($ys),
        ];
    }

    protected function flattenCoordinates(array $coordinates): array
    {
        if (count($coordinates) === 2 && is_numeric($coordinates[0]) && is_numeric($coordinates[1])) {
            return [$coordinates];
        }

        $points = [];
        foreach ($coordinates as $item) {
            $points = array_merge($points, $this->flattenCoordinates($item));
        }
        return $points;
    }
}
