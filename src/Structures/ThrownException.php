<?php

declare(strict_types=1);

namespace Smuuf\Primi\Structures;

use Smuuf\Primi\Values\ExceptionValue;
use Smuuf\StrictObject;

class ThrownException {

	use StrictObject;

	public function __construct(
		public readonly ExceptionValue $exception,
		public ?Traceback $tb = \null,
	) {}

	public function setTraceback(Traceback $tb): void {
		$this->tb = $tb;
	}

	public function getTraceback(): ?Traceback {
		return $this->tb;
	}

}
