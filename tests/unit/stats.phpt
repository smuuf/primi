<?php

use \Tester\Assert;

use \Smuuf\Primi\Helpers\Stats;

require __DIR__ . '/../bootstrap.php';

//
// Stats gatherer.
//

//
// Stats gathering is disabled by default.
//

Stats::add('prefix_a_something');
Assert::type('array', Stats::multi('prefix_a_'), 'Stats::multi() returns an array - even if empty');
Assert::falsey(
	Stats::multi('prefix_a_'),
	"AbstractValue for 'prefix_a_something' is missing - because it was not incremented - because of disabled stats gathering"
);
Assert::falsey(
	Stats::single('prefix_a_something'),
	"AbstractValue for 'prefix_a_something' is missing - because it was not incremented - because of disabled stats gathering"
);
Assert::falsey(
	Stats::single('prefix_a_nonexistent'),
	"AbstractValue for 'prefix_a_nonexistent' is missing because of disabled stats gathering"
);

//
// Enable gathering stats.
//

Stats::enable();
Stats::add('prefix_a_something'); // Create stats entry and increment its counter by 1.

Assert::type('array', Stats::multi('prefix_a_'), 'Stats::multi() returns an array.');
Assert::same(
	['something' => 1],
	Stats::multi('prefix_a_'),
	"AbstractValue for 'prefix_a_something' is present and has prefix trimmed off."
);

Stats::add('prefix_a_something'); // Increment existing stats entry by 1.
Stats::add('prefix_b_something'); // Create a new stats entry and increment its counter by 1.

Assert::same(
	['something' => 2],
	Stats::multi('prefix_a_'),
	"AbstractValue for 'prefix_a_something' is present and has correct value."
);
Assert::same(
	['something' => 1],
	Stats::multi('prefix_b_'),
	"AbstractValue for 'prefix_b_something' is present and has correct value."
);

Assert::same(2, Stats::single('prefix_a_something'), "Getting value for a single stats entry");
Assert::same(1, Stats::single('prefix_b_something'), "Getting value for a single stats entry");
