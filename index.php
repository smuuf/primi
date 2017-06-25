<?php

// Composer's autoload.
require __DIR__ . "/vendor/autoload.php";

// Autoloader.
$loader = new \Smuuf\Koloader\Autoloader(__DIR__ . "/temp/");
$loader->addDirectory(__DIR__ . "/src")->register();

$source = file_get_contents($argv[1]);

// Context.
$context = new \Smuuf\Primi\Context;

// Interpreter
$interpreter = new \Smuuf\Primi\Interpreter($context, __DIR__ . "/temp/");

try {

	// Get syntax tree.
	//$tree = $interpreter->getSyntaxTree($source);
	//print_r($tree);

	// Run interpreter
	$interpreter->run($source);
	//var_dump($context->getVariables());

} catch (\Smuuf\Primi\ErrorException $e) {

	echo $e->getMessage() . "\n";

}
