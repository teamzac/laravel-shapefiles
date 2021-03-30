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

	public static function make($fileOrDirectory)
	{
		return new static($fileOrDirectory);
	}

	/**
	 * Create a new shapefile reader 
	 *
	 * @param string $fileOrDirectory
	 */
	public function __construct($fileOrDirectory)
	{
		if (is_dir($fileOrDirectory)) {
			$fileOrDirectory = $this->convertDirectoryToShapefile($fileOrDirectory);
		}

		$this->reader = new ShapefileReader($fileOrDirectory);
		$this->collection = LazyCollection::make(function() use ($fileOrDirectory) {
			while ($geometry = $this->reader->fetchRecord()) {
				yield new Geometry($geometry);
			}
		});
	}

	public function getOriginal() 
	{
		return $this->reader;
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
	 * Pass all uncaught method calls to the underlying collection
	 */
	public function __call($method, $parameters)
	{
        return $this->forwardCallTo($this->collection, $method, $parameters);
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
        $files = scandir($directory);
        foreach ($files as $file) {
        	if (substr($file, -4) === '.shp') {
                return sprintf('%s/%s', rtrim($directory, '/'), $file);
            }
        }
        throw new \Exception('Unable to find a shapefile in this directory');
	}
}
