<?php

use Smuuf\Primi\Ex\EngineInternalError;
use \Tester\Assert;

use \Smuuf\Primi\Modules\DotPath;

require __DIR__ . '/../bootstrap.php';

//
// Absolute dot paths.
//

$dp = new DotPath('a');
Assert::false($dp->isRelative());
Assert::same(['a'], $dp->getParts());

$dp = new DotPath('a.b');
Assert::false($dp->isRelative());
Assert::same(['a', 'b'], $dp->getParts());

$dp = new DotPath('a.b.c');
Assert::false($dp->isRelative());
Assert::same(['a', 'b', 'c'], $dp->getParts());

$dp = new DotPath('a1.b2.c3');
Assert::false($dp->isRelative());
Assert::same(['a1', 'b2', 'c3'], $dp->getParts());

//
// Relative dot paths.
//

$dp = new DotPath('.a.b.c');
Assert::true($dp->isRelative());
Assert::same(['.', 'a', 'b', 'c'], $dp->getParts());

$dp = new DotPath('..a.b.c');
Assert::true($dp->isRelative());
Assert::same(['.', '..', 'a', 'b', 'c'], $dp->getParts());

$dp = new DotPath('...a.b.c');
Assert::true($dp->isRelative());
Assert::same(['.', '..', '..', 'a', 'b', 'c'], $dp->getParts());

$dp = new DotPath('.a');
Assert::true($dp->isRelative());
Assert::same(['.', 'a'], $dp->getParts());

$dp = new DotPath('..a');
Assert::true($dp->isRelative());
Assert::same(['.', '..', 'a'], $dp->getParts());

$dp = new DotPath('...a');
Assert::true($dp->isRelative());
Assert::same(['.', '..', '..', 'a'], $dp->getParts());

//
// Test exceptions caused by invalid dot paths.
//

Assert::exception(
	fn() => new DotPath(''),
	EngineInternalError::class
);

Assert::exception(
	fn() => new DotPath('0'),
	EngineInternalError::class
);

Assert::exception(
	fn() => new DotPath('123.a.b'),
	EngineInternalError::class
);

Assert::exception(
	fn() => new DotPath('..123.a.b'),
	EngineInternalError::class
);

Assert::exception(
	fn() => new DotPath('a/b'),
	EngineInternalError::class
);

Assert::exception(
	fn() => new DotPath('a|b'),
	EngineInternalError::class
);

Assert::exception(
	fn() => new DotPath('a.b/c'),
	EngineInternalError::class
);

Assert::exception(
	fn() => new DotPath('x.3.z'),
	EngineInternalError::class
);
