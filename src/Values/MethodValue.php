<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Helpers\Stats;
use \Smuuf\Primi\Structures\FnContainer;

class MethodValue extends FuncValue {

	const TYPE = "method";

	public function __construct(FnContainer $fn) {
		Stats::add('values_method');
		$this->value = $fn;
	}

	public function getStringRepr(): string {
		return \sprintf(
			"<method: %s>",
			$this->value->isPhpFunction() ? 'native' : 'user',
		);
	}

}
