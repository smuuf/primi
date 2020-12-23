<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\Config;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Ex\SystemException;
use \Smuuf\Primi\Ex\ControlFlowException;
use \Smuuf\Primi\Ex\ContextAwareException;
use \Smuuf\Primi\Tasks\Emitters\PosixSignalTaskEmitter;
use \Smuuf\Primi\Parser\ParserHandler;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Helpers\Traits\StrictObject;
use \Smuuf\Primi\Helpers\Wrappers\CatchPosixSignalsWrapper;
use \Smuuf\Primi\Handlers\HandlerFactory;

/**
 * Primi's direct raw abstract syntax tree interpreter.
 *
 * Raw interpreter can be used to simply execute some Primi source code
 * within a given context.
 *
 * @see https://en.wikipedia.org/wiki/Interpreter_(computing)#Abstract_Syntax_Tree_interpreters
 */
class DirectInterpreter {

	use StrictObject;

	/**
	 * Execute source code within a given context.
	 *
	 * This is a bit lower-level approach to running some source code provided
	 * as string, which allows the client to specify a context object to run the
	 * code within. This allows, for example, the REPL to operate within
	 * a context representing a runtime-in-progress (and access local variables,
	 * for example).
	 */
	public function execute(string $source, Context $ctx): AbstractValue {

		// Each node must have two keys: 'name' and 'text'.
		// These are provided by the PHP-PEG itself, so we should be able to
		// be counting on it.

		// We begin the process of interpreting a source code simply by
		// passing the AST's root node to its dedicated handler (determined by
		// node's "name").

		$ast = $this->getSyntaxTree($source);

		// Register signal handling - maybe.
		if (Config::getEffectivePosixSignalHandling()) {
			PosixSignalTaskEmitter::catch(SIGINT);
			PosixSignalTaskEmitter::catch(SIGQUIT);
			PosixSignalTaskEmitter::catch(SIGTERM);
		}

		try {

			$wrapper = new CatchPosixSignalsWrapper($ctx->getTaskQueue());
			return $wrapper->wrap(function() use ($ast, $ctx) {

				$handler = HandlerFactory::getFor($ast['name']);
				$retval = $handler::run($ast, $ctx);

				// This is the end of a single runtime, so run any tasks that
				// may be still left in the task queue (this means, for example,
				// that all callbacks in the queue will still be executed).
				$ctx->getTaskQueue()->deplete();

				return $retval;

			});

		} catch (ContextAwareException|SystemException $e) {
			throw new RuntimeError($e->getMessage());
		} catch (ControlFlowException $e) {
			$what = $e::ID;
			throw new RuntimeError("Cannot '{$what}' from global scope");
		}

	}

	public function getSyntaxTree(string $source): array {
		return (new ParserHandler($source))->run();
	}

}
