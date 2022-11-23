<?php

use \Tester\Assert;

use \Smuuf\Primi\Config;
use \Smuuf\Primi\Ex\BaseException;
use \Smuuf\Primi\Code\SourceFile;

require __DIR__ . '/../../bootstrap.php';

echo "█ Integration tests 'typing'\n";

Assert::noError(function() {

	$cases = glob(__DIR__ . '/cases/*.primi');

	foreach ($cases as $file) {
		$source = new SourceFile($file);
		$interpreter = new \Smuuf\Primi\Interpreter;
		$interpreter->run($source);
	}

});
