<?php

namespace TeamZac\LaravelShapefiles;

use Illuminate\Support\Traits\ForwardsCalls;

class Geometry
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
	public function transform()
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
	public function asGeoJson()
	{
		if (! $this->shouldTransform || is_null($this->sourceProjection) || is_null($this->destinationProjection)) {
			return $this->getGeoJSON();
		}

		$json = json_decode($this->getGeoJSON());
		
		for ($i = 0; $i < count($json->coordinates); $i++) {
			for ($j = 0; $j < count($json->coordinates[$i]); $j++) {
				$json->coordinates[$i][$j] = $this->sourceProjection->transformPoint($json->coordinates[$i][$j], $this->destinationProjection);
			}
		}
		return json_encode($json);
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
