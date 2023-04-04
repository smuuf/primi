<?php

declare(strict_types=1);

namespace Smuuf\Primi;

use Smuuf\StrictObject;
use Smuuf\Primi\Scope;
use Smuuf\Primi\Ex\EngineError;
use Smuuf\Primi\Code\Source;
use Smuuf\Primi\Code\SourceFile;
use Smuuf\Primi\Tasks\Emitters\PosixSignalTaskEmitter;

/**
 * Primi's primary abstract syntax tree interpreter.
 */
class Interpreter {

	use StrictObject;

	/** Runtime configuration. */
	private Config $config;

	/**
	 * Create a new instance of interpreter.
	 *
	 * @param Config|null $config _(optional)_ Config for the interpreter.
	 */
	public function __construct(?Config $config = null) {

		$this->config = $config ?? Config::buildDefault();
		EnvInfo::bootCheck();

		// Register signal handling - maybe.
		if ($this->config->getEffectivePosixSignalHandling()) {
			PosixSignalTaskEmitter::catch(SIGINT);
			PosixSignalTaskEmitter::catch(SIGQUIT);
			PosixSignalTaskEmitter::catch(SIGTERM);
		}

	}

	public function buildContext(): Context {
		return new Context($this->config);
	}

	/**
	 * Main entrypoint for executing Primi source code.
	 *
	 * @param string|Source $source Primi source code provided as string or
	 * as an instance of `Source` object.
	 * @param Scope|null $scope Optional scope object that is to be used as
	 * global scope of the main module.
	 * @param Context|null $ctx Optional context object the interpreter
	 * should use.
	 */
	public function run(
		string|Source $source,
		?Scope $scope = \null,
		?Context $context = \null,
	): InterpreterResult {

		// This is forbidden - Context already has initialized importer
		// with its import paths, but SourceFile would also expect its
		// directory in the import paths.
		if ($context && $source instanceof SourceFile) {
			throw new EngineError(
				"Cannot pass SourceFile and Context at the same time");
		}

		$source = \is_string($source)
			? new Source($source)
			: $source;

		$context ??= $this->buildContext();
		$scope ??= new Scope();

		$context->runMain($source, $scope);

		return new InterpreterResult($scope, $context);

	}

}
