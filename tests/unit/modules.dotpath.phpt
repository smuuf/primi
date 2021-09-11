<?php

use \Smuuf\Primi\Ex\ImportBeyondTopException;
use \Smuuf\Primi\Ex\EngineInternalError;
use \Tester\Assert;

use \Smuuf\Primi\Modules\Dotpath;

require __DIR__ . '/../bootstrap.php';

//
// Absolute dotpaths.
//

$dp = new Dotpath('a');
Assert::same('a', $dp->getAbsolute());

$dp = new Dotpath('a.b');
Assert::same('a.b', $dp->getAbsolute());

$dp = new Dotpath('a.b.c');
Assert::same('a.b.c', $dp->getAbsolute());

$dp = new Dotpath('a1.b2.c3');
Assert::same('a1.b2.c3', $dp->getAbsolute());

//
// Absolute dotpaths with origin - origin is ignored.
//

$dp = new Dotpath('a.b.c', '');
Assert::same('a.b.c', $dp->getAbsolute());

$dp = new Dotpath('a.b.c', 'x');
Assert::same('a.b.c', $dp->getAbsolute());

$dp = new Dotpath('a', 'x.y.z');
Assert::same('a', $dp->getAbsolute());

$dp = new Dotpath('a.b.c', 'x.y.z');
Assert::same('a.b.c', $dp->getAbsolute());

//
// Relative dotpaths.
//

$dp = new Dotpath('.a', 'package');
Assert::same('package.a', $dp->getAbsolute());

$dp = new Dotpath('.a.b.c', 'package.x');
Assert::same('package.x.a.b.c', $dp->getAbsolute());

$dp = new Dotpath('.a.b.c', 'package.x.y');
Assert::same('package.x.y.a.b.c', $dp->getAbsolute());

$dp = new Dotpath('..a.b.c', 'package.x.y');
Assert::same('package.x.a.b.c', $dp->getAbsolute());

$dp = new Dotpath('...a.b.c', 'package.x.y');
Assert::same('package.a.b.c', $dp->getAbsolute());

// Relative import reached beyond even the package, so expect an error.
Assert::exception(
	fn() => new Dotpath('.....a.b.c', 'package.x.y'),
	ImportBeyondTopException::class
);

Assert::exception(
	fn() => new Dotpath('................a.b.c', 'package.x.y'),
	ImportBeyondTopException::class
);

//
// Test internal exceptions caused by invalid dotpaths.
//

Assert::exception(
	fn() => new Dotpath(''),
	EngineInternalError::class
);

Assert::exception(
	fn() => new Dotpath('0'),
	EngineInternalError::class
);

Assert::exception(
	fn() => new Dotpath('123.a.b'),
	EngineInternalError::class
);

Assert::exception(
	fn() => new Dotpath('..123.a.b'),
	EngineInternalError::class
);

Assert::exception(
	fn() => new Dotpath('a/b'),
	EngineInternalError::class
);

Assert::exception(
	fn() => new Dotpath('a|b'),
	EngineInternalError::class
);

Assert::exception(
	fn() => new Dotpath('a.b/c'),
	EngineInternalError::class
);

Assert::exception(
	fn() => new Dotpath('x.3.z'),
	EngineInternalError::class
);
