<?php

//
// Resolved issue:
// Number 0.0000001 is recognized as string 1.0E-8 in the function result.
// https://github.com/smuuf/primi/issues/27
//

use \Tester\Assert;

use \Smuuf\Primi\Scope;
use \Smuuf\Primi\Interpreter;
use \Smuuf\Primi\Values\NumberValue;
use \Smuuf\Primi\Values\AbstractValue;

require __DIR__ . "/../bootstrap.php";

$interpreter = new Interpreter;

$mainScope = new Scope;
$mainScope->setVariable('teste', AbstractValue::buildAuto(function() {
        return 0.00000000001;
}));

$interpreter->run('a = teste(); b = 3 + a; c = b ** 3', $mainScope);

$a = $mainScope->getVariable('a');
Assert::type(NumberValue::class, $a);
Assert::same('0.00000000001', $a->getStringValue());

$b = $mainScope->getVariable('b');
Assert::type(NumberValue::class, $b);
Assert::same('3.00000000001', $b->getStringValue());

$c = $mainScope->getVariable('c');
Assert::type(NumberValue::class, $c);
Assert::same('27.000000000270000000000900000000001', $c->getStringValue());
