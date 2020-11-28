<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

class UnhashableTypeException extends EngineException {

	/** @var string Type name. */
	protected $type;

	public function __construct(string $type) {
		$this->type = $type;
		parent::__construct("Unhashable type '$type'");
	}

	public function getType(): string {
		return $this->type;
	}

}
