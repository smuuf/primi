<?php

use \Tester\Assert;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Helpers\ComparisonLTR;

require __DIR__ . '/../bootstrap.php';

class TypeA extends Value {

	public function __construct($value) {
		$this->value = $value;
	}

	public function getStringRepr(): string {
		return 'A';
	}

	function isEqualTo(Value $right): ?bool {

		// This type doesn't know how to compare against anything.
		return null;

	}

}

class TypeB extends Value {

	public function __construct($value) {
		$this->value = $value;
	}

	public function getStringRepr(): string {
		return 'B';
	}

	function isEqualTo(Value $right): ?bool {
		return $this->value == $right->value;
	}

}

function get_val(Value $v) {
	return $v->getInternalValue();
}

// TypeA vs TypeA, which doesn't know how to compare
Assert::false(get_val(ComparisonLTR::evaluate('==', new TypeA(''), new TypeA(''))));
Assert::true(get_val(ComparisonLTR::evaluate('!=', new TypeA(''), new TypeA(''))));

// TypeA::compare() will be null, TypeB::compare() will be used instead.
Assert::true(get_val(ComparisonLTR::evaluate('==', new TypeA('y'), new TypeB('y'))));
Assert::false(get_val(ComparisonLTR::evaluate('==', new TypeA('y'), new TypeB('x'))));
Assert::true(get_val(ComparisonLTR::evaluate('==', new TypeA('x'), new TypeB('x'))));
Assert::true(get_val(ComparisonLTR::evaluate('==', new TypeA('x'), new TypeB('x'))));

Assert::false(get_val(ComparisonLTR::evaluate('!=', new TypeA('y'), new TypeB('y'))));
Assert::true(get_val(ComparisonLTR::evaluate('!=', new TypeA('y'), new TypeB('x'))));
Assert::false(get_val(ComparisonLTR::evaluate('!=', new TypeA('x'), new TypeB('x'))));
Assert::false(get_val(ComparisonLTR::evaluate('!=', new TypeA('x'), new TypeB('x'))));
