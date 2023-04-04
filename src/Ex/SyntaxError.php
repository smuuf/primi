<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

use Closure;
use Smuuf\Primi\Code\Bytecode;
use Smuuf\Primi\Location;
use Smuuf\Primi\Code\Source;
use Smuuf\Primi\Context;
use Smuuf\Primi\Helpers\Exceptions;
use Smuuf\Primi\Helpers\Func;
use Smuuf\Primi\Helpers\StringEscaping;
use Smuuf\Primi\Stdlib\StaticExceptionTypes;

class SyntaxError extends EngineException {

	public function __construct(
		private Location $location,
		private ?string $excerpt = \null,
		private ?string $reason = \null,
	) {

		$sanitizedExcerpt = $excerpt
			? StringEscaping::escapeString($excerpt, '"')
			: false;

		$msg = \sprintf(
			"Invalid syntax%s%s",
			$reason
				? \sprintf(" (%s)", \trim($reason))
				: '',
			$sanitizedExcerpt
				? \sprintf(" near \"%s\"", \trim($sanitizedExcerpt))
				: '',
		);

		parent::__construct($msg);

	}

	public function getLocation(): Location {
		return $this->location;
	}

	public static function fromInternal(
		InternalSyntaxError $e,
		Source $source
	): SyntaxError {

		$sourceString = $source->getSourceString();
		$offset = $e->offset;
		$line = \false;

		[$line, $linePos] = Func::get_position_estimate($sourceString, $offset);

		// Show a bit of code where the syntax error occurred.
		// And don't ever have negative start index (that would take characters
		// indexed from the end).
		$excerpt = \mb_substr($sourceString, max($offset - 5, 0), 10);

		return new SyntaxError(
			new Location($source->getSourceId(), $line, $linePos),
			$excerpt,
			$e->reason,
		);

	}

	/**
	 * Executes the provided closure and if it throws a SyntaxError
	 * PHP exception, it prepares a Primi SyntaxError exception which will be
	 * correctly thrown and handled in virtual machine later.
	 */
	public static function catch(Context $ctx, \Closure $fn): mixed {

		try {
			$bytecode = $fn();
		} catch (SyntaxError $ex) {

			// Build fake bytecode and set a SyntaxError exception into the
			// context. When the fake bytecode is executed, the exception will
			// be immediately handled as a thrown exception.

			$loc = $ex->getLocation();
			$bytecode = Bytecode::buildFake(
				$loc->getLine(),
				$loc->getPosition()
			);

			Exceptions::set(
				$ctx,
				StaticExceptionTypes::getSyntaxErrorType(),
				$ex->getMessage(),
			);

		}

		return $bytecode;


	}

}
