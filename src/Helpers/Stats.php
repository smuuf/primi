<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\Helpers\Traits\StrictObject;

/**
 * A static runtime statistics gatherer.
 */
abstract class Stats {

	use StrictObject;

	/** @var bool If true, statistics are gathered. Disabled by default. */
	private static $enabled = \false;

	/** @var float Point in time when stats gathering was enabled. */
	private static $startTime = 0;

	/** @var array Dictionary for gathered statistics. */
	private static $stats = [];

	/**
	 * Enable global stats gathering.
	 */
	public static function enable(bool $state = \true): void {
		self::$enabled = $state;
		self::$startTime = Func::monotime();
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
	 * @return array<string, int>
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

	public static function print(): void {

		self::out(Colors::get("{yellow}Runtime stats:{_}"));

		// Print general stats.

		self::out(Colors::get("{green}General:{_}"));

		self::out("PHP: " . PHP_VERSION);

		$mb = round(memory_get_peak_usage() / 1e6, 2);
		self::out("Memory peak: {$mb} MB");

		$duration = round(Func::monotime() - self::$startTime, 2);
		self::out("Runtime duration: {$duration} s");

		// Print gathered stats.

		self::out(Colors::get("{green}Gathered:{_}"));

		ksort(self::$stats);
		foreach (self::$stats as $name => $value) {
			self::out("- {$name}: {$value}");
		}

	}

	private static function out(string $text): void {
		echo "$text\n";
	}

}
