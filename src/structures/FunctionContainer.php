<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\ReturnException;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\HandlerFactory;

class FunctionContainer extends \Smuuf\Primi\StrictObject {

	/** @var \Closure Closure wrapping the function itself. **/
	protected $closure;

	/** @var array Array containing parameters the function is aware of. **/
	protected $args = [];

	/** @var Context Optional context bound to this function. **/
	protected $context;

	public static function build(
		array $node,
		array $definitionArgs = [],
		Context $definitionContext = \null
	) {

		// Build a closure wrapper around the Primi function.
		// Invoking this closure is equal to standard execution of the nodes
		// that make up the body of the function.
		// The closure also returns the resulting Primi value object.
		$closure = function() use ($node, $definitionContext, $definitionArgs) {

			// Create new context (scope) for the function, so it doesn't operate
			// in the global scope (and thus it won't modify the global context.
			$context = new Context;

			if ($definitionContext) {
				$context->setVariables($definitionContext->getVariables());
			}

			// Create pairs of arguments <arg_name> => <arg_value>.
			$args = \array_combine($definitionArgs, \func_get_args());
			$context->setVariables($args);

			try {

				// Run the function body and expect a ReturnException with the
				// return value.
				$handler = HandlerFactory::get($node['name']);
				$handler::handle($node, $context);

			} catch (ReturnException $e) {
				return $e->getValue();
			}

		};

		return new self($closure, $definitionArgs);

	}

	public static function buildNative(callable $fn) {

		$closure = \Closure::fromCallable($fn);

		$r = new \ReflectionFunction($closure);
		$args = \array_map(function($param) {
			return $param->name;
		}, $r->getParameters());

		return new self($closure, $args);

	}

	/**
	 * Disallow direct instantiation.
	 */
	private function __construct(\Closure $closure, array $args = []) {
		$this->closure = $closure;
		$this->args = $args;
	}

	public function getClosure() {
		return $this->closure;
	}

	public function getArgs() {
		return $this->args;
	}

}
