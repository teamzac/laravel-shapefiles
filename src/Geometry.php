<?php

namespace TeamZac\LaravelShapefiles;

use Illuminate\Support\Traits\ForwardsCalls;

class Geometry
{
	use ForwardsCalls;

	/** @var Shapefile\Geometry\Geometry */
	protected $geometry;

	public function __construct($parent)
	{
		$this->geometry = $parent;
	}

	public function getRaw()
	{
		return $this->geometry;
	}

	public function asGeoJson()
	{
		return $this->getGeoJSON();
	}

	public function asJson()
	{
		return json_decode($this->getGeoJSON());
	}

	public function getData($key = null)
	{
		$data = $this->getDataArray();

		if (is_null($key)) {
			return $data;
		}
		return $data[$key];
	}

	public function __call($method, $parameters)
	{
		return $this->forwardCallTo($this->geometry, $method, $parameters);
	}

	public function __get($key)
	{
		return $this->getData($key);
	}
}
