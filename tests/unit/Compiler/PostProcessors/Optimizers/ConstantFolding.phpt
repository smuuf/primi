<?php

declare(strict_types=1);

use Smuuf\Primi\Compiler\BytecodeDLL;
use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Helpers\Interned;
use Smuuf\Primi\Compiler\Op;
use Smuuf\Primi\Compiler\PostProcessors\Optimizer;
use Smuuf\Primi\Compiler\PostProcessors\Optimizers\ConstantFolding;
use Tester\Assert;

require __DIR__ . '/../../../../bootstrap.php';

$ops = [
	new Op(Machine::OP_LOAD_CONST, [Interned::number('2')]),
	new Op(Machine::OP_LOAD_CONST, [Interned::number('8')]),
	new Op(Machine::OP_LOAD_CONST, [Interned::number('7')]),
	new Op(Machine::OP_MULTI),
	new Op(Machine::OP_LOAD_CONST, [Interned::number('4')]),
	new Op(Machine::OP_DIV),
	new Op(Machine::OP_ADD),
	new Op(Machine::OP_LOAD_CONST, [Interned::string("yay")]),
	new Op(Machine::OP_LOAD_CONST, [Interned::number('4')]),
	new Op(Machine::OP_MULTI),
	new Op(Machine::OP_ADD),
	new Op(Machine::OP_LOAD_NAME, ['b']),
	new Op(Machine::OP_LOAD_CONST, [Interned::number('0')]),
	new Op(Machine::OP_ADD),
	new Op(Machine::OP_LOAD_CONST, [Interned::number('1')]),
	new Op(Machine::OP_LOAD_CONST, [Interned::number('2')]),
	new Op(Machine::OP_ADD),
	new Op(Machine::OP_DUP_TOP),
	new Op(Machine::OP_STORE_NAME, ['b']),
	new Op(Machine::OP_LOAD_CONST, [Interned::number('4')]),
	new Op(Machine::OP_LOAD_CONST, [Interned::number('7')]),
	new Op(Machine::OP_MULTI),
	new Op(Machine::OP_DIV),
	new Op(Machine::OP_ADD),
	new Op(Machine::OP_DUP_TOP),
	new Op(Machine::OP_STORE_NAME, ['b']),
	new Op(Machine::OP_ADD),
	new Op(Machine::OP_BUILD_LIST, [1]),
	new Op(Machine::OP_LOAD_CONST, [Interned::string("ahoj")]),
	new Op(Machine::OP_LOAD_CONST, [Interned::number('3')]),
	new Op(Machine::OP_MULTI),
	new Op(Machine::OP_LOAD_CONST, [Interned::number('0')]),
	new Op(Machine::OP_LOAD_CONST, [Interned::number('0')]),
	new Op(Machine::OP_LOAD_CONST, [Interned::number('0')]),
	new Op(Machine::OP_BUILD_LIST, [3]),
	new Op(Machine::OP_ADD),
	new Op(Machine::OP_ADD),
	new Op(Machine::OP_DUP_TOP),
	new Op(Machine::OP_STORE_NAME, ['a']),
	new Op(Machine::OP_RETURN),
];

$expected = [
	new Op(Machine::OP_LOAD_CONST, [Interned::number('16')]),
	new Op(Machine::OP_LOAD_CONST, [Interned::string("yayyayyayyay")]),
	new Op(Machine::OP_ADD),
	new Op(Machine::OP_LOAD_NAME, ['b']),
	new Op(Machine::OP_LOAD_CONST, [Interned::number('0')]),
	new Op(Machine::OP_ADD),
	new Op(Machine::OP_LOAD_CONST, [Interned::number('3')]),
	new Op(Machine::OP_DUP_TOP),
	new Op(Machine::OP_STORE_NAME, ['b']),
	new Op(Machine::OP_LOAD_CONST, [Interned::number('28')]),
	new Op(Machine::OP_DIV),
	new Op(Machine::OP_ADD),
	new Op(Machine::OP_DUP_TOP),
	new Op(Machine::OP_STORE_NAME, ['b']),
	new Op(Machine::OP_ADD),
	new Op(Machine::OP_BUILD_LIST, [1]),
	new Op(Machine::OP_LOAD_CONST, [Interned::string("ahojahojahoj")]),
	new Op(Machine::OP_LOAD_CONST, [Interned::number('0')]),
	new Op(Machine::OP_LOAD_CONST, [Interned::number('0')]),
	new Op(Machine::OP_LOAD_CONST, [Interned::number('0')]),
	new Op(Machine::OP_BUILD_LIST, [3]),
	new Op(Machine::OP_ADD),
	new Op(Machine::OP_ADD),
	new Op(Machine::OP_DUP_TOP),
	new Op(Machine::OP_STORE_NAME, ['a']),
	new Op(Machine::OP_RETURN),
];


$dll = BytecodeDLL::fromArray($ops);

$optimizer = new Optimizer;
$optimizer->process($dll, [new ConstantFolding]); // Is done in-place.
$actual = $dll->toArray();

Assert::equal($expected, $actual);
