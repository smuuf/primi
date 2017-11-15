<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Structures\Func;

interface IContext {

	public function setVariable(string $name, Value $value);
	public function setVariables(array $pairs);
	public function getVariable(string $name): Value;
	public function getVariables(): array;

	public function setFunction(string $name, Func $function);
	public function setFunctions(array $pairs);
	public function getFunction(string $name): Func;
	public function getFunctions(): array;

}
