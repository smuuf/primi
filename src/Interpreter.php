<?php

namespace Smuuf\Primi;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Scope;
use \Smuuf\Primi\Code\Source;
use \Smuuf\Primi\Code\SourceFile;
use \Smuuf\Primi\Values\ModuleValue;
use \Smuuf\Primi\Helpers\Wrappers\ContextPushPopWrapper;

/**
 * Primi's primary abstract syntax tree interpreter.
 */
class Interpreter {

	use StrictObject;

	/** Runtime context the interpreter works with. */
	private Context $context;

	/** Runtime configuration. */
	private Config $config;

	/** Last context that was being executed. */
	private ?Context $lastContext = null;

	/**
	 * Create a new instance of interpreter.
	 *
	 * @param Context|null $context _(optional)_ Runtime context to be used
	 * by the interpreter. If not provided, a new context is created
	 * automatically.
	 */
	public function __construct(?Config $config = null) {
		$this->config = $config ?? Config::buildDefault();
	}

	/**
	 * Return the last `Context` object that was being executed.
	 *
	 * This is handy for context inspection if any unhandled exception ocurred
	 * during Primi runtime.
	 */
	public function getLastContext(): ?Context {
		return $this->lastContext;
	}

	/**
	 * Main entrypoint for executing Primi source code.
	 *
	 * @param string|Source Source provided as string or instance of `Source`
	 * object.
	 * @param Scope|null $globalScope Optional scope object that is to
	 * be used as global scope of the main module.
	 */
	public function run(
		$source,
		?Scope $mainScope = null
	): Scope {

		$mainDirectory = null;
		if (\is_string($source)) {

			// Convert string source to source string object, if necessary.
			$source = new Source($source);

		} elseif ($source instanceof SourceFile) {

			// Extract main directory from the source file - it will be added
			// to import paths for the Importer to use.
			$mainDirectory = $source->getDirectory();

		}

		$mainScope = $mainScope ?? new Scope;
		$mainModule = new ModuleValue('__main__', '', $mainScope);

		$interpreterServices = new InterpreterServices($this->config);
		$ast = $interpreterServices->getAstProvider()->getAst($source);

		$frame = new StackFrame('<main>', $mainModule);
		$context = new Context($interpreterServices, $mainDirectory);

		$this->lastContext = $context;

		$wrapper = new ContextPushPopWrapper($context, $frame, $mainScope);
		$wrapper->wrap(function($ctx) use ($ast) {
			return DirectInterpreter::execute($ast, $ctx);
		});

		return $mainScope;

	}

}
