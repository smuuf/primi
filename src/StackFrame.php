<?php

declare(strict_types=1);

namespace Smuuf\Primi;

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
	private ModuleValue $module;

	/** Callsite location. */
	private ?Location $location;

	public function __construct(
		string $name,
		ModuleValue $module,
		?Location $location = \null
	) {
		$this->name = $name;
		$this->module = $module;
		$this->location = $location;
	}

	public function __toString(): string {
		$loc = $this->location ? " called from {$this->location}" : '';
		return "{$this->name} in {$this->module->getStringRepr()}{$loc}";
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
