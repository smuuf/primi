<?php

use \Tester\Assert;

use \Smuuf\Primi\Code\SourceFile;
use \Smuuf\Primi\Config;
use \Smuuf\Primi\Logger;

require __DIR__ . '/../../bootstrap.php';

const TEST_LIST = [
	__DIR__ . '/project_1/main.primi',
	__DIR__ . '/project_2/entrypoint.primi',
	__DIR__ . '/project_3/start.primi',
];

Logger::enable();

$config = Config::buildDefault();
$config->addImportPath(__DIR__ . '/libs');

foreach (TEST_LIST as $testPath) {

	echo "█ Integration tests 'importing': $testPath\n";

	Assert::noError(function() use ($config, $testPath) {

		$interpreter = new \Smuuf\Primi\Interpreter($config);
		$source = new SourceFile($testPath);
		$interpreter->run($source);

	} );

}
