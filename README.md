# A Laravel Collection style API for gasparesganga/php-shapefile

[![Latest Version on Packagist](https://img.shields.io/packagist/v/team-zac/laravel-shapefiles.svg?style=flat-square)](https://packagist.org/packages/team-zac/laravel-shapefiles)

The [gasparesganga/php-shapefile](https://www.github.com/gasparesganga/php-shapefile) package is extremely handy for working directly with Shapefiles, but we work mostly in Laravel and prefer the collection-style APIs found throughout. This package simply wraps the original ShapefileReader class in a LazyCollection to make iterating on it a bit more familiar.

## Installation

You can install the package via composer:

```bash
composer require teamzac/laravel-shapefiles
```

## Usage

``` php
$reader = new TeamZac\LaravelShapefiles\Reader('file_or_directory_path_here');

$reader->count(); // total number of records
$reader->each(function($geometry) {
	// do something
});
```

You can pass a reference to the .shp file, or the directory containing the .shp file, when instantiating the Reader.

When iterating in the original package, you'd receive an instance of ```Shapefile\Geometry\Geometry```. We also wrap this in a class that adds a couple of additional methods:

```php
...
$reader->each(function($geometry) {
	$geometry->asGeoJson(); // passes through to the getGeoJSON() method with the "as" verbiage commonly used in Laravel
	$geometry->asJson(); // a convenience method that runs the GeoJSON through json_decode first
	$geometry->getData('ID'); // allows retreival of a specific key in the data array
	$geometry->ID; // you can also access the data array as properties on the Geometry class

	$geometry->getDataArray(); // methods are passed through to the underlying Shapefile\Geometry\Geometry class
	$geometry->getRaw(); // you can retrieve the underlying Shapefile\Geometry\Geometry class with the getRaw() method
});
...
```

### To-do

We don't currently implement a wrapper around the ShapefileWriter class, since we don't use it. That might be added one day if we need it.

### Testing

``` bash
composer test
```

The package comes with a small shapefile containing two polygons. Note, we've only tested this with polygons but there's no reason why it shouldn't work with any other kind of feature.

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email chad@zactax.com instead of using the issue tracker.

## Credits

- [Gaspare Sganga](https://github.com/gasparesganga/php-shapefile) - Author of the underlying package
- [Chad Janicek](https://github.com/team-zac)
- [Laravel Package Boilerplate](https://laravelpackageboilerplate.com)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
