<?php

//
// Resolved issue:
// Fix precedence/affinity of power/exponent "**" operator
// https://github.com/smuuf/primi/issues/35
//

use \Tester\Assert;

use \Smuuf\Primi\Scope;
use \Smuuf\Primi\Interpreter;
use \Smuuf\Primi\Values\NumberValue;

require __DIR__ . "/../bootstrap.php";

$interpreter = new Interpreter;
$mainScope = new Scope;
$interpreter->run('a = 2 ** 5 ** 2', $mainScope);

$a = $mainScope->getVariable('a');
Assert::type(NumberValue::class, $a);
Assert::same('33554432', $a->getStringValue());

$interpreter->run('a = 2 ** 3 ** 4 ** 1 ** 2', $mainScope);

$a = $mainScope->getVariable('a');
Assert::type(NumberValue::class, $a);
Assert::same('2417851639229258349412352', $a->getStringValue());

$interpreter->run('a = 2 + 3 ** 3 * 16 ** 2 + 4', $mainScope);

$a = $mainScope->getVariable('a');
Assert::type(NumberValue::class, $a);
Assert::same('6918', $a->getStringValue());

$interpreter->run('a = 1 + 2 ** 3 - 4 ** 5 * 6 ** 7 / 8', $mainScope);

$a = $mainScope->getVariable('a');
Assert::type(NumberValue::class, $a);
Assert::same('-35831799', $a->getStringValue());
