<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\ReturnException;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\Context;

class FuncValue extends Value {

	const TYPE = "function";

	/** @var string Function name is stored - in 'value' property - only for convenience. **/
	protected $value;

	/** @var array List of variable names used as arguments. **/
	protected $args;

	/** @var array Node representing the body of function. **/
	protected $body;

	/** @var Context Optional context bound to this function. **/
	protected $context;

	public function __construct(
		string $name,
		array $args,
		array $body,
		Context $context = null
	) {
		$this->value = $name;
		$this->args = $args;
		$this->body = $body;
		$this->context = $context;
	}

	public function getStringValue(): string {
		return $this->value;
	}

	public function invoke(array $args) {

		$handler = HandlerFactory::get($this->body['name']);

		if (\count($this->args) !== \count($args)) {
			throw new ErrorException(sprintf(
				"Too few arguments passed to the '%s' function (%s instead of %s)",
				$this->value,
				\count($args),
				\count($this->args)
			), $this->body);
		}

		// Create new context (scope) for the function, so it doesn't operate in the global scope (and thus it won't
		// modify the global context.
		$context = new Context;

		$args = \array_combine($this->args, $args);
		if ($this->context) {
			$context->setVariables($this->context->getVariables());
		}

		$context->setVariables($args);

		// Run the function body and expect a ReturnException with the return value.

		try {
			$handler::handle($this->body, $context);
		} catch (ReturnException $e) {
			return $e->getValue();
		}

	}

}
