<?php

declare(strict_types=1);

namespace Smuuf\Primi\Modules;

use \Smuuf\Primi\Ex\EngineInternalError;
use \Smuuf\Primi\Helpers\Traits\StrictObject;

class DotPath {

	use StrictObject;

	private const VALID_REGEX = '#^\.*(?:[a-zA-Z_][a-zA-Z0-9_]*\.)*(?:[a-zA-Z_][a-zA-Z0-9_]*)$#';

	/** The original dotpath. */
	private string $dotPath;

	/**
	 * True is the dotpath is relative (starts with a dot), or false if it is
	 * absolute.
	 */
	private bool $isRelative;

	/** Parts of the resolved dotpath. */
	private array $parts;

	public function __construct(string $dotPath) {

		if (!self::isValid($dotPath)) {
			throw new EngineInternalError("Invalid dot path '$dotPath'");
		}

		$this->dotPath = $dotPath;
		$this->isRelative = str_starts_with($dotPath, '.');
		$this->parts = self::resolve($dotPath);

	}

	private static function isValid(string $dotPath): bool {
		return (bool) \preg_match(self::VALID_REGEX, $dotPath);
	}

	/**
	 * Resolve dotpath into a list of path parts that can be used to navigate
	 * filesystem.
	 *
	 * Examples:
	 * - `a.b.c` -> `['a', 'b', 'c']`
	 * - `.a.b.c` -> `['.', 'a', 'b', 'c']`
	 * - `..a.b.c` -> `['.', '..', 'a', 'b', 'c']`
	 * - `...a.b.c` -> `['.', '..', '..', 'a', 'b', 'c']`
	 */
	private static function resolve(string $dotPath): array {

		$start = \true;
		$parts = [];
		foreach (\explode('.', $dotPath) as $part) {

			// Each dot in the dotpath represents a directory.
			// Dots at the beginning of the dotpath represent current directory
			// or parent directories. So, a single dot is current directory, two
			// dots represent parent directory, three dots represent parent
			// directory of the parent directory, and so on.
			// Since dotpath was exploded using '.', empty $part represents a
			// single dot in the original dotpath.

			if ($part === '') {
				if ($start) {
					// First dot at the start of the dotpath is current dir.
					$parts[] = '.';
				} else {
					// Other dots at the start of the dotpath are parent dirs.
					$parts[] = '..';
				}
			} else {
				$parts[] = $part;
			}

			$start = \false;

		}

		return $parts;

	}

	public function getOriginal(): string {
		return $this->dotPath;
	}

	public function isRelative(): bool {
		return $this->isRelative;
	}

	public function getParts(): array {
		return $this->parts;
	}

}
