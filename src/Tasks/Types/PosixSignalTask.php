<?php

declare(strict_types=1);

namespace Smuuf\Primi\Tasks\Types;

use Smuuf\Primi\Repl;
use Smuuf\Primi\Context;
use Smuuf\Primi\Ex\EngineInternalError;
use Smuuf\Primi\Helpers\Exceptions;
use Smuuf\Primi\Stdlib\StaticExceptionTypes;
use Smuuf\Primi\Tasks\TaskInterface;
use Smuuf\StrictObject;

class PosixSignalTask implements TaskInterface {

	use StrictObject;

	/** @var int Posix signal number. */
	private $signum = \null;

	public function __construct(int $signum) {
		$this->signum = $signum;
	}

	public function execute(Context $ctx): void {

		switch ($this->signum) {
			case SIGINT:
				Exceptions::piggyback(
					StaticExceptionTypes::getSystemExceptionType(),
					'Received SIGINT',
				);
			case SIGTERM:
				Exceptions::piggyback(
					StaticExceptionTypes::getSystemExceptionType(),
					'Received SIGTERM',
				);
			case SIGQUIT:

				if (!$ctx->getConfig()->getSigQuitDebugging()) {
					Exceptions::piggyback(
						StaticExceptionTypes::getSystemExceptionType(),
						'Received SIGQUIT',
					);
				}

				$repl = new Repl('debugger');
				$repl->start($ctx);
				return;

			default:
				$msg = sprintf('Unable to handle unknown signal %d', $this->signum);
				throw new EngineInternalError($msg);
		}

	}

}
