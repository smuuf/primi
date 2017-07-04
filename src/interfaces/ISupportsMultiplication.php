<?php

namespace Smuuf\Primi;

interface ISupportsMultiplication {

	public function doMultiplication(string $operator, ISupportsMultiplication $operand);

}
