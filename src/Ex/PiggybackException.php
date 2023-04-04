<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

use Smuuf\Primi\Values\TypeValue;

/**
 * PHP exception that can be used by any PHP code that is reached (called) by
 * Primi VM. This is an alternative to manually setting a (Primi) exception into
 * the interpreter context.
 *
 * Context object may not be available everywhere and this allows us to
 * piggy-back on the PHP's exception system to make things easier for Primi's
 * exception system.
 */
class PiggybackException extends BaseException {

	/**
	 * @param list<AbstractValue>
	 */
	public function __construct(
		public readonly TypeValue $excType,
		public readonly array $args,
	) {}

}

