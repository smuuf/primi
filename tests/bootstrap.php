<?php

require __DIR__ . '/../vendor/autoload.php';

// Autoloader.
$loader = new \Smuuf\Koloader\Autoloader(__DIR__ . "/../temp/");
$loader->addDirectory(__DIR__ . "/../src")->register();

\Smuuf\Primi\ExtensionHub::add([
	\Smuuf\Primi\Psl\StandardExtension::class,
	\Smuuf\Primi\Psl\StringExtension::class,
	\Smuuf\Primi\Psl\BoolExtension::class,
	\Smuuf\Primi\Psl\RegexExtension::class,
	\Smuuf\Primi\Psl\NumberExtension::class,
	\Smuuf\Primi\Psl\DictExtension::class,
	\Smuuf\Primi\Psl\ListExtension::class,
	\Smuuf\Primi\Psl\CastingExtension::class,
]);

\Tester\Environment::setup();
