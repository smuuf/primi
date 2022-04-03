<?php

declare(strict_types=1);

namespace Smuuf\Primi;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Scope;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Code\AstProvider;
use \Smuuf\Primi\Ex\EngineInternalError;
use \Smuuf\Primi\Tasks\TaskQueue;
use \Smuuf\Primi\Values\ModuleValue;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Modules\Importer;
use \Smuuf\Primi\Structures\CallRetval;

class Context {

	use StrictObject;

	/** Runtime config bound to this context. */
	private Config $config;

	//
	// Call stack.
	//

	/** Configured call stack limit. */
	private int $maxCallStackSize;

	/** @var StackFrame[] Call stack list. */
	private $callStack = [];

	/**
	 * Current call stack size.
	 * Instead of count()ing $callStack property every time.
	 *
	 * @var int
	 */
	private $callStackSize = 0;

	//
	// Scope stack.
	//

	/** @var Scope[] Scope stack list. */
	private $scopeStack = [];

	/** Direct reference to the scope on the top of the stack. */
	private ?Scope $currentScope;

	//
	// Context services.
	//

	/** Task queue for this context. */
	private TaskQueue $taskQueue;

	/** Importer instance */
	private Importer $importer;

	/** AstProvider instance */
	private AstProvider $astProvider;

	//
	// Return value storage.
	//

	private ?CallRetval $retval = null;

	//
	// References to essential modules for fast and direct access.
	//

	/** Native 'std.__builtins__' module. */
	private Scope $builtins;

	/** Native 'std.types' module scope. */
	private Scope $typesModule;

	public function __construct(
		InterpreterServices $interpreterServices,
		?string $mainDirectory = null
	) {

		// Assign stuff to properties to avoid unnecessary indirections when
		// accessing them (optimization).
		$this->config = $interpreterServices->getConfig();
		$this->astProvider = $interpreterServices->getAstProvider();

		$services = new ContextServices(
			$this,
			$interpreterServices,
			$mainDirectory
		);

		$this->taskQueue = $services->getTaskQueue();
		$this->importer = $services->getImporter();
		$this->maxCallStackSize = $this->config->getCallStackLimit();

		// Import our builtins module.
		$this->builtins = $this->importer
			->getModule('std.__builtins__')
			->getInternalValue();

		// Import 'std.types' module for fast direct access.
		$this->typesModule = $this->importer
			->getModule('std.types')
			->getInternalValue();

	}

	// Access to runtime config.

	public function getConfig(): Config {
		return $this->config;
	}

	// Access to AST provider.

	public function getAstProvider(): AstProvider {
		return $this->astProvider;
	}

	// Task queue management.

	public function getTaskQueue(): TaskQueue {
		return $this->taskQueue;
	}

	// Import management.

	public function getImporter(): Importer {
		return $this->importer;
	}

	public function getCurrentModule(): ?ModuleValue {

		$currentFrame = \end($this->callStack);
		return $currentFrame
			? $currentFrame->getModule()
			: \null;

	}

	// Call stack management.

	/**
	 * @return array<StackFrame>
	 */
	public function getCallStack(): array {
		return $this->callStack;
	}

	/**
	 * @param StackFrame $call
	 * @return void
	 */
	public function pushCall($call) {

		// Increment current stack size.
		$this->callStackSize++;
		$this->callStack[] = $call;

		// We can check this every time. Even if 'maxCallStackSize' is zero,
		// the 'callStackSize' will never be.
		if ($this->callStackSize === $this->maxCallStackSize) {

			throw new RuntimeError(\sprintf(
				"Maximum call stack size (%d) reached",
				$this->maxCallStackSize
			));

		}

	}

	/**
	 * @return void
	 */
	public function popCall() {

		\array_pop($this->callStack);
		$this->callStackSize--;
		$this->taskQueue->tick();

	}

	// Direct access to native 'builtins' module.

	public function getBuiltins(): Scope {
		return $this->builtins;
	}

	// Direct access to 'std.types' module.

	public function getTypesModule(): Scope {
		return $this->typesModule;
	}

	// Scope management.

	/**
	 * @return Scope
	 */
	public function getCurrentScope() {
		return $this->currentScope;
	}

	// Call + Scope management.

	/**
	 * @param ?StackFrame $call
	 * @param ?Scope $scope
	 * @return void
	 */
	public function pushCallScopePair($call, $scope) {

		// Push call, if there's any.
		if ($call) {

			// Increment current stack size.
			$this->callStackSize++;
			// Call stack.
			$this->callStack[] = $call;

			// We can check this every time. Even if 'maxCallStackSize' is zero,
			// the 'callStackSize' will never be.
			if ($this->callStackSize === $this->maxCallStackSize) {
				throw new RuntimeError(\sprintf(
					"Maximum call stack size (%d) reached",
					$this->maxCallStackSize
				));
			}

		}

		// Push scope, if there's any.
		if ($scope) {
			$this->scopeStack[] = $scope;
			$this->currentScope = $scope;
		}

	}

	/**
	 * @param bool $popCall
	 * @param bool $popScope
	 * @return void
	 */
	public function popCallScopePair($popCall = \true, $popScope = \true) {

		if ($popCall) {
			\array_pop($this->callStack);
			$this->callStackSize--;
		}

		if ($popScope) {
			\array_pop($this->scopeStack);
			$this->currentScope = \end($this->scopeStack) ?: \null;
		}

		$this->taskQueue->tick();

	}

	// Direct access to the current scope - which is the one on the top of the
	// stack. Also fetches stuff from builtins module, if it's not found
	// in current scope (and its parents).

	public function getVariable(string $name): ?AbstractValue {
		return $this->currentScope->getVariable($name)
			?? $this->builtins->getVariable($name);
	}

	/**
	 * @return array<string, AbstractValue>
	 */
	public function getVariables(bool $includeParents = \false): array {

		return array_merge(
			$this->builtins->getVariables(),
			$this->currentScope->getVariables($includeParents)
		);

	}

	/**
	 * @return void
	 */
	public function setVariable(string $name, AbstractValue $value) {
		$this->currentScope->setVariable($name, $value);
	}

	/**
	 * @param array<string, AbstractValue> $pairs
	 * @return void
	 */
	public function setVariables(array $pairs) {
		$this->currentScope->setVariables($pairs);
	}

	/**
	 * Register a return value for function which is currently being executed.
	 */
	public function setRetval(CallRetval $retval): void {

		if ($this->retval) {
			throw new EngineInternalError("Retval already present");
		}

		$this->retval = $retval;

	}

	/**
	 * Return and reset return value that came from some function.
	 */
	public function popRetval(): CallRetval {

		if (!$this->retval) {
			throw new EngineInternalError("Retval not present");
		}

		[$retval, $this->retval] = [$this->retval, \null];
		return $retval;

	}

	/**
	 * Returns true if a return value is currently registered from some
	 * function.
	 */
	public function hasRetval(): bool {
		return isset($this->retval);
	}

}
