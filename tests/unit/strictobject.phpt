<?php

use \Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$object = new class extends \Smuuf\Primi\StrictObject {
	// Nothing to see here.
};

Assert::exception(function() use ($object) {
	$x = $object->undefined;
}, \LogicException::class, '~read~');

Assert::exception(function() use ($object) {
	$object->undefined = 1;
}, \LogicException::class, '~write~');
