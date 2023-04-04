<?php

declare(strict_types=1);

use Tester\Assert;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Compiler\BytecodeDLL;
use Smuuf\Primi\Compiler\Op;
use Smuuf\Primi\Compiler\PostProcessors\Peephole\PeepholePattern;

require __DIR__ . '/../../../bootstrap.php';

// These don't make sense, but will do for our test.
$ops = [
	new Op(Machine::OP_ADD),
	new Op(Machine::OP_JUMP),
	new Op(Machine::OP_LABEL, args: ['AAA']),
	new Op(Machine::OP_NEGATE),
	new Op(Machine::OP_LABEL, args: ['BBB']),
	new Op(Machine::OP_JUMP),
	new Op(Machine::OP_LABEL, args: ['AAA']),
	new Op(Machine::OP_JUMP),
	new Op(Machine::OP_LABEL, args: ['CCC']),
	new Op(Machine::OP_ADD),
	new Op(Machine::OP_STORE_NAME, args: ['yay']),
	new Op(Machine::OP_LOAD_NAME, args: ['yay']),
	new Op(Machine::OP_ADD),
	new Op(Machine::OP_STORE_NAME, args: ['bruh']),
	new Op(Machine::OP_LOAD_NAME, args: ['__not_bruh']),
	new Op(Machine::OP_DUP_TOP),
	new Op(Machine::OP_POP),
	new Op(Machine::OP_STORE_NAME, args: ['__not_bruh_2']),
	new Op(Machine::OP_LOAD_NAME, args: ['bruh']),
	new Op(Machine::OP_POP),
	new Op(Machine::OP_JUMP),
	new Op(Machine::OP_POP),
	new Op(Machine::OP_NEGATE),
	new Op(Machine::OP_NEGATE),
	new Op(Machine::OP_LABEL, args: ['AAA']),
	new Op(Machine::OP_STORE_NAME, args: ['bruh']),
	new Op(Machine::OP_LOAD_NAME, args: ['bruh']),
];

//
//
//

$dll = BytecodeDLL::fromArray($ops);
$pattern = (new PeepholePattern)
	->add(Machine::OP_STORE_NAME)
	->add(Machine::OP_LOAD_NAME);

$counter = 0;
foreach ($pattern->scan($dll) as $replacer) {

	$replacer(function($found) use (&$counter) {
		$counter++;
		return [
			new Op("__STORE_LOAD__{$counter}"),
		];
	});

}

Assert::equal(4, $counter);

$expected = [
	new Op(Machine::OP_ADD),
	new Op(Machine::OP_JUMP),
	new Op(Machine::OP_LABEL, args: ['AAA']),
	new Op(Machine::OP_NEGATE),
	new Op(Machine::OP_LABEL, args: ['BBB']),
	new Op(Machine::OP_JUMP),
	new Op(Machine::OP_LABEL, args: ['AAA']),
	new Op(Machine::OP_JUMP),
	new Op(Machine::OP_LABEL, args: ['CCC']),
	new Op(Machine::OP_ADD),
	new Op("__STORE_LOAD__1"),
	new Op(Machine::OP_ADD),
	new Op("__STORE_LOAD__2"),
	new Op(Machine::OP_DUP_TOP),
	new Op(Machine::OP_POP),
	new Op("__STORE_LOAD__3"),
	new Op(Machine::OP_POP),
	new Op(Machine::OP_JUMP),
	new Op(Machine::OP_POP),
	new Op(Machine::OP_NEGATE),
	new Op(Machine::OP_NEGATE),
	new Op(Machine::OP_LABEL, args: ['AAA']),
	new Op("__STORE_LOAD__4"),
];

Assert::equal($expected, $dll->toArray());

//
//
//

$dll = BytecodeDLL::fromArray($ops);
$pattern = (new PeepholePattern)
	->add(Machine::OP_JUMP)
	->add(Machine::OP_LABEL, fn($op) => $op->args[0] === 'AAA');

$counter = 0;
foreach ($pattern->scan($dll) as $replacer) {

	$replacer(function($found) use (&$counter) {
		$counter++;
		return [
			new Op("__JUMP_LABEL_AAA__{$counter}"),
			new Op("__SOME"),
			new Op("__MORE"),
			new Op("__FLUFF"),
		];
	});

}

Assert::equal(2, $counter);

//
//
//

$pattern = (new PeepholePattern)
	->add(Machine::OP_STORE_NAME, onMatch: fn($op, &$storage) => $storage['name'] = $op->args[0])
	->add(Machine::OP_LOAD_NAME, filter: fn($op, &$storage) => $op->args[0] === $storage['name']);

$counter = 0;
foreach ($pattern->scan($dll) as $replacer) {

	$replacer(function($found) use (&$counter) {
		$counter++;
		return [
			new Op("_LOADSTORENAME_{$found[0]->args[0]}"),
		];
	});

}

Assert::equal(2, $counter);

$expected = [
	new Op(Machine::OP_ADD),
	new Op("__JUMP_LABEL_AAA__1"),
	new Op("__SOME"),
	new Op("__MORE"),
	new Op("__FLUFF"),
	new Op(Machine::OP_NEGATE),
	new Op(Machine::OP_LABEL, args: ['BBB']),
	new Op("__JUMP_LABEL_AAA__2"),
	new Op("__SOME"),
	new Op("__MORE"),
	new Op("__FLUFF"),
	new Op(Machine::OP_JUMP),
	new Op(Machine::OP_LABEL, args: ['CCC']),
	new Op(Machine::OP_ADD),
	new Op("_LOADSTORENAME_yay"),
	new Op(Machine::OP_ADD),
	new Op(Machine::OP_STORE_NAME, args: ['bruh']),
	new Op(Machine::OP_LOAD_NAME, args: ['__not_bruh']),
	new Op(Machine::OP_DUP_TOP),
	new Op(Machine::OP_POP),
	new Op(Machine::OP_STORE_NAME, args: ['__not_bruh_2']),
	new Op(Machine::OP_LOAD_NAME, args: ['bruh']),
	new Op(Machine::OP_POP),
	new Op(Machine::OP_JUMP),
	new Op(Machine::OP_POP),
	new Op(Machine::OP_NEGATE),
	new Op(Machine::OP_NEGATE),
	new Op(Machine::OP_LABEL, args: ['AAA']),
	new Op("_LOADSTORENAME_bruh"),
];

Assert::equal($expected, $dll->toArray());
