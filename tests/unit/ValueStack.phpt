<?php

use Tester\Assert;

use Smuuf\Primi\VM\ValueStack;
use Smuuf\Primi\Helpers\Interned;

require __DIR__ . '/../bootstrap.php';

function fresh_vs(): ValueStack {

	$vs = new ValueStack();
	$vs->push(Interned::number("1"));
	$vs->push(Interned::number("2"));
	$vs->push(Interned::number("3"));
	$vs->push(Interned::number("4"));
	$vs->push(Interned::number("5"));

	return $vs;

}

//
// ValueStack::popToListRev()
//

$vs = fresh_vs();
Assert::count(5, $vs);

$tmp = $vs->popToListRev(3);
Assert::same([
	Interned::number("3"),
	Interned::number("4"),
	Interned::number("5"),
], $tmp);

Assert::count(2, $vs);
Assert::same(Interned::number("2"), $vs[0]);
Assert::same(Interned::number("1"), $vs[1]);

$vs->push(Interned::number("a"));
$vs->push(Interned::number("b"));
$vs->push(Interned::number("c"));

Assert::count(5, $vs);
Assert::same(Interned::number("c"), $vs[0]);
Assert::same(Interned::number("b"), $vs[1]);
Assert::same(Interned::number("a"), $vs[2]);
Assert::same(Interned::number("2"), $vs[3]);
Assert::same(Interned::number("1"), $vs[4]);

//
// ValueStack::swap()
//

$vs = fresh_vs();

Assert::count(5, $vs);
Assert::same(Interned::number("5"), $vs[0]);
Assert::same(Interned::number("4"), $vs[1]);
Assert::same(Interned::number("3"), $vs[2]);
Assert::same(Interned::number("2"), $vs[3]);
Assert::same(Interned::number("1"), $vs[4]);

$tmp = $vs->swap(3);

Assert::count(5, $vs);
Assert::same(Interned::number("3"), $vs[0]); // Originally 3rd.
Assert::same(Interned::number("4"), $vs[1]);
Assert::same(Interned::number("5"), $vs[2]); // Originally on top.
Assert::same(Interned::number("2"), $vs[3]);
Assert::same(Interned::number("1"), $vs[4]);

$tmp = $vs->swap(4);

Assert::count(5, $vs);
Assert::same(Interned::number("2"), $vs[0]);
Assert::same(Interned::number("4"), $vs[1]);
Assert::same(Interned::number("5"), $vs[2]);
Assert::same(Interned::number("3"), $vs[3]);
Assert::same(Interned::number("1"), $vs[4]);

//
// ValueStack::copy()
//

$vs = fresh_vs();

Assert::count(5, $vs);
Assert::same(Interned::number("5"), $vs[0]);
Assert::same(Interned::number("4"), $vs[1]);
Assert::same(Interned::number("3"), $vs[2]);
Assert::same(Interned::number("2"), $vs[3]);
Assert::same(Interned::number("1"), $vs[4]);

$tmp = $vs->copy(2);

Assert::count(6, $vs);
Assert::same(Interned::number("4"), $vs[0]);
Assert::same(Interned::number("5"), $vs[1]);
Assert::same(Interned::number("4"), $vs[2]);
Assert::same(Interned::number("3"), $vs[3]);
Assert::same(Interned::number("2"), $vs[4]);
Assert::same(Interned::number("1"), $vs[5]);

$tmp = $vs->copy(2);

Assert::count(7, $vs);
Assert::same(Interned::number("5"), $vs[0]);
Assert::same(Interned::number("4"), $vs[1]);
Assert::same(Interned::number("5"), $vs[2]);
Assert::same(Interned::number("4"), $vs[3]);
Assert::same(Interned::number("3"), $vs[4]);
Assert::same(Interned::number("2"), $vs[5]);
Assert::same(Interned::number("1"), $vs[6]);

//
// ValueStack::popN()
//

$vs = fresh_vs();

Assert::count(5, $vs);
Assert::same(Interned::number("5"), $vs[0]);
Assert::same(Interned::number("4"), $vs[1]);
Assert::same(Interned::number("3"), $vs[2]);
Assert::same(Interned::number("2"), $vs[3]);
Assert::same(Interned::number("1"), $vs[4]);

$tmp = $vs->popN(1);

Assert::count(4, $vs);
Assert::same(Interned::number("4"), $vs[0]);
Assert::same(Interned::number("3"), $vs[1]);
Assert::same(Interned::number("2"), $vs[2]);
Assert::same(Interned::number("1"), $vs[3]);

$tmp = $vs->popN(3);

Assert::count(1, $vs);
Assert::same(Interned::number("1"), $vs[0]);

//
// ValueStack::clear()
//

$vs = fresh_vs();

Assert::count(5, $vs);
$vs->clear();
Assert::count(0, $vs);
