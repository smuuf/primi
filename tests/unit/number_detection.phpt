<?php

use \Tester\Assert;

use \Smuuf\Primi\Helpers\Func;

require __DIR__ . '/../bootstrap.php';

//
// Numeric string detection.
//

Assert::true(Func::is_decimal("1"));
Assert::true(Func::is_decimal("1.2"));
Assert::true(Func::is_decimal("11234567891234567891368184384.2564684684381381318313847837"));
Assert::true(Func::is_decimal("0.0"));
Assert::true(Func::is_decimal("0.00000000000000000000000000000000000000000000000000000123"));
Assert::true(Func::is_decimal("-0.0"));
Assert::true(Func::is_decimal("-1.2"));
Assert::true(Func::is_decimal("-1"));
Assert::true(Func::is_decimal("+0.0"));

Assert::false(Func::is_decimal(""));
Assert::false(Func::is_decimal(" 1"));
Assert::false(Func::is_decimal(" 1.2"));
Assert::false(Func::is_decimal(" 1"));
Assert::false(Func::is_decimal(" 1.2"));
Assert::false(Func::is_decimal("hell no"));
Assert::false(Func::is_decimal("not 1"));
Assert::false(Func::is_decimal("1 owl"));
Assert::false(Func::is_decimal("2 2"));
Assert::false(Func::is_decimal("+-1"));

Assert::false(Func::is_decimal("1.0E+44"));
Assert::false(Func::is_decimal("-1.0E+44"));
Assert::false(Func::is_decimal("1.0E-44"));
Assert::false(Func::is_decimal("-1.0E-44"));
Assert::false(Func::is_decimal("19.0E+44"));
Assert::false(Func::is_decimal("-19.0E+44"));
Assert::false(Func::is_decimal("19.0E-44"));
Assert::false(Func::is_decimal("-19.0E-44"));
Assert::false(Func::is_decimal(".0E+44"));
Assert::false(Func::is_decimal("-.0E+44"));
Assert::false(Func::is_decimal("E+44"));
Assert::false(Func::is_decimal("-E+44"));
Assert::false(Func::is_decimal("14.0E+44 damn"));
Assert::false(Func::is_decimal("-14.0E+44damn"));
Assert::false(Func::is_decimal("damn14.0E-44"));
Assert::false(Func::is_decimal("damn -14.0E-44"));
Assert::false(Func::is_decimal("1.0E+44 🌭"));
Assert::false(Func::is_decimal("-1.0E+44🌭"));
Assert::false(Func::is_decimal("🌭1.0E-44"));
Assert::false(Func::is_decimal("🌭 -1.0E-44"));

Assert::true(Func::is_round_int("1.0E+44"));
Assert::true(Func::is_round_int("1.1E+44"));
Assert::true(Func::is_round_int("1.9E+44"));
Assert::true(Func::is_round_int("-1.2E+44"));
Assert::true(Func::is_round_int("1.0E+44"));
Assert::true(Func::is_round_int("-1.0E+44"));

if (PHP_VERSION_ID < 80000) {
	// Will be true in PHP 8.0
	// See https://wiki.php.net/rfc/saner-numeric-strings
	Assert::false(Func::is_round_int(" 1 "));
}

Assert::true(Func::is_round_int(" 1"));
Assert::false(Func::is_round_int(""));
Assert::false(Func::is_round_int(" "));
Assert::false(Func::is_round_int(" 1.1 "));
Assert::false(Func::is_round_int(" 0.1 "));
Assert::false(Func::is_round_int("1.0E-44"));
Assert::false(Func::is_round_int("1.1E-44"));
Assert::false(Func::is_round_int("1.9E-44"));
Assert::false(Func::is_round_int("-1.2E-44"));
Assert::false(Func::is_round_int("1.0E-44"));
Assert::false(Func::is_round_int("-1.0E-44"));

if (PHP_MAJOR_VERSION > 7) {
	// PHP 8 changed how \is_numeric() works.
	Assert::true(Func::is_round_int(" 1. "));
} else {
	Assert::false(Func::is_round_int(" 1. "));
}

//
// Passing floats instead of strings-
//

Assert::false(Func::is_decimal(1.0E+44));
Assert::false(Func::is_decimal(-1.0E+44));
Assert::false(Func::is_decimal(1.0E-44));
Assert::false(Func::is_decimal(-1.0E-44));

Assert::false(Func::is_decimal(19.0E+44));
Assert::false(Func::is_decimal(-19.0E+44));
Assert::false(Func::is_decimal(19.0E-44));
Assert::false(Func::is_decimal(-19.0E-44));

Assert::true(Func::is_round_int(1.0E+44));
Assert::true(Func::is_round_int(1.1E+44));
Assert::true(Func::is_round_int(1.9E+44));
Assert::true(Func::is_round_int(-1.2E+44));
Assert::true(Func::is_round_int(1.0E+44));
Assert::true(Func::is_round_int(-1.0E+44));

Assert::false(Func::is_round_int(1.0E-44));
Assert::false(Func::is_round_int(1.1E-44));
Assert::false(Func::is_round_int(1.9E-44));
Assert::false(Func::is_round_int(-1.2E-44));
Assert::false(Func::is_round_int(1.0E-44));
Assert::false(Func::is_round_int(-1.0E-44));
