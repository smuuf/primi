<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Scope;

/**
 * @property Scope|null $value Global scope of the module or null, if
 * the module has no global scope (which is handy for "anonymous" modules that
 * wrap Primi functions that are wrappers for native PHP functions which might
 * not be placed in any module).
 */
class ModuleValue extends AbstractValue {

	protected const TYPE = "Module";

	/** Name of the module */
	protected string $name;

	/** Package name of the module. */
	protected string $package;

	public function __construct(
		string $name,
		string $package = '',
		?Scope $scope = \null
	) {

		$this->name = $name;
		$this->package = $package;
		$this->value = $scope;

	}

	public function getName(): string {
		return $this->name;
	}

	public function getPackage(): string {
		return $this->package;
	}

	public function getStringRepr(): string {
		return "<module: {$this->name}>";
	}

	public function getLength(): ?int {
		return count($this->value->getVariables());
	}

	public function isTruthy(): bool {
		return (bool) $this->value->getVariables();
	}

	/**
	 * Accessing module attributes equals to accessing variables from module's
	 * scope.
	 */
	public function attrGet(string $key): ?AbstractValue {

		// If the variable is not found, we'll return null instead of value
		// and AttrAccess handler will throw "unknown attribute" error.
		return $this->value->getVariable($key);

	}

	/**
	 * Setting module attributes equals to setting variables into module's
	 * scope.
	 */
	public function attrSet(string $key, AbstractValue $value): bool {
		$this->value->setVariable($key, $value);
		return \true;
	}

	/**
	 * The 'in' operator for dictionaries looks for keys and not values.
	 */
	public function doesContain(AbstractValue $key): ?bool {
		return (bool) $this->value->getVariable($key->getStringValue());
	}

}
