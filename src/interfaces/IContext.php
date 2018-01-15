<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Structures\FuncValue;

interface IContext {

	public function setVariable(string $name, Value $value);
	public function setVariables(array $pairs);
	public function getVariable(string $name): Value;
	public function getVariables(): array;

}
