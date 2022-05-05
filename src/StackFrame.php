<?php

declare(strict_types=1);

namespace Smuuf\Primi;

use \Smuuf\Primi\Helpers\Colors;
use \Smuuf\Primi\Values\ModuleValue;

use \Smuuf\StrictObject;

class StackFrame {

	use StrictObject;

	/** Name of the function call (often the name of the called function). */
	private string $name;

	/**
	 * Module the stack frame points to.
	 *
	 * This is also used for resolving relative imports performed within the
	 * stack frame.
	 */
	private ?ModuleValue $module;

	/** Callsite location. */
	private ?Location $location;

	public function __construct(
		string $name,
		?ModuleValue $module = \null,
		?Location $location = \null
	) {
		$this->name = $name;
		$this->module = $module;
		$this->location = $location;
	}

	public function asString(): string {

		$loc = $this->location
			? " called from {$this->location->asString()}"
			: '';

		$mod = $this->module
			? $this->module->getStringRepr()
			: '<unknown>';

		return "{$this->name} in {$mod}{$loc}";

	}

	/**
	 * Get name of the call.
	 */
	public function getCallName(): string {
		return $this->name;
	}

	public function getModule(): ModuleValue {
		return $this->module;
	}

	/**
	 * Get call location.
	 */
	public function getLocation(): ?Location {
		return $this->location;
	}

}
