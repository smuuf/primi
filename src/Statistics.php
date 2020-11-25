<?php

declare(strict_types=1);

namespace Smuuf\Primi;

/**
 * A static runtime statistics gatherer.
 */
abstract class Statistics extends StrictObject {

	/** @var bool If true, statistics are gathered. Disabled by default. */
	private static $enabled = false;

	/** @var Dictionary for gathered statistics. */
	private static $stats = [];

	/**
	 * Enable global stats gathering.
	 */
	public static function enable(bool $state = true): void {
		self::$enabled = $state;
	}

	public static function add(string $what): void {

		if (!self::$enabled) {
			return;
		}

		self::$stats[$what] = (self::$stats[$what] ?? 0) + 1;

	}

	public static function get(string $prefix = ''): \Generator {

		$trim = strlen($prefix);
		foreach (self::$stats as $name => $value) {

			if ($prefix && strpos($name, $prefix) !== 0) {
				continue;
			}

			yield substr($name, $trim) => $value;

		}

	}

	public static function single(string $name): int {
		return self::$stats[$name] ?? 0;
	}

}
