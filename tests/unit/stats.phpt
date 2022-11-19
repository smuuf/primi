<?php

use \Tester\Assert;

use \Smuuf\Primi\Helpers\Stats;

require __DIR__ . '/../bootstrap.php';

//
// Stats gatherer.
//

Stats::add('prefix_a_something');

Assert::falsey(
	Stats::get('prefix_a_something'),
	"AbstractValue for 'prefix_a_something' is missing - because it was not incremented - because of disabled stats gathering"
);
Assert::falsey(
	Stats::get('prefix_a_nonexistent'),
	"AbstractValue for 'prefix_a_nonexistent' is missing because of disabled stats gathering"
);

Stats::add('prefix_a_something'); // Create stats entry and increment its counter by 1.
Stats::add('prefix_a_something'); // Increment existing stats entry by 1.
Stats::add('prefix_b_something'); // Create a new stats entry and increment its counter by 1.

Assert::same(2, Stats::get('prefix_a_something'), "Getting value for a get stats entry");
Assert::same(1, Stats::get('prefix_b_something'), "Getting value for a get stats entry");
