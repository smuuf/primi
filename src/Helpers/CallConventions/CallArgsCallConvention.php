<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers\CallConventions;

use Smuuf\StrictObject;
use Smuuf\Primi\Context;
use Smuuf\Primi\Values\AbstractValue;
use Smuuf\Primi\Structures\CallArgs;

/**
 * Call convention for invoking PHP callables from within Primi code/engine.
 *
 * This convention passes pure CallArgs object into the PHP callable and
 * how args and kwargs are handled is left to the PHP callable to decide and
 * implement.
 *
 * @internal
 */
class CallArgsCallConvention implements CallConventionInterface {

	use StrictObject;

	private \Closure $closure;

	public function __construct(\Closure $closure) {
		$this->closure = $closure;
	}

	public function call(
		CallArgs $args,
		Context $ctx
	): ?AbstractValue {
		return ($this->closure)($args, $ctx);
	}

}

