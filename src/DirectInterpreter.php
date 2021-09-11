<?php

namespace Smuuf\Primi;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Ex\SystemException;
use \Smuuf\Primi\Ex\ControlFlowException;
use \Smuuf\Primi\Code\Ast;
use \Smuuf\Primi\Tasks\Emitters\PosixSignalTaskEmitter;
use \Smuuf\Primi\Values\AbstractValue;
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
abstract class DirectInterpreter {

	use StrictObject;

	/**
	 * Execute source code within a given context.
	 *
	 * This is a bit lower-level approach to running some source code provided
	 * as `Ast` object. This allows the client to specify a context object
	 * to run the code (represented by `Ast` object) within. This in turn
	 * allows, for example, the REPL to operate within some specific context
	 * representing a runtime-in-progress (where it can access local variables).
	 *
	 * @param Ast $ast `Ast` object representing the AST to execute.
	 */
	public static function execute(
		Ast $ast,
		Context $ctx
	): AbstractValue {

		// Register signal handling - maybe.
		if ($ctx->getConfig()->getEffectivePosixSignalHandling()) {
			PosixSignalTaskEmitter::catch(SIGINT);
			PosixSignalTaskEmitter::catch(SIGQUIT);
			PosixSignalTaskEmitter::catch(SIGTERM);
		}

		$wrapper = new CatchPosixSignalsWrapper($ctx->getTaskQueue());
		return $wrapper->wrap(function() use ($ast, $ctx) {

			try {

				$tree = $ast->getTree();
				return HandlerFactory::getFor($tree['name']);

			} catch (ControlFlowException $e) {

				$what = $e::ID;
				throw new RuntimeError("Cannot '{$what}' from global scope");

			} finally {

				try {

					// This is the end of a single runtime, so run any tasks
					// that may be still left in the task queue (this means, for
					// example, that all callbacks in the queue will still be
					// executed).
					$ctx->getTaskQueue()->deplete();

				} catch (SystemException $e) {
					throw new RuntimeError($e->getMessage());
				}

			}

		});

	}

}
