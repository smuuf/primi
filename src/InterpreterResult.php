<?php

declare(strict_types=1);

namespace Smuuf\Primi;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Scope;

/**
 * Object wrapping a single interpreter execution result.
 */
class InterpreterResult {

	use StrictObject;

	public function __construct(
		private Scope $scope,
		private Context $context,
	) {}

	public function getScope(): Scope {
		return $this->scope;
	}

	public function getContext(): Context {
		return $this->context;
	}

}
