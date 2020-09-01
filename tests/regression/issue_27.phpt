<?php

//
// Resolved issue:
//
// Number 0.0000001 is recognized as string 1.0E-8 in the function result.
//
// https://github.com/smuuf/primi/issues/27
//

use \Tester\Assert;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Interpreter;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Structures\NumberValue;

require __DIR__ . "/../bootstrap.php";

$context = new Context;
$interpreter = new Interpreter($context);
$context->setVariable('teste', Value::buildAutomatic(function() {
        return 0.00000000001;
}));
$interpreter->run('a = teste(); b = 3 + a; c = b ** 3');

$a = $context->getVariable('a');
Assert::type(NumberValue::class, $a);
Assert::same('0.00000000001', $a->getStringValue());

$b = $context->getVariable('b');
Assert::type(NumberValue::class, $b);
Assert::same('3.00000000001', $b->getStringValue());

$c = $context->getVariable('c');
Assert::type(NumberValue::class, $c);
Assert::same('27.000000000270000000000900000000001', $c->getStringValue());
