<?php

require __DIR__ . '/../vendor/autoload.php';

// Autoloader.
$loader = new \Smuuf\Koloader\Autoloader(__DIR__ . "/../temp/");
$loader->addDirectory(__DIR__ . "/../src")->register();

\Smuuf\Primi\ExtensionHub::add([
	\Smuuf\Primi\Psl\GenericExtension::class,
	\Smuuf\Primi\Psl\StringExtension::class,
	\Smuuf\Primi\Psl\NumberExtension::class,
	\Smuuf\Primi\Psl\ArrayExtension::class,
]);

\Tester\Environment::setup();
