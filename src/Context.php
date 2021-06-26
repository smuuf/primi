<?php

declare(strict_types=1);

namespace Smuuf\Primi;

use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Ex\EngineInternalError;
use \Smuuf\Primi\Tasks\TaskQueue;
use \Smuuf\Primi\Scopes\Scope;
use \Smuuf\Primi\Scopes\AbstractScope;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Helpers\Traits\StrictObject;
use \Smuuf\Primi\Modules\Importer;
use \Smuuf\StrictObject;

class Context {

	use StrictObject;

	//
	// Call stack.
	//

	/**
	 * Value of static property self::$callStackLimit fixed when
	 * initializing new instance of Context (to ignore later modifications).
	 */
	private int $maxCallStackSize;

	/** @var CallFrame[] Call stack list. */
	private array $callStack = [];

	//
	// Scope stack.
	//

	/** @var AbstractScope[] Scope stack list. */
	private array $scopeStack = [];

	/** Direct reference to the scope on the top of the stack. */
	private AbstractScope $currentScope;

	//
	// Insides.
	//

	/** Task queue for this context. */
	private TaskQueue $taskQueue;

	/** Importer instance */
	private Importer $importer;

	/** ExtensionHub instance. */
	private ExtensionHub $extensionHub;

	public function __construct(
		?AbstractScope $globalScope = \null,
		?ExtensionHub $extHub = \null
	) {

		$this->extensionHub = $extHub ?? new ExtensionHub;

		// Render the config value into instance property to ensure changing
		// of the config doesn't change behavior of existing contexts.
		$this->maxCallStackSize = Config::getCallStackLimit();

		$this->taskQueue = new TaskQueue($this);
		$this->importer = new Importer($this);

		if ($globalScope) {
			$this->extensionHub->apply($globalScope);
		} else {
			$globalScope = $this->buildNewGlobalScope();
		}

		$this->pushScope($globalScope);

	}

	// "Global scope" factory.

	public function buildNewGlobalScope(): AbstractScope {

		$scope = new Scope;
		$this->extensionHub->apply($scope);

		return $scope;

	}

	// Task queue management.

	public function getTaskQueue(): TaskQueue {
		return $this->taskQueue;
	}

	// Importer management.

	public function getImporter(): Importer {
		return $this->importer;
	}

	public function getCurrentModule(): string {
		return end($this->callStack)->getName();
	}

	// Call stack management.

	public function getCallStack(): array {
		return $this->callStack;
	}

	public function getTraceback(): array {
		return $this->callStack;
	}

	public function pushCall(CallFrame $call): void {

		$this->callStack[] = $call;

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
