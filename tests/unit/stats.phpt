<?php

use \Tester\Assert;

use \Smuuf\Primi\Helpers\Stats;

require __DIR__ . '/../bootstrap.php';

//
// Stats gatherer.
//

Stats::add('some_A');
Assert::same(1, Stats::get('some_A'));
Assert::same(0, Stats::get('missing_X'));

Stats::add('some_A');
Stats::add('some_A');
Stats::add('some_B');

Assert::same(3, Stats::get('some_A'));
Assert::same(1, Stats::get('some_B'));
