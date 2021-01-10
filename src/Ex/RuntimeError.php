<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

class RuntimeError extends ErrorException {

	public function __construct(string $msg) {
		$this->message = $msg;
	}

}
