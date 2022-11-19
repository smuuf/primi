<?php

use \Smuuf\Primi\Drivers\ReplIoDriverInterface;
use \Tester\Assert;

use \Smuuf\Primi\MagicStrings;

require __DIR__ . '/../bootstrap.php';

// So that output is not messed up by color codes and we can test it easily.
putenv('NO_COLOR=1');

$counter = 0;
$commands = [
    'a',
    'a = 1',
    'print(end: ""); b',
	'a',
	'?',
	"(a, b) => { return a + b; }",
	'?tb',
	'??',
	'exit',
];

$mainModuleName = MagicStrings::MODULE_MAIN_NAME;

// This is expected output. This will be compared with actual output down below.
// Asterisk * means that the line can be whatever.
$expected = [
	"Error: Undefined variable 'a' @ <module: $mainModuleName> on line 1 at position 0",
	"Traceback:",
	"[0] <repl: cli> in <module: $mainModuleName>",
	"1",
	"Error: Undefined variable 'b' @ <module: $mainModuleName> on line 1 at position 16",
	"Traceback:",
	"[0] <repl: cli> in <module: $mainModuleName>",
	"1",
	"a: 1",
	"_: 1",
	"<function: $mainModuleName.<anonymous>()>",
	"Traceback:",
	"[0] <repl: cli> in <module: $mainModuleName>",
	'...', // ... - skip checking the rest.
];

$driver = new class implements ReplIoDriverInterface {

	public string $output = '';

	public function input(string $prompt): string {

		global $commands, $counter;

		// Each call to this method will return the next item in the
		// global $commands array. We're simulating user input.
		return $commands[$counter++];

	}

	public function stdout(string ...$text): void {
		$this->output .= implode('', $text);
	}

	public function stderr(string ...$text): void {
		$this->stdout(...$text);
	}

	public function addToHistory(string $item): void {}
	public function loadHistory(string $path): void {}
	public function storeHistory(string $path): void {}

};

$repl = new \Smuuf\Primi\Repl(null, $driver);
$repl::$noExtras = true;

// Run prepared commands and catch whole output.
$repl->start();
$allLines = explode("\n", $driver->output);
$nonempty = array_filter($allLines, fn($l) => trim($l) !== '');
$output = array_values($nonempty);

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
