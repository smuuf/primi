<?php

use \Tester\Assert;

use \Smuuf\Primi\Code\SourceFile;
use \Smuuf\Primi\Config;

require __DIR__ . '/../../bootstrap.php';

const TEST_LIST = [
	__DIR__ . '/project_1/main.primi',
	__DIR__ . '/project_2/entrypoint.primi',
	__DIR__ . '/project_3/start.primi',
];

foreach (TEST_LIST as $testPath) {

	echo "█ Integration tests 'importing': $testPath\n";

	Assert::noError(function() use ($testPath) {

		$source = new SourceFile($testPath);

		$config = Config::buildDefault();
		$config->addImportPath(__DIR__ . '/libs');
		$config->addImportPath($source->getDirectory());

		$interpreter = new \Smuuf\Primi\Interpreter($config);
		$interpreter->run($source);

	} );

}
