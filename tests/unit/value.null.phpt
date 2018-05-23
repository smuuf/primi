<?php

use \Tester\Assert;

use \Smuuf\Primi\Structures\{
	BoolValue,
	NullValue,
	Value
};

require __DIR__ . '/../bootstrap.php';

function get_val(Value $v) {
	return $v->getInternalValue();
}

$null = new NullValue;
$nullTwo = new NullValue;
$true = new BoolValue(true);
$false = new BoolValue(false);

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
Assert::same(true, get_val($null->doComparison("==", $nullTwo)));
Assert::same(true, get_val($null->doComparison("==", $false)));
Assert::same(false, get_val($null->doComparison("==", $true)));

// Inequality.
Assert::same(false, get_val($null->doComparison("!=", $nullTwo)));
Assert::same(false, get_val($null->doComparison("!=", $false)));
Assert::same(true, get_val($null->doComparison("!=", $true)));
