<?php

use \Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$counter = 0;
$commands = [
    'a',
    'a = 1',
    'b',
	'a',
	'?',
    'exit',
];

// This is expected output. This will be compared with actual output down below.
$expected = [
	"ERR: Undefined variable 'a' @ line 1, position 0, code: a",
	"1",
	"ERR: Undefined variable 'b' @ line 1, position 0, code: b",
	"1",
	"a: 1",
];

$driver = new class implements \Smuuf\Primi\IReadlineDriver {

	public function readline(string $prompt): string {

        global $commands, $counter;

		// Each call to this method will return the next item in the
		// global $commands array. We're simulating user input.
		return $commands[$counter++];

	}

	public function readlineAddHistory(string $item): void {

	}

	public function readlineReadHistory(string $path): void {

	}

	public function readlineWriteHistory(string $path): void {

	}

};

$context = new \Smuuf\Primi\Context;
$interpreter = new \Smuuf\Primi\Interpreter($context);
$repl = new \Smuuf\Primi\Repl($interpreter, $driver, true);

// Run prepared commands and catch whole output.
ob_start();
$repl->start();
$buffer = ob_get_clean();

Assert::same(
	normalize_array($expected),
	normalize_string($buffer)
);

function normalize_array(array $lines) {
	return implode(' ', array_map('normalize_string', $lines));
}

function normalize_string(string $string) {
	return trim(preg_replace('~\s+~', " ", $string));
}
