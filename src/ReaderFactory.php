<?php

namespace TeamZac\LaravelShapefiles;

use Shapefile\Shapefile;

class ReaderFactory
{
    /** @var array */
    protected $options = [];

    /** @var Projection */
    protected $destination;

    /**
     * Allows a maximum field size of 255 bytes instead of 254 bytes in the .dbf file
     */
    public function allowLargerDbfFieldSize($flag = true)
    {
        $this->options[Shapefile::OPTION_DBF_ALLOW_FIELD_SIZE_255] = $flag;
        return $this;
    }

    /**
     * Converts from input charset to UTF-8 all strings read from the .dbf file
     */
    public function convertToUtf8($flag = true)
    {
        $this->options[Shapefile::OPTION_DBF_CONVERT_TO_UTF8] = $flag;
        return $this;
    }

    /**
     * Forces all column names in upper case in the .dbf file
     */
    public function forceAllCaps($flag = true)
    {
        $this->options[Shapefile::OPTION_DBF_FORCE_ALL_CAPS] = $flag;
        return $this;
    }

    /**
     * Array containing the names of the fields to ignore from the .dbf file
     */
    public function ignoreFields($fields = [])
    {
        $this->options[Shapefile::OPTION_DBF_IGNORED_FIELDS] = $fields;
        return $this;
    }

    /**
     * Defines a null padding character used in the .dbf file to represent null values
     */
    public function nullPaddingCharacter($character = null)
    {
        $this->options[Shapefile::OPTION_DBF_NULL_PADDING_CHAR] = $character;
        return $this;
    }

    /**
     * Returns a null value for invalid dates when reading .dbf files
     */
    public function nullifyInvalidDates($flag = true)
    {
        $this->options[Shapefile::OPTION_DBF_NULLIFY_INVALID_DATES] = $flag;
        return $this;
    }

    /**
     * Returns dates as DateTime objects instead of ISO strings (YYYY-MM-DD)
     */
    public function datesAsObjects($flag = true)
    {
        $this->options[Shapefile::OPTION_DBF_RETURN_DATES_AS_OBJECTS] = $flag;
        return $this;
    }

    /**
     * Reads Polyline and Polygon Geometries as Multi (ESRI specs do not distinguish between Linestring/MultiLinestring and Polygon/MultiPolygon)
     */
    public function forceMultipartGeometries($flag = true)
    {
        $this->options[Shapefile::OPTION_FORCE_MULTIPART_GEOMETRIES] = $flag;
        return $this;
    }

    /**
     * Ignores .dbf file (useful to recover corrupted Shapefiles). Data will not be available for geometries
     */
    public function ignoreFileDbf($flag = true)
    {
        $this->options[Shapefile::OPTION_IGNORE_FILE_DBF] = $flag;
        return $this;
    }

    /**
     * Ignores .shx file (useful to recover corrupted Shapefiles). This might not always work and random access to specific records will not be possible
     */
    public function ignoreFileShx($flag = true)
    {
        $this->options[Shapefile::OPTION_IGNORE_FILE_SHX] = $flag;
        return $this;
    }

    /**
     * Ignores geometries bounding boxes read from shapefile and computes some real ones instead
     */
    public function ignoreGeometriesBBoxes($flag = true)
    {
        $this->options[Shapefile::OPTION_IGNORE_GEOMETRIES_BBOXES] = $flag;
        return $this;
    }

    /**
     * Ignores bounding box read from shapefile and computes a real one instead
     */
    public function ignoreShapefileBBox($flag = true)
    {
        $this->options[Shapefile::OPTION_IGNORE_SHAPEFILE_BBOX] = $flag;
        return $this;
    }

    /**
     * Take no action when a closed ring issue is encountered
     */
    public function ignoreClosedRingIssues()
    {
        $this->options[Shapefile::OPTION_POLYGON_CLOSED_RINGS_ACTION] = Shapefile::ACTION_IGNORE;
        return $this;
    }

    /**
     * Throw an exception if a closed ring is encountered
     */
    public function checkForClosedRings()
    {
        $this->options[Shapefile::OPTION_POLYGON_CLOSED_RINGS_ACTION] = Shapefile::ACTION_CHECK;
        return $this;
    }

    /**
     * Attempt to fix closed ring issues
     */
    public function forceClosedRings()
    {
        $this->options[Shapefile::OPTION_POLYGON_CLOSED_RINGS_ACTION] = Shapefile::ACTION_FORCE;
        return $this;
    }

    /**
     * Allows Polygons orientation to be either clockwise or counterclockwise when reading Shapefiles
     */
    public function allowFlexiblePolygonOrientation()
    {
        $this->options[Shapefile::OPTION_POLYGON_ORIENTATION_READING_AUTOSENSE] = false;
        return $this;
    }

    /**
     * Force clockwise polygon output
     */
    public function clockwisePolygonOutput()
    {
        $this->options[Shapefile::OPTION_POLYGON_OUTPUT_ORIENTATION] = Shapefile::ORIENTATION_CLOCKWISE;
        return $this;
    }

    /**
     * Force counterclockwise polygon output
     */
    public function counterClockwisePolygonOutput()
    {
        $this->options[Shapefile::OPTION_POLYGON_OUTPUT_ORIENTATION] = Shapefile::ORIENTATION_COUNTERCLOCKWISE;
        return $this;
    }

    /**
     * Ignores M dimension from Shapefile
     */
    public function suppressM($flag = true)
    {
        $this->options[Shapefile::OPTION_SUPPRESS_M] = $flag;
        return $this;
    }

    /**
     * Ignores Z dimension from Shapefile
     */
    public function suppressZ($flag = true)
    {
        $this->options[Shapefile::OPTION_SUPPRESS_Z] = $flag;
        return $this;
    }

    /**
     * Set a destination projection that all coordinates should be transformed to
     */
    public function transformTo($destination)
    {
        $this->destination = new Projection($destination);
        return $this;
    }

    public function make($file)
    {
        return new Reader($file, $this->options, $this->destination);
    }
}
