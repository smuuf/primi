<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers;

use Smuuf\Primi\Context;
use Smuuf\Primi\Structures\Traceback;
use Smuuf\Primi\Ex\PiggybackException;
use Smuuf\Primi\Structures\ThrownException;
use Smuuf\Primi\Values\TypeValue;
use Smuuf\Primi\Values\AbstractValue;
use Smuuf\Primi\Values\ExceptionValue;

abstract class Exceptions {

	public static function piggyback(
		TypeValue $excType,
		mixed ...$args,
	): never {
		throw new PiggybackException($excType, $args);
	}

	public static function build(
		Context $ctx,
		TypeValue $excType,
		mixed ...$args,
	): ExceptionValue {

		// Convert exception arguments passed ar non-Primi-object-values into
		// Primi objects.
		$args = \array_map(fn($a) => AbstractValue::buildAuto($a), $args);
		return new ExceptionValue($excType, $ctx, $args);

	}

	public static function set(
		Context $ctx,
		TypeValue $excType,
		mixed ...$args,
	): void {
		$ctx->setException(self::build($ctx, $excType, ...$args));
	}

	public static function unwindStack(Context $ctx): Traceback {

		$frame = $ctx->getCurrentFrame();

		// If there's no frame at all, just return empty traceback object.
		if (!$frame) {
			return new Traceback([]);
		}

		/** @var list<array{string, string, OpLocation}> */
		$framelist = [];

		do {
			$opLoc = $frame->getBytecode()->linesInfo[$frame->getOpIndex()];
			$framelist[] = [
				$frame->getName(),
				$frame->getModule(),
				$opLoc,
			];
		} while ($frame = $frame->getParent());

		return new Traceback(array_reverse($framelist));

	}

	public static function renderThrownExceptionToText(
		ThrownException $thrown,
	): string {

		$parts = [
			self::colorizeTracebackText(
				self::renderTracebackToText(
					$thrown->getTraceback(),
				),
			),
			$thrown->exception->getStringValue(),
		];

		return implode("\n", $parts);

	}

	public static function renderTracebackToText(Traceback $tb): string {

		if (!$tb->framelist) {
			return "Traceback: <empty>";
		}

		$result = [];
		$result[] = "Traceback:";

		foreach ($tb->framelist as $level => [$name, $module, $opLoc])  {
			$opLoc = $opLoc->line . ":" . $opLoc->pos;
			$result[] = "[$level] $name in {$module->getStringRepr()} on line $opLoc";
		}

		$result[] = '';

		return \implode("\n", $result);

	}

	public static function colorizeTracebackText(string $tb): string {

		$result = \preg_replace_callback_array([
			'#^Traceback:$#m' => // "Traceback:" string.
				fn($m) => Colors::get("{green}$m[0]"),
			'#(@|in|from) (<.*?>)#m' => // E.g. "in <module: blahblah>"
				fn($m) => Colors::get("{$m[1]} {yellow}{$m[2]}{_}"),
			'#^(\[\d+\]) (.+) in #m' => // E.g. "[4] __main__.somefunc()"
				fn($m) => Colors::get("{darkgrey}{$m[1]}{_} {lightblue}{$m[2]}{_} in "),
			'#near (["\'])(.*?)\\1 @#' => // E.g. '... near "some code" @ ...'
				fn($m) => Colors::get("near {$m[1]}{lightcyan}{$m[2]}{_}{$m[1]}"),
		], $tb);

		return $result;

	}


}
