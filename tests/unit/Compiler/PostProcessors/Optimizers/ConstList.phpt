<?php

declare(strict_types=1);

use Smuuf\Primi\Compiler\BytecodeDLL;
use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Helpers\Interned;
use Smuuf\Primi\Compiler\Op;
use Smuuf\Primi\Compiler\PostProcessors\Optimizer;
use Smuuf\Primi\Compiler\PostProcessors\Optimizers\ConstList;
use Tester\Assert;

require __DIR__ . '/../../../../bootstrap.php';

$ops = [
	new Op(Machine::OP_LOAD_CONST, [Interned::number('1')]),
	new Op(Machine::OP_LOAD_CONST, [Interned::number('2')]),
	new Op(Machine::OP_LOAD_CONST, [Interned::number('3')]),
	new Op(Machine::OP_BUILD_LIST, [3]),
	new Op(Machine::OP_LOAD_CONST, [Interned::number('4')]),
	new Op(Machine::OP_LOAD_CONST, [Interned::number('5')]),
	new Op(Machine::OP_BUILD_LIST, [2]),
	new Op(Machine::OP_ADD),
	new Op(Machine::OP_LOAD_NAME, ["var_a"]),
	new Op(Machine::OP_LOAD_CONST, [Interned::number('6')]),
	new Op(Machine::OP_LOAD_CONST, [Interned::number('7')]),
	new Op(Machine::OP_BUILD_LIST, [3]),
	new Op(Machine::OP_ADD),
	new Op(Machine::OP_RETURN),
];

$expected = [
	new Op(Machine::OP_BUILD_CONST_LIST, [[Interned::number('1'), Interned::number('2'), Interned::number('3')]]),
	new Op(Machine::OP_BUILD_CONST_LIST, [[Interned::number('4'), Interned::number('5')]]),
	new Op(Machine::OP_ADD),
	new Op(Machine::OP_LOAD_NAME, ["var_a"]),
	new Op(Machine::OP_LOAD_CONST, [Interned::number('6')]),
	new Op(Machine::OP_LOAD_CONST, [Interned::number('7')]),
	new Op(Machine::OP_BUILD_LIST, [3]),
	new Op(Machine::OP_ADD),
	new Op(Machine::OP_RETURN),
];


$dll = BytecodeDLL::fromArray($ops);

$optimizer = new Optimizer;
$optimizer->process($dll, [new ConstList]); // Is done in-place.
$actual = $dll->toArray();

Assert::equal($expected, $actual);
