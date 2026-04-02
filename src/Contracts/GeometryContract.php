<?php

namespace TeamZac\LaravelShapefiles\Contracts;

interface GeometryContract
{
    /**
     * Mark the geometry for coordinate transformation
     */
    public function transform(): static;

    /**
     * Get the GeoJSON string representation
     */
    public function asGeoJson(): string;

    /**
     * Get the GeoJSON as a decoded object
     */
    public function asJson(): \stdClass;

    /**
     * Get the bounding box for this geometry
     */
    public function getBoundingBox(): ?array;

    /**
     * Get data value(s) from the geometry's attribute table
     */
    public function getData(?string $key = null): mixed;
}
