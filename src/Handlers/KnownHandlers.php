<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Ex\EngineInternalError;

abstract class KnownHandlers {

	private static bool $initialized = \false;

	/**
	 * State ID of present handlers. This ID will be different if any handler
	 * is added, removed or modified.
	 *
	 * Why do we track this? Because if anything changes, old parsed and cached
	 * ASTs are invalid and we need to tell our cache not to use them.
	 */
	private static string $stateId = '';

	/** @var array<string, int> */
	private static array $namesToIdsMapping = [];

	/** @var array<int, string> */
	private static array $idsToNamesMapping = [];

	/**
	 * Helper function which returns *.php files from the directory where
	 * source files of handlers are present.
	 *
	 * @return array<int, string>
	 */
	private static function listHandlerFiles(): array {

		$files = [];

		// One might think that `glob()` would be enough, but `glob()` doesn't
		// work when we're inside PHAR, so we need the old-fashioned non-glob way.
		foreach (\scandir(__DIR__ . "/Kinds/") as $filename) {
			if (!\str_ends_with($filename, '.php')) {
				continue;
			}
			$files[] = __DIR__ . "/Kinds/$filename";
		}

		return $files;

	}

	public static function init(): void {

		if (self::$initialized) {
			return;
		}

		$handlerStateId = [];
		foreach (self::listHandlerFiles() as $id => $filepath) {

			// Don't start at zero, so that no handler has ID of zero - let's
			// prevent any dumb errors that might be caused by something
			// somewhere comparing the handler ID the wrong way (0 is falsy).
			$id += 1;

			// "./Kinds/SomeHandler.php" -> "SomeHandler"
			$name = \strchr(\basename($filepath), '.php', \true);

			self::$namesToIdsMapping[$name] = $id;
			self::$idsToNamesMapping[$id] = $name;

			// Get hash of the handler source code, so we track changes inside.
			$handlerStateId[$name] = \md5(\file_get_contents($filepath));

		}

		self::$stateId = md5(\json_encode([
			self::$namesToIdsMapping,
			self::$idsToNamesMapping,
			$handlerStateId,
		]));

		self::$initialized = \true;

	}

	public static function getStateId(): string {
		return self::$stateId;
	}

    public static function fromId(int $id): string {

		$name = self::$idsToNamesMapping[$id] ?? \null;
        if ($name === \null) {
			throw new EngineInternalError("Unknown handler ID '$id'");
		}

		return $name;

	}

    public static function fromName(string $name, bool $strict = \true): ?int {

		$id = self::$namesToIdsMapping[$name] ?? \null;
        if ($id === \null) {
			if ($strict) {
				throw new EngineInternalError("Unknown handler name '$name'");
			} else {
				return \null;
			}
		}

		return $id;

	}

}

KnownHandlers::init();
