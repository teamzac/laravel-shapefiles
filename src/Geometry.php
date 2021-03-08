<?php

namespace TeamZac\LaravelShapefiles;

use Illuminate\Support\Traits\ForwardsCalls;

class Geometry
{
	use ForwardsCalls;

	/** @var Shapefile\Geometry\Geometry */
	protected $geometry;

	public function __construct(\Shapefile\Geometry\Geometry $parent)
	{
		$this->geometry = $parent;
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
	public function asGeoJson()
	{
		return $this->getGeoJSON();
	}

	/** 
	 * Run the GeoJSON string through json_decode
	 *
	 * @return stdClass
	 */
	public function asJson()
	{
		return json_decode($this->getGeoJSON());
	}

	/**
	 * Convenience method to access individual items off the data
	 * array without having to mess with the array itself, or
	 * simply return the data array with a less wordy syntax
	 *
	 * @param string|null $key
	 * @return mixed
	 */
	public function getData($key = null)
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
