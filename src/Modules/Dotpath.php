<?php

declare(strict_types=1);

namespace Smuuf\Primi\Modules;

use Smuuf\StrictObject;
use Smuuf\Primi\Ex\EngineInternalError;
use Smuuf\Primi\Ex\ImportBeyondTopException;

class Dotpath {

	use StrictObject;

	private const VALID_REGEX = '#^(:?|(\.*))(?:[a-zA-Z_][a-zA-Z0-9_]*\.)*(?:[a-zA-Z_][a-zA-Z0-9_]*)$#';

	/** Final absolute dotpath (with relativity resolved). */
	private string $absolute;

	/**
	 * Parts of the resolved dotpath.
	 *
	 * @var array<string>
	 */
	private array $parts;

	public function __construct(
		string $dotpath,
		string $originPackage = ''
	) {

		if (!self::isValid($dotpath)) {
			throw new EngineInternalError("Invalid dot path '$dotpath'");
		}

		if ($originPackage !== '' && !self::isValid($originPackage)) {
			throw new EngineInternalError("Invalid origin package dot path '$originPackage'");
		}

		[
			$this->absolute,
			$this->parts,
		] = self::resolve($dotpath, $originPackage);

	}

	/**
	 * Resolve the original, possibly relative, dotpath into an absolute
	 * dotpath using origin as the origin dotpath.
	 *
	 * @return array{string, array<string>}
	 */
	private static function resolve(
		string $dotpath,
		string $originPackage
	): array {

		$parts = [];
		$steps = \explode('.', $dotpath);

		// If the dotpath is absolute, we don't need to process it further.
		if (!\str_starts_with($dotpath, '.')) {
			return [$dotpath, $steps];
		}

		// If the dotpath is relative (starts with a dot), use passed origin as
		// the origin.
		$parts = $originPackage
			? \explode('.', $originPackage)
			: [];

		// Exploding relative import dotpath '.a.b' results in array list
		// with three items - empty string being the first - so let's get
		// rid of it. It's because the "while" algo below handles each one
		// of these empty strings as "go-one-level-up" from the origin
		// package dotpath, but we want the first dot in relative path
		// to mean "current level", not "one level up".
		\array_shift($steps);

		while (\true) {

			// If there are no more steps to process, break the loop.
			if (!$steps) {
				break;
			}

			$step = array_shift($steps);

			// Empty step means a single dot exploded from the original dotpath.
			// And
			if ($step === '') {

				// If there are no parts left and one level up was requested,
				// it's a beyond-top-level error.
				if (!$parts) {
					throw new ImportBeyondTopException($dotpath);
				}

				array_pop($parts);
				continue;

			}

			$parts[] = $step;

		}

		// Save final dotpath (with relativity resolved).
		$absolute = \implode('.', $parts);
		return [$absolute, $parts];

	}

	public function getAbsolute(): string {
		return $this->absolute;
	}

	public function getFirstPart(): string {
		return \reset($this->parts);
	}

	/**
	 * @return iterable<array{string, string, string}>
	 */
	public function iterPaths(string $basepath = ''): iterable {

		$dotpath = '';
		$package = '';
		$filepath = "{$basepath}/";

		foreach ($this->parts as $part) {

			$dotpath .= $part;
			$filepath .= $part;

			yield [$dotpath, $package, $filepath];

			$package = $dotpath;
			$dotpath .= '.';
			$filepath .= '/';

		}

	}

	// Helpers.

	/** Returns true if the dotpath string is a valid dotpath. */
	private static function isValid(string $dotpath): bool {
		return (bool) \preg_match(self::VALID_REGEX, $dotpath);
	}

}
