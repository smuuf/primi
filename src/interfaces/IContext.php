<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\Structures\Value;

interface IContext {

	public function setVariable(string $name, Value $value, bool $global = false);
	public function setVariables(array $pairs, bool $global = false);
	public function getVariable(string $name): Value;
	public function getVariables(): array;

}
