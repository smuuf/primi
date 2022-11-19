<?php

use \Tester\Assert;

use \Smuuf\Primi\Values\{
	BoolValue,
	NullValue,
	AbstractValue
};
use \Smuuf\Primi\Helpers\Interned;

require __DIR__ . '/../bootstrap.php';

function get_val(AbstractValue $v) {
	return $v->getCoreValue();
}

$null = new NullValue;
$nullTwo = new NullValue;
$true = Interned::bool(true);
$false = Interned::bool(false);

// Always null.

Assert::same(null, get_val(new NullValue));
Assert::same(null, get_val(new NullValue(1)));
Assert::same(null, get_val(new NullValue("2")));
Assert::same(null, get_val(new NullValue("hey")));
Assert::same(null, get_val(new NullValue("")));
Assert::same(null, get_val(new NullValue(1e6)));
Assert::same(null, get_val(new NullValue(true)));
Assert::same(null, get_val(new NullValue(false)));
Assert::same(null, get_val(new NullValue(null)));

//
// Comparison.
//

// Equality.
Assert::true($null->isEqualTo($nullTwo));
Assert::null($null->isEqualTo($false));
Assert::null($null->isEqualTo($true));

// Inequality.
Assert::false(!$null->isEqualTo($nullTwo));
Assert::null($null->isEqualTo($false));
Assert::null($null->isEqualTo($true));
