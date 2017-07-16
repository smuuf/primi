<?php

namespace Smuuf\Primi;

interface ISupportsComparison {

	public function doComparison(string $operator, ISupportsComparison $operand);

}
