<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Ex\ControlFlowException;
use \Smuuf\Primi\Ex\ContextAwareException;
use \Smuuf\Primi\Parser\ParserHandler;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Helpers\Traits\StrictObject;
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
	public function execute(string $source, Context $context): AbstractValue {

		// Each node must have two keys: 'name' and 'text'.
		// These are provided by the PHP-PEG itself, so we should be able to
		// be counting on it.

		// We begin the process of interpreting a source code simply by
		// passing the AST's root node to its dedicated handler (determined by
		// node's "name").

		$ast = $this->getSyntaxTree($source);

		try {
			$handler = HandlerFactory::getFor($ast['name']);
			return $handler::run($ast, $context);
		} catch (ContextAwareException $e) {
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
