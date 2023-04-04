<?php

declare(strict_types=1);

use Smuuf\Primi\Compiler\BytecodeDLL;
use Tester\Assert;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Compiler\Op;

require __DIR__ . '/../../bootstrap.php';

function get_dll() {

	$ops = [
		new Op(Machine::OP_ADD),
		new Op(Machine::OP_SUB),
		new Op(Machine::OP_MULTI),
		new Op(Machine::OP_LOAD_NAME),
		new Op(Machine::OP_LOAD_CONST),
		new Op(Machine::OP_BUILD_LIST),
	];

	return BytecodeDLL::fromArray($ops);

}

//
// Splice with empty replacement - will simply remove items from the
// specified range.
//

$dll = get_dll();
$dll->splice(0, 1, []);

Assert::equal([
	new Op(Machine::OP_SUB),
	new Op(Machine::OP_MULTI),
	new Op(Machine::OP_LOAD_NAME),
	new Op(Machine::OP_LOAD_CONST),
	new Op(Machine::OP_BUILD_LIST),
], $dll->toArray());

//
// Splice with one item as replacement instead of a single item.
//

$dll = get_dll();
$dll->splice(0, 1, [new Op(Machine::OP_NEGATE)]);

Assert::equal([
	new Op(Machine::OP_NEGATE),
	new Op(Machine::OP_SUB),
	new Op(Machine::OP_MULTI),
	new Op(Machine::OP_LOAD_NAME),
	new Op(Machine::OP_LOAD_CONST),
	new Op(Machine::OP_BUILD_LIST),
], $dll->toArray());

//
// Splice with one item as replacement instead of several items.
//

$dll = get_dll();
$dll->splice(0, 4, [new Op(Machine::OP_NEGATE)]);

Assert::equal([
	new Op(Machine::OP_NEGATE),
	new Op(Machine::OP_LOAD_CONST),
	new Op(Machine::OP_BUILD_LIST),
], $dll->toArray());

$dll = get_dll();
$dll->splice(1, 4, [new Op(Machine::OP_NEGATE)]);

Assert::equal([
	new Op(Machine::OP_ADD),
	new Op(Machine::OP_NEGATE),
	new Op(Machine::OP_BUILD_LIST),
], $dll->toArray());

//
// Splice with several items as replacement.
//

$dll = get_dll();
$dll->splice(1, 4, [
	new Op(Machine::OP_NEGATE),
	new Op(Machine::OP_UNPACK_ITERABLE),
	new Op(Machine::OP_SWAP),
]);

Assert::equal([
	new Op(Machine::OP_ADD),
	new Op(Machine::OP_NEGATE),
	new Op(Machine::OP_UNPACK_ITERABLE),
	new Op(Machine::OP_SWAP),
	new Op(Machine::OP_BUILD_LIST),
], $dll->toArray());


//
// Out-of-range exceptions for invalid ranges.
//

Assert::exception(function() {
	$dll = get_dll();
	$dll->splice(-1, 1, [new Op(Machine::OP_NEGATE)]);
}, OutOfRangeException::class, 'Invalid range for splicing');

Assert::exception(function() {
	$dll = get_dll();
	$dll->splice(1, 40, [new Op(Machine::OP_NEGATE)]);
}, OutOfRangeException::class, 'Invalid range for splicing');

Assert::exception(function() {
	$dll = get_dll();
	$dll->splice(-99, 99, [new Op(Machine::OP_NEGATE)]);
}, OutOfRangeException::class, 'Invalid range for splicing');
