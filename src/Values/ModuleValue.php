<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Ex\LookupError;
use \Smuuf\Primi\Scopes\AbstractScope;

/**
 * @property AbstractScope $value Global scope of the module.
 */
class ModuleValue extends AbstractValue {

	const TYPE = "module";

	/** @var string Name/identifier of the module (probably file path). */
	protected string $name;

	public function __construct(string $name, AbstractScope $scope) {
		$this->name = $name;
		$this->value = $scope;
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

	public function attrGet(StringValue $key): ?AbstractValue {

		$variableName = $key->getStringValue();

		// If the variable is not found, we'll return null instead of value
		// and AttrAccess handler will throw "unknown attribute" error.
		$value = $this->value->getVariable($variableName);
		if ($value === null) {
			throw new LookupError("Unknown attribute '$variableName'");
		}

		return $value;

	}

	public function attrSet(StringValue $key, AbstractValue $value): bool {

		$this->value->setVariable($key->getStringValue(), $value);
		return \true;

	}

	/**
	 * The 'in' operator for dictionaries looks for keys and not values.
	 */
	public function doesContain(AbstractValue $right): ?bool {
		return (bool) $this->value->getVariable($right);
	}

}
