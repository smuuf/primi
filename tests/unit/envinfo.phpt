<?php

use \Tester\Assert;

use \Smuuf\Primi\EnvInfo;

require __DIR__ . '/../bootstrap.php';

Assert::noError(fn() => EnvInfo::getPrimiBuild());
Assert::type('string', EnvInfo::getPrimiBuild());

Assert::noError(fn() => EnvInfo::isRunningInPhar());
Assert::type('bool', EnvInfo::isRunningInPhar());

Assert::noError(fn() => EnvInfo::getHomeDir());
$x = EnvInfo::getHomeDir();
Assert::true(is_string($x) || is_null($x));

Assert::noError(fn() => EnvInfo::getCurrentUser());
Assert::type('string', EnvInfo::getHomeDir());

Assert::noError(fn() => EnvInfo::getBestTempDir());
$x = EnvInfo::getBestTempDir();
Assert::true(is_string($x) || is_null($x));
