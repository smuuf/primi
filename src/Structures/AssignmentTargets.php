<?php

declare(strict_types=1);

namespace Smuuf\Primi\Structures;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\EngineError;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Values\AbstractValue;

/**
 * @internal
 */
class AssignmentTargets {

	use StrictObject;

	/** @var string[] List of target variable names. */
	private array $targets;

	/**
	 * If true, there is only a single target and assigned value does not need
	 * unpacking.
	 */
	private ?string $singleTarget;

	/** Total number of targets. */
	private int $targetsCount;

	/**
	 * @param array<int, string> $targets
	 */
	public function __construct(array $targets = []) {

		if (!\array_is_list($targets)) {
			throw new EngineError(
				"Assignment targets must be specified as a list array");
		}

		$this->targets = $targets;
		$this->targetsCount = \count($targets);
		$this->singleTarget = $this->targetsCount === 1
			? $targets[0]
			: \null;

	}

	/**
	 * @param AbstractValue $value
	 * @return void
	 */
	public function assign(AbstractValue $value, Context $ctx) {

		if ($this->singleTarget !== \null) {
			$ctx->setVariable($this->singleTarget, $value);
			return;
		}

		$iter = $value->getIterator();
		if ($iter === \null) {
			throw new RuntimeError("Cannot unpack non-iterable");
		}

		$buffer = [];
		$targetIndex = 0;
		foreach ($iter as $value) {

			if (\array_key_exists($targetIndex, $this->targets)) {
				$buffer[$this->targets[$targetIndex]] = $value;
			} else {
				throw new RuntimeError(
					"Too many values to unpack (expected {$this->targetsCount})");
			}

			$targetIndex++;

		}

		if ($targetIndex < $this->targetsCount) {
			throw new RuntimeError("Not enough values to unpack (expected {$this->targetsCount} but got {$targetIndex})");
		}

		$ctx->setVariables($buffer);

	}

}
