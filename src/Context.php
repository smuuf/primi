<?php

declare(strict_types=1);

namespace Smuuf\Primi;

use Smuuf\StrictObject;
use Smuuf\Primi\Scope;
use Smuuf\Primi\Ex\EngineError;
use Smuuf\Primi\Ex\UncaughtError;
use Smuuf\Primi\VM\Frame;
use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Code\Source;
use Smuuf\Primi\Code\Bytecode;
use Smuuf\Primi\Code\BytecodeProvider;
use Smuuf\Primi\Tasks\TaskQueue;
use Smuuf\Primi\Values\ModuleValue;
use Smuuf\Primi\Values\AbstractValue;
use Smuuf\Primi\Values\ExceptionValue;
use Smuuf\Primi\Drivers\StdIoDriverInterface;
use Smuuf\Primi\Ex\RuntimeError;
use Smuuf\Primi\Ex\SyntaxError;
use Smuuf\Primi\Helpers\Exceptions;
use Smuuf\Primi\Helpers\Wrappers\CatchPosixSignalsWrapper;
use Smuuf\Primi\Modules\Importer;
use Smuuf\Primi\Stdlib\StaticExceptionTypes;
use Smuuf\Primi\Structures\ThrownException;

class Context {

	use StrictObject;

	private Machine $vm;
	private ?Frame $currentFrame = null;

	//
	// Context services.
	//

	/** Task queue for this context. */
	private TaskQueue $taskQueue;

	private Importer $importer;
	private BytecodeProvider $bytecodeProvider;
	private StdIoDriverInterface $stdIoDriver;

	/** Native `std.__builtins__` module. */
	private Scope $builtins;

	/** Current thrown and pending exception. */
	private ?ThrownException $thrownExc = null;

	public function __construct(
		private Config $config,
	) {

		$services = new ContextServices($this, $config);
		$this->vm = new Machine($this);

		$this->stdIoDriver = $this->config->getStdIoDriver();

		// Assign stuff to properties to avoid unnecessary indirections when
		// accessing them (optimization).
		$this->bytecodeProvider = $services->getBytecodeProvider();
		$this->taskQueue = $services->getTaskQueue();
		$this->importer = $services->getImporter();

		// Import our builtins module.
		$this->builtins = $this->importer
			->getModule('std.__builtins__')
			->getCoreValue();

	}

	// Access to runtime config.

	public function getConfig(): Config {
		return $this->config;
	}

	// Access to AST provider.

	public function getBytecodeProvider(): BytecodeProvider {
		return $this->bytecodeProvider;
	}

	// Standard IO driver.

	public function getStdIoDriver(): StdIoDriverInterface {
		return $this->stdIoDriver;
	}

	// Task queue management.

	public function getTaskQueue(): TaskQueue {
		return $this->taskQueue;
	}

	// Import management.

	public function getImporter(): Importer {
		return $this->importer;
	}

	// Direct access to native 'builtins' module.

	public function getBuiltins(): Scope {
		return $this->builtins;
	}

	public function runMain(Source $source, Scope $scope): AbstractValue {

		$mainModule = new ModuleValue(
			name: MagicStrings::MODULE_MAIN_NAME,
			package: '', // Main module is always in the top package.
			scope: $scope,
		);

		$bytecode = SyntaxError::catch(
			$this,
			fn() => $this->bytecodeProvider->getBytecode($source),
		);

		$frame = $this->buildFrame(
			name: '<main>',
			bytecode: $bytecode,
			scope: $scope,
			module: $mainModule,
		);

		$retval = $this->runFrame($frame);

		if ($this->thrownExc) {
			throw new UncaughtError($this->getAndResetException());
		}

		return $retval;

	}

	public function buildFrame(
		string $name,
		?Bytecode $bytecode,
		?Scope $scope = null,
		?ModuleValue $module = null,
	): Frame {

		$current = $this->getCurrentFrame();
		if ($scope === null) {
			$scope = new Scope(parent: $current?->getScope());
		}

		return new Frame(
			name: $name,
			bytecode: $bytecode,
			scope: $scope,
			module: $module ?? $current?->getModule(),
			parent: $current,
		);

	}

	public function runFrame(Frame $frame): AbstractValue {

		$wrapper = new CatchPosixSignalsWrapper($this->taskQueue);
		return $wrapper->wrap(function() use ($frame) {
			try {
				return $this->vm->run($frame);
			} finally {

				try {

					// This is the end of a single runtime, so run any tasks
					// that may be still left in the task queue (this means, for
					// example, that all callbacks in the queue will still be
					// executed).
					$this->taskQueue->deplete();

				} catch (SystemException $e) {
					throw new RuntimeError($e->getMessage());
				}

			}

		});

	}

	/**
	 * Executes a bytecode object within a copy of the specified frame, or
	 * within a copy of current (top) frame.
	 */
	public function runSource(
		Source $source,
		?Frame $frame = null,
		array $compilerArgs = [],
	): AbstractValue {

		$bytecode = SyntaxError::catch(
			$this,
			fn() => BytecodeProvider::compile($source, ...$compilerArgs),
		);

		$frame = $frame ?? $this->getCurrentFrame();

		if (!$frame) {
			throw new EngineError(sprintf(
				"Trying to call %s without any existing frame",
				__METHOD__,
			));
		}

		$result = $this->runFrame($frame->withBytecode($bytecode));
		if ($this->thrownExc) {
			throw new UncaughtError($this->getAndResetException());
		}

		return $result;

	}

	// Frames management.

	/**
	 * Sets current frame.
	 * @param ?Frame $frame
	 * @return void
	 */
	public function setCurrentFrame($frame) {
		$this->currentFrame = $frame;
	}

	/**
	 * Returns current frame.
	 *
	 * @return ?Frame
	 */
	public function getCurrentFrame() {
		return $this->currentFrame;
	}

	// Exceptions management.

	/**
	 * @param ExceptionValue $exc
	 * @return void
	 */
	public function setException($excValue) {
		$this->thrownExc = new ThrownException($excValue);
	}

	/**
	 * @return ?ThrownException
	 */
	public function getPendingException() {
		return $this->thrownExc;
	}

	/**
	 * @return ?ThrownException
	 */
	public function getAndResetException() {
		$exc = $this->thrownExc;
		$this->thrownExc = null;
		return $exc;
	}

}
