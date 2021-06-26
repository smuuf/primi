<?php

use \Tester\Assert;

use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Helpers\ComparisonLTR;

require __DIR__ . '/../bootstrap.php';

class TypeA extends AbstractValue {

	public function __construct($value) {
		$this->value = $value;
	}

	public function getStringRepr(): string {
		return 'A';
	}

	function isEqualTo(AbstractValue $right): ?bool {

		// This type doesn't know how to compare against anything.
		return null;

	}

}

class TypeB extends AbstractValue {

	public function __construct($value) {
		$this->value = $value;
	}

	public function getStringRepr(): string {
		return 'B';
	}

	function isEqualTo(AbstractValue $right): ?bool {
		return $this->value === $right->value;
	}

}

// TypeA vs TypeA, which do not know how to compare.
Assert::false(ComparisonLTR::evaluate('==', new TypeA(''), new TypeA('')));
Assert::true(ComparisonLTR::evaluate('!=', new TypeA(''), new TypeA('')));

// TypeA::compare() will be null, TypeB::compare() will be used instead.
Assert::true(ComparisonLTR::evaluate('==', new TypeA('y'), new TypeB('y')));
Assert::false(ComparisonLTR::evaluate('==', new TypeA('y'), new TypeB('x')));
Assert::true(ComparisonLTR::evaluate('==', new TypeA('x'), new TypeB('x')));
Assert::true(ComparisonLTR::evaluate('==', new TypeA('x'), new TypeB('x')));

Assert::false(ComparisonLTR::evaluate('!=', new TypeA('y'), new TypeB('y')));
Assert::true(ComparisonLTR::evaluate('!=', new TypeA('y'), new TypeB('x')));
Assert::false(ComparisonLTR::evaluate('!=', new TypeA('x'), new TypeB('x')));
Assert::false(ComparisonLTR::evaluate('!=', new TypeA('x'), new TypeB('x')));
