<?php

use \Smuuf\Primi\Scope;
use \Smuuf\Primi\Interpreter;
use \Smuuf\Primi\Code\Source;
use Smuuf\Primi\Values\AbstractValue;
use \Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

// Interpreter will create and use its own default config if needed.
$interpreter = new Interpreter;

$src = <<<SRC

a = 1; b = c = 2;
print(a);

b = b + 1
// Variable c is still 2.

assert(b - a == c) // 3 - 1 == 2

assert(injected_str == 'yay a variable!', 'Passed injected_str')
assert(injected_num == 123.456, 'Passed injected_num')
assert(injected_numeric_str == 123.456, 'injected_numeric_str was numeric string and was converted to number - intuitive for PHP users')

SRC;

$source = new Source($src);
$scope = new Scope([
	'injected_str' => AbstractValue::buildAuto('yay a variable!'),
	'injected_num' => AbstractValue::buildAuto(123.456),
	'injected_numeric_str' => AbstractValue::buildAuto('123.456'),
]);

Assert::noError(fn() => $interpreter->run($source, $scope));
