<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use Smuuf\Primi\Scope;
use Smuuf\Primi\Stdlib\StaticTypes;

/**
 * @property Scope $value Global scope of the module.
 * @method Scope getCoreValue()
 */
class ModuleValue extends AbstractBuiltinValue {

	public const TYPE = "module";

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

	public function getType(): TypeValue {
		return StaticTypes::getModuleType();
	}

	/**
	 * Get full name of the module.
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * Get package name of the module.
	 */
	public function getPackage(): string {
		return $this->package;
	}

	public function getStringRepr(): string {
		return "<module: {$this->name}>";
	}

	/**
	 * Return number of variables in the module's scope.
	 */
	public function getLength(): ?int {
		return \count($this->value->getVariables());
	}

	/**
	 * Returns true if there are any variables present in the module's scope.
	 */
	public function isTruthy(): bool {
		return (bool) $this->value->getVariables();
	}

	/**
	 * Accessing module attributes equals to accessing variables from module's
	 * scope.
	 */
	public function attrGet(string $key): ?AbstractValue {
		return $this->value->getVariable($key);
	}

	/**
	 * Setting module attributes equals to setting variables into module's
	 * scope.
	 */
	public function attrSet(string $name, AbstractValue $value): bool {
		$this->value->setVariable($name, $value);
		return \true;
	}

	/**
	 * The 'in' operator for dictionaries looks for keys and not values.
	 */
	public function doesContain(AbstractValue $key): ?bool {
		return (bool) $this->value->getVariable($key->getStringValue());
	}

	public function dirItems(): ?array {
		return \array_keys($this->value->getVariables());
	}

}
