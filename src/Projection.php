<?php

namespace TeamZac\LaravelShapefiles;

use proj4php\{Proj, Proj4php, Point};

class Projection
{
    protected $proj4;
    protected $parent;

    public function __construct($prj)
    {
        $this->proj4 = new Proj4php;
        $this->parent = new Proj($prj, $this->proj4);
    }

    public function transformPoint($coordinates, $destination)
    {
        $pointSrc = new Point($coordinates[0], $coordinates[1], $this->getParent());
        $pointDest = $this->proj4->transform($destination->getParent(), $pointSrc);

        return [$pointDest->x, $pointDest->y];
    }

    public function getParent()
    {
        return $this->parent;
    }
}
