<?php

namespace TeamZac\LaravelShapefiles;

use Illuminate\Support\Traits\ForwardsCalls;
use TeamZac\LaravelShapefiles\Contracts\GeometryContract;

class Geometry implements GeometryContract
{
	use ForwardsCalls;

	/** @var Shapefile\Geometry\Geometry */
	protected $geometry;

	/** @var Projection */
	protected $sourceProjection;

	/** @var Projection */
	protected $destinationProjection;

	/** @var Bool */
	protected $shouldTransform = false;

	public function __construct(\Shapefile\Geometry\Geometry $parent, $sourceProjection = null, $destinationProjection = null)
	{
		$this->geometry = $parent;
		$this->sourceProjection = $sourceProjection;
		$this->destinationProjection = $destinationProjection;
	}

	/** 
	 * Transform the coordinates before returning the geometry
	 */
	public function transform(): static
	{
		$this->shouldTransform = true;
		return $this;
	}

	/**
	 * Get the underlying Shapefile\Geometry\Geometry object
	 *
	 * @return Shapefile\Geometry\Geometry
	 */
	public function getRaw()
	{
		return $this->geometry;
	}

	/**
	 * Convenience method to get the underlying GeoJSON string
	 *
	 * @return string
	 */
	public function asGeoJson(): string
	{
		if (! $this->shouldTransform || is_null($this->sourceProjection) || is_null($this->destinationProjection)) {
			return $this->getGeoJSON();
		}

		$json = json_decode($this->getGeoJSON());
		$json->coordinates = $this->transformCoordinates($json->coordinates);
		return json_encode($json);
	}

	/**
	 * Run the GeoJSON string through json_decode
	 *
	 * @return stdClass
	 */
	public function asJson(): \stdClass
	{
		return json_decode($this->asGeoJson());
	}

	/**
	 * Recursively walk coordinates and transform each [x, y] pair
	 */
	protected function transformCoordinates(array $coordinates): array
	{
		if (count($coordinates) === 2 && is_numeric($coordinates[0]) && is_numeric($coordinates[1])) {
			return $this->sourceProjection->transformPoint($coordinates, $this->destinationProjection);
		}

		return array_map(fn($item) => $this->transformCoordinates($item), $coordinates);
	}

	/**
	 * Convenience method to access individual items off the data
	 * array without having to mess with the array itself, or
	 * simply return the data array with a less wordy syntax
	 *
	 * @param string|null $key
	 * @return mixed
	 */
	public function getData(?string $key = null): mixed
	{
		$data = $this->getDataArray();

		if (is_null($key)) {
			return $data;
		}
		return $data[$key];
	}

	/**
	 * Pass method calls through to the underlying Shapefile\Geometry\Geometry object
	 */
	public function __call($method, $parameters)
	{
		return $this->forwardCallTo($this->geometry, $method, $parameters);
	}

	/**
	 * Allow property-style access to the data array
	 */
	public function __get($key)
	{
		return $this->getData($key);
	}
}
