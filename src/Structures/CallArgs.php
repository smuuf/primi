<?php

declare(strict_types=1);

namespace Smuuf\Primi\Structures;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Ex\EngineError;
use \Smuuf\Primi\Values\AbstractValue;

/**
 * Container for passing arguments - positional and keyword arguments - to the
 * function-invoking mechanism.
 *
 * @internal
 */
class CallArgs {

	use StrictObject;

	/** @var AbstractValue[] Positional arguments. */
	private $args = [];

	/** @var AbstractValue[] Keyword argument. */
	private $kwargs = [];

	/** Total number of positional and keyword arguments combined. */
	private int $count;

	public function __construct(array $args = [], array $kwargs = []) {

		if (!array_is_list($args)) {
			throw new EngineError(
				"Positional arguments must be specified as a list array");
		}

		$this->args = $args;
		$this->kwargs = $kwargs;
		$this->count = \count($args) + \count($kwargs);

	}

	/**
	 * @return AbstractValue[]
	 */
	public function getArgs(): array {
		return $this->args;
	}

	/**
	 * @return AbstractValue[]
	 */
	public function getKwargs(): array {
		return $this->kwargs;
	}

	/**
	 * Returns total number of positional and keyword arguments combined.
	 */
	public function getCount(): int {
		return $this->count;
	}

	/**
	 * Return new `CallArgs` object with original args and kwargs being
	 * added (positional args) or overwritten (keyword args) by the args
	 * from the "extra" `CallArgs` object passed as the argument.
	 */
	public function withExtra(CallArgs $more): self {
		return new self(
			[...$this->args, ...$more->getArgs()],
			array_merge($this->kwargs, $more->getKwargs())
		);
	}

}
