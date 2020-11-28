<?php

use \Tester\Assert;

use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Values\StringValue;
use \Smuuf\Primi\Values\AbstractValue;

require __DIR__ . '/../bootstrap.php';

function get_val(AbstractValue $v) {
	return $v->getInternalValue();
}

$s = new StringValue("hello there");

//
// Argument count error message parser.
//

try {
    (function($a, $b, $c) {
        echo "yay";
    })(1, 2);
} catch (\ArgumentCountError $e) {
    [$x, $y] = Func::parse_argument_count_error($e);
    Assert::same(2, (int) $x);
    Assert::same(3, (int) $y);
}

try {
    (function($a, $b) {
        echo "yay";
    })();
} catch (\ArgumentCountError $e) {
    [$x, $y] = Func::parse_argument_count_error($e);
    Assert::same(0, (int) $x);
    Assert::same(2, (int) $y);
}

try {
    (function($a, $b, $c) {
        echo "yay";
    })(1);
} catch (\ArgumentCountError $e) {
    [$x, $y] = Func::parse_argument_count_error($e);
    Assert::same(1, (int) $x);
    Assert::same(3, (int) $y);
}
