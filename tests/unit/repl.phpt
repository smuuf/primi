<?php

use \Tester\Assert;

require __DIR__ . '/../bootstrap.php';

putenv('NO_COLOR=1');

$counter = 0;
$commands = [
    'a',
    'a = 1',
    'b',
	'a',
	'?',
	"(a, b) => { return a + b; }",
	'??',
	'exit',
];

// This is expected output. This will be compared with actual output down below.
// Asterisk * means that the line can be whatever.
$expected = [
	'*', // REPL info
	'*', // REPL info
	'*', // REPL info
	"ERR: Undefined variable 'a' @ line 1, position 0",
	"1",
	"ERR: Undefined variable 'b' @ line 1, position 0",
	"1",
	"a: 1",
	"_: 1",
	"<function: user>",
	'...', // ... - skip checking the rest.
];

$driver = new class implements \Smuuf\Primi\ICliIoDriver {

	public $lines = [];
	public $buffer = '';

	public function input(string $prompt): string {

		global $commands, $counter;

		// Each call to this method will return the next item in the
		// global $commands array. We're simulating user input.
		return $commands[$counter++];

	}

	public function output(string $text): void {

		// The mechanism down below ensures that any output will be correcly
		// divided into separate lines.
		$this->buffer .= $text;

		if (strpos($this->buffer, "\n") !== false) {
			$lines = explode("\n", trim($this->buffer));
			while (($line = array_shift($lines)) !== null) {
				if (trim($line) === '') {
					continue;
				}
				$this->lines[] = $line;
			}
			$this->buffer = '';
		}

	}

	public function addToHistory(string $item): void {}
	public function loadHistory(string $path): void {}
	public function storeHistory(string $path): void {}

};

$context = new \Smuuf\Primi\Context;
$interpreter = new \Smuuf\Primi\Interpreter($context);
$repl = new \Smuuf\Primi\Repl($interpreter, $driver, true);

// Run prepared commands and catch whole output.
$repl->start();
$output = $driver->lines;

foreach ($expected as $index => $string) {

	// If there's * in the expected array, take whatever output was on that
	// line - skip this line.
	if ($string === '*') {
		continue;
	}

	// On first encounter of ... in the expected array, skip the rest.
	if ($string === '...') {
		break;
	}

	$line = $index + 1;
	Assert::same(
		trim($expected[$index]),
		trim($output[$index]),
		"Output line $line match"
	);

}
