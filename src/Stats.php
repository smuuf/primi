<?php

declare(strict_types=1);

namespace Smuuf\Primi;

/**
 * A static runtime statistics gatherer.
 */
abstract class Stats extends StrictObject {

	/** @var bool If true, statistics are gathered. Disabled by default. */
	private static $enabled = \false;

	/** @var array Dictionary for gathered statistics. */
	private static $stats = [];

	/**
	 * Enable global stats gathering.
	 */
	public static function enable(bool $state = \true): void {
		self::$enabled = $state;
	}

	/**
	 * Increment counter of stats entry by 1.
	 * The stats entry is initialized with 0 if it doesn't exist yet.
	 */
	public static function add(string $entryName): void {

		if (!self::$enabled) {
			return;
		}

		self::$stats[$entryName] = (self::$stats[$entryName] ?? 0) + 1;

	}

	/**
	 * Return an array of multiple stats entries based on some prefix.
	 * The prefix is trimmed off the stats entry name.
	 *
	 * @return array<name, int>
	 */
	public static function multi(string $prefix = ''): array {

		$result = [];
		$trim = \strlen($prefix);

		foreach (self::$stats as $name => $value) {

			if ($prefix && \strpos($name, $prefix) !== 0) {
				continue;
			}

			$result[\substr($name, $trim)] = $value;

		}

		return $result;

	}

	/**
	 * Return a single stats entry by its full name.
	 */
	public static function single(string $name): int {
		return self::$stats[$name] ?? 0;
	}

}