<?php

use \Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$counter = 0;
$commands = [
    'a',
    'a = 1',
    'b',
    'a',
    'exit',
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
$repl = new \Smuuf\Primi\Repl($interpreter, $driver);

$i = new \Smuuf\Primi\Repl($interpreter, $driver);

// Run prepared commands and catch whole output.
ob_start();
$i->start();
$buffer = ob_get_clean();

// This is expected output. This will be compared with actual output down below.
$expected = "
ERR: Undefined variable 'a' @ code: a
1
ERR: Undefined variable 'b' @ code: b
1
";

Assert::same(trim($expected), trim($buffer));
