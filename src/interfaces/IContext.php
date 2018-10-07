<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Structures\FuncValue;

interface IContext {

	public function setVariable(string $name, Value $value, bool $internal = false);
	public function setVariables(array $pairs, bool $internal = false);
	public function getVariable(string $name): Value;
	public function getVariables(): array;

}
