<?php

use \Tester\Assert;

use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Helpers\ComparisonLTR;
use \Smuuf\Primi\Values\TypeValue;

require __DIR__ . '/../bootstrap.php';

$customTypeA = new TypeValue("customA");
$customTypeB = new TypeValue("customB");

class CustomTypeValueA extends AbstractValue {

	public function __construct($value) {
		$this->value = $value;
	}

	public function getType(): TypeValue {
		global $customTypeA;
		return $customTypeA;
	}

	public function getStringRepr(): string {
		return 'A';
	}

	function isEqualTo(AbstractValue $right): ?bool {

		// This type doesn't know how to compare against anything.
		return null;

	}

}

class TypeBValue extends AbstractValue {

	public function __construct($value) {
		$this->value = $value;
	}

	public function getType(): TypeValue {
		global $customTypeB;
		return $customTypeB;
	}

	public function getStringRepr(): string {
		return 'B';
	}

	function isEqualTo(AbstractValue $right): ?bool {
		return $this->value === $right->value;
	}

}

// CustomTypeValueA vs CustomTypeValueA, which do not know how to compare.
Assert::false(ComparisonLTR::evaluate('==', new CustomTypeValueA(''), new CustomTypeValueA('')));
Assert::true(ComparisonLTR::evaluate('!=', new CustomTypeValueA(''), new CustomTypeValueA('')));

// CustomTypeValueA::compare() will be null, TypeBValue::compare() will be used instead.
Assert::true(ComparisonLTR::evaluate('==', new CustomTypeValueA('y'), new TypeBValue('y')));
Assert::false(ComparisonLTR::evaluate('==', new CustomTypeValueA('y'), new TypeBValue('x')));
Assert::true(ComparisonLTR::evaluate('==', new CustomTypeValueA('x'), new TypeBValue('x')));
Assert::true(ComparisonLTR::evaluate('==', new CustomTypeValueA('x'), new TypeBValue('x')));

Assert::false(ComparisonLTR::evaluate('!=', new CustomTypeValueA('y'), new TypeBValue('y')));
Assert::true(ComparisonLTR::evaluate('!=', new CustomTypeValueA('y'), new TypeBValue('x')));
Assert::false(ComparisonLTR::evaluate('!=', new CustomTypeValueA('x'), new TypeBValue('x')));
Assert::false(ComparisonLTR::evaluate('!=', new CustomTypeValueA('x'), new TypeBValue('x')));
