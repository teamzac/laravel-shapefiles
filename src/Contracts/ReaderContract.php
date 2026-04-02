<?php

namespace TeamZac\LaravelShapefiles\Contracts;

interface ReaderContract
{
    /**
     * Return the total number of features
     */
    public function count(): int;

    /**
     * Get the PRJ string for this shapefile's projection
     */
    public function getPrj(): ?string;

    /**
     * Get the field names from the DBF file
     */
    public function getFieldNames(): array;
}
