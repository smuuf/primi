<?php

use \Smuuf\Primi\Ex\EngineInternalError;
use \Smuuf\Primi\Values\StringValue;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Values\InstanceValue;
use \Smuuf\Primi\Helpers\Types;

use \Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$tmp = Types::php_classes_to_primi_types([
	AbstractValue::class,
	StringValue::class,
	gettype(null),
]);
Assert::same('object|string|null', $tmp);

$tmp = Types::php_classes_to_primi_types([
	StringValue::class,
	gettype(null),
	AbstractValue::class,
	InstanceValue::class,
]);
Assert::same('string|null|object|object', $tmp);

//
// Passing a string that doesn't represent a class which represents a known
// basic Primi type will result in engine internal error.
//

Assert::exception(
	fn() => Types::php_classes_to_primi_types(['THIS-IS-NONSENSE']),
	EngineInternalError::class,
	'#Unable to resolve basic type name for class.*THIS-IS-NONSENSE#',
);
