<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\ReturnException;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\Context;

class Func extends \Smuuf\Primi\Object {

	/** @var string Function name for convenience. **/
	protected $name;

	/** @var array List of variable names used as arguments. **/
	protected $args;

	/** @var array Node representing the body of function. **/
	protected $body;

	public function __construct(string $name, array $args, array $body) {
		$this->name = $name;
		$this->args = $args;
		$this->body = $body;
	}

	public function call(array $args, Context $callerContext) {

		$handler = HandlerFactory::get($this->body['name']);

		if (count($this->args) !== count($args)) {
			throw new ErrorException(sprintf(
					"Wrong number of arguments passed to the '%s' function (%s instead of %s)",
					$this->name,
					count($args),
					count($this->args)
				));
		}

		// Create new context (scope) for the function, so it doesn't operate in the global scope (and thus it won't
		// modify the global context.
		$context = new Context;

		$args = array_combine($this->args, $args);
		$context->setFunctions($callerContext->getFunctions());
		$context->setVariables($args);

		// Run the function body and expect a ReturnException with the return value.

		try {
			$handler::handle($this->body, $context);
		} catch (ReturnException $e) {
			return $e->getValue();
		}

	}

}
