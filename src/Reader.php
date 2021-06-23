<?php

namespace TeamZac\LaravelShapefiles;

use Illuminate\Support\LazyCollection;
use Illuminate\Support\Traits\ForwardsCalls;
use Shapefile\ShapefileReader;

class Reader
{
	use ForwardsCalls;

	/** @var Shapefile\ShapefileReader */
	protected $reader;

	/** @var LazyCollection */
	protected $collection;

	/** @var Projection */
	protected $sourceProjection;

	/** @var Projection */
	protected $destinationProjection;

	public static function make($fileOrDirectory)
	{
		return new static($fileOrDirectory);
	}

	/**
	 * Create a new shapefile reader 
	 *
	 * @param string $fileOrDirectory
	 */
	public function __construct($fileOrDirectory, $options = [], $destinationProjection = null)
	{
		if (is_dir($fileOrDirectory)) {
			$fileOrDirectory = $this->convertDirectoryToShapefile($fileOrDirectory);
		}

		$this->reader = new ShapefileReader($fileOrDirectory, $options);

		if (! is_null($destinationProjection)) {
			$this->sourceProjection = new Projection($this->reader->getPRJ());
			$this->destinationProjection = $destinationProjection instanceof Projection ? 
				$destinationProjection : new Projection($destinationProjection);
		}

		$this->collection = LazyCollection::make(function() use ($fileOrDirectory) {
			while ($geometry = $this->reader->fetchRecord()) {
				yield new Geometry($geometry, $this->sourceProjection, $this->destinationProjection);
			}
		});
	}

	public function getOriginal() 
	{
		return $this->reader;
	}

	public function getSourceProjection()
	{
		return $this->sourceProjection;
	}

	/**
	 * Return the total number of features in the shapefile
	 *
	 * @return int
	 */
	public function count() 
	{
		return $this->reader->getTotRecords();
	}

	/**
	 * Take a directory and return the .shp file inside of it
	 * 
	 * @param string $directory 
	 * @return string 				returns the full path of the .shp file
	 * @throws Exception 			when no .shp file is found
	 */
	protected function convertDirectoryToShapefile($directory)
	{
		$dotShpFiles = glob($directory.'/**/*.shp');
		if (count($dotShpFiles) === 0) {
			throw new \Exception('Unable to find a shapefile in this directory');
		}
		return $dotShpFiles[0];
	}

	/** 
	 * Pass all uncaught method calls to the underlying collection
	 */
	public function __call($method, $parameters)
	{
	    return $this->forwardCallTo($this->collection, $method, $parameters);
	}

	public static function factory()
	{
		return new ReaderFactory;
	}
}
