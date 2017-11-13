<?php

require __DIR__ . "/../../vendor/autoload.php";
$bench = new \Smuuf\Phpcb\PhpBenchmark;

class FirstParentClass {}
class SecondParentClass {}
class FirstChildClass extends FirstParentClass {}
class SecondChildClass extends SecondParentClass {}

function check_without_cache($className) {
	return !is_subclass_of($className, \FirstParentClass::class);
}

function check_with_cache($className) {
	static $cache = [];
	return $cache[$className] ?? ($cache[$className] = !is_subclass_of($className, \FirstParentClass::class));
}

$bench->addBench(function() {
	$x = check_without_cache('\\FirstChildClass');
	$y = check_without_cache('\\SecondChildClass'); // Intentionally check child of SecondParentClass
});

$bench->addBench(function() {
	$x = check_with_cache('\\FirstChildClass');
	$y = check_with_cache('\\SecondChildClass');
});

$bench->run(); 