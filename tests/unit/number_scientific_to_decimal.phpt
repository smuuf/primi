<?php

use \Tester\Assert;

use \Smuuf\Primi\Helpers\Func;

require __DIR__ . '/../bootstrap.php';

//
// Parsing scientific notation (PHP might give it for small float numbers) into
// decimal numbers.
//

function scitodec(string $value): string {
	return Func::normalize_decimal(Func::scientific_to_decimal($value));
}

Assert::same("1123000", scitodec("1.123E+6"));
Assert::same("1999999.999", scitodec("1.999999999E+6"));
Assert::same("0.000001123", scitodec("1.123E-6"));
Assert::same("0.987654123", scitodec("987654.123E-6"));;
