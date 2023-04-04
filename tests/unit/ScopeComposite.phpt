<?php

use Smuuf\Primi\Helpers\Interned;
use Tester\Assert;

use Smuuf\Primi\Scope;
use Smuuf\Primi\ScopeComposite;

require __DIR__ . '/../bootstrap.php';

$scopeA = new Scope(['a' => Interned::number('1'), 'b' => Interned::number('2')]);
$scopeB = new Scope(['b' => Interned::number('3'), 'c' => Interned::number('4')]);

$comp = new ScopeComposite($scopeA, $scopeB);

Assert::same(Interned::number('1'), $comp->getVariable('a')); // From scope A
Assert::same(Interned::number('2'), $comp->getVariable('b')); // From scope A - priority over scope B.
Assert::same(Interned::number('4'), $comp->getVariable('c')); // From scope B - missing in scope A.

$vars = $comp->getVariables();
Assert::equal([
	'a' => Interned::number('1'),
	'b' => Interned::number('2'),
	'c' => Interned::number('4'),
], $vars);
