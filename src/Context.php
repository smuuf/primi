<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\StrictObject;
use \Smuuf\Primi\Ex\EngineInternalError;
use \Smuuf\Primi\Structures\Value;

class Context extends StrictObject {

	/** @var string[] Call stack list. */
	protected $callStack = [];

	/** @var AbstractScope[] Scope stack list. */
	protected $scopeStack = [];

	/** @var string[] Event queue. */
	protected $eventQueue = [];

	/**
	 * Direct reference to the scope on the top of the stack.
	 * @var AbstractScope
	 */
	protected $currentScope = \null;

	public function __construct(?AbstractScope $scope = \null) {
		$this->pushScope($scope ?? new Scope);
	}

	// Events.

	public function addEvent(string $name): void {
		$this->eventQueue[] = $name;
	}

	public function getEvent(): ?string {
		return array_pop($this->eventQueue);
	}

	// Callstack management.

	public function getCallStack(): array {
		return $this->callStack;
	}

	public function getTraceback(): array {
		return $this->callStack;
	}

	public function pushCall(string $callId): void {
		$this->callStack[] = $callId;
	}

	public function popCall(): void {
		\array_pop($this->callStack);
	}

	// Scope management.

	public function getCurrentScope(): AbstractScope {
		return $this->currentScope;
	}

	public function pushScope(AbstractScope $scope): void {
		$this->scopeStack[] = $scope;
		$this->currentScope = $scope;
	}

	public function popScope(): void {

		if (\count($this->scopeStack) === 1) {
			// At least one scope needs to be present at all times.
			throw new EngineInternalError("Cannot pop last scope");
		}

		\array_pop($this->scopeStack);
		$this->currentScope = \end($this->scopeStack);

	}

	// Direct access to the current scope - which is the one on the top of the
	// stack (compatibility with Primi 0.4).

	public function getVariable(string $name): ?Value {
		return $this->currentScope->getVariable($name);
	}

	public function getVariables(bool $includeParents = \false): array {
		return $this->currentScope->getVariables($includeParents);
	}

	public function setVariable(string $name, Value $value) {
		$this->currentScope->setVariable($name, $value);
	}

	public function setVariables(array $pairs) {
		$this->currentScope->setVariables($pairs);
	}

}
