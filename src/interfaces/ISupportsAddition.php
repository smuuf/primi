<?php

namespace Smuuf\Primi;

interface ISupportsAddition {

	public function doAddition(string $operator, ISupportsAddition $operand);

}
