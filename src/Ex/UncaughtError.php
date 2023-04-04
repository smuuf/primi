<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

use Smuuf\Primi\Structures\ThrownException;

class UncaughtError extends EngineException {

	public function __construct(
		public readonly ThrownException $thrownException,
	) {
		parent::__construct($this->thrownException->exception->getStringRepr());
	}

}
