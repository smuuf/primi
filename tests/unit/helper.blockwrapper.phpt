<?php

use \Tester\Assert;

use \Smuuf\Primi\Helpers\Wrappers\AbstractWrapper;
use \Smuuf\Primi\Helpers\Traits\StrictObject;

require __DIR__ . '/../bootstrap.php';

$gatherer = [];

class YayWrapper extends AbstractWrapper {

	use StrictObject;

	private $arg;

	public function __construct($arg) {
		$this->arg = $arg;
	}

	public function executeBefore() {
		global $gatherer;
		$gatherer[] = "enter {$this->arg}";
	}

	public function executeAfter() {
		global $gatherer;
		$gatherer[] = "exit {$this->arg}";
	}

}

$wrapper = new YayWrapper('whatever_1');
$wrapper->wrap(function() {
	global $gatherer;
	$gatherer[] = "something 1";
});

$wrapper = new YayWrapper('whatever_2');
$wrapper->wrap(function() {
	global $gatherer;
	$gatherer[] = "something 2";
});

$expected = [
	'enter whatever_1',
	'something 1',
	'exit whatever_1',
	'enter whatever_2',
	'something 2',
	'exit whatever_2',
];

Assert::same($expected, $gatherer);
