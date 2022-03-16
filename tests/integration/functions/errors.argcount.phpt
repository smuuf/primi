<?php

use \Smuuf\Primi\Ex\ErrorException;
use \Smuuf\Primi\Helpers\Interned;
use \Smuuf\Primi\Scope;
use \Smuuf\Primi\Interpreter;
use \Smuuf\Primi\Structures\FnContainer;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Values\FuncValue;
use \Smuuf\Primi\Values\StringValue;

use \Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

// Interpreter will create and use its own default config if needed.
$i = new Interpreter;

$someFunc = fn(AbstractValue $a, StringValue $b) => Interned::number(1);
$funcObject = new FuncValue(FnContainer::buildFromClosure($someFunc));
$mainScope = new Scope(['some_func' => $funcObject]);

Assert::exception(function() use ($i, $mainScope) {
	$src = <<<SRC
		x = some_func();
	SRC;
	$i->run($src, $mainScope);
}, ErrorException::class, '#Expected 2 arguments but got 0#');

Assert::exception(function() use ($i, $mainScope) {
	$src = <<<SRC
		x = some_func(123);
	SRC;
	$i->run($src, $mainScope);
}, ErrorException::class, '#Expected 2 arguments but got 1#');

Assert::exception(function() use ($i, $mainScope) {
	$src = <<<SRC
		x = some_func(123, 456);
	SRC;
	$resultScope = $i->run($src, $mainScope);
	var_dump($resultScope->getVariables());
}, ErrorException::class, "#Expected 'string' but got 'number' as argument 2#");
