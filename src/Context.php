<?php

declare(strict_types=1);

namespace Smuuf\Primi;

use \Smuuf\Primi\Ex\EngineError;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Ex\EngineInternalError;
use \Smuuf\Primi\Tasks\TaskQueue;
use \Smuuf\Primi\Scopes\Scope;
use \Smuuf\Primi\Scopes\AbstractScope;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Helpers\Traits\StrictObject;

class Context {

	use StrictObject;

	/**
	 * Value of static property self::$callStackLimit fixed when
	 * initializing new instance of Context (to ignore later modifications).
	 *
	 * @var int
	 */
	private $maxCallStackSize;

	/** @var string[] Call stack list. */
	private $callStack = [];

	/** @var AbstractScope[] Scope stack list. */
	private $scopeStack = [];

	/**
	 * Direct reference to the scope on the top of the stack.
	 * @var AbstractScope
	 */
	private $currentScope = \null;

	/** @var TaskQueue Task queue for this context. */
	private $taskQueue;

	public function __construct(?AbstractScope $scope = \null) {

		// Render the config value into instance property to ensure changing
		// of the config doesn't change behavior of existing contexts.
		$this->maxCallStackSize = Config::getCallStackLimit();

		$this->taskQueue = new TaskQueue($this);
		$this->pushScope($scope ?? new Scope);

	}

	public function getTaskQueue(): TaskQueue {
		return $this->taskQueue;
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

		if (
			$this->maxCallStackSize
			&& count($this->callStack) === $this->maxCallStackSize
		) {

			throw new RuntimeError(sprintf(
				"Maximum call stack size (%d) reached",
				$this->maxCallStackSize
			));

		}

	}

	public function popCall(): void {

		\array_pop($this->callStack);
		$this->taskQueue->tick();

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

		// At least one scope needs to be present at all times.
		if (\count($this->scopeStack) === 1) {
			throw new EngineInternalError("Cannot pop last scope");
		}

		\array_pop($this->scopeStack);
		$this->currentScope = \end($this->scopeStack);

	}

	// Direct access to the current scope - which is the one on the top of the
	// stack (compatibility with Primi <0.5).

	public function getVariable(string $name): ?AbstractValue {
		return $this->currentScope->getVariable($name);
	}

	public function getVariables(bool $includeParents = \false): array {
		return $this->currentScope->getVariables($includeParents);
	}

	public function setVariable(string $name, AbstractValue $value) {
		$this->currentScope->setVariable($name, $value);
	}

	public function setVariables(array $pairs) {
		$this->currentScope->setVariables($pairs);
	}

}
