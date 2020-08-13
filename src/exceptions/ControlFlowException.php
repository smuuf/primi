<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

abstract class ControlFlowException extends EngineException {

	/**
	 * Statement causing such control flow mechanism. This is a default value
	 * and ultimately shouldn't appear anywhere, as it should always be
	 * overriden in child classes.
	 *
	 * @const string
	 */
	public const ID = 'control flow';

}
