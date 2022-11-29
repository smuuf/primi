<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\Cli\Term;
use \Smuuf\StrictObject;

/**
 * A static runtime statistics gatherer.
 */
abstract class Stats {

	use StrictObject;

	/** @var float Point in time when stats gathering was enabled. */
	private static float $startTime = 0;

	/** @var array<string, int> Dictionary for gathered statistics. */
	private static array $stats = [];

	/**
	 * Initialize stats gathering.
	 */
	public static function init(): void {
		self::$startTime = Func::monotime();
	}

	/**
	 * Increment counter of some stats entry by 1 (the entry is initialized
	 * with 0 if it doesn't exist yet
	 */
	public static function add(string $entryName): void {
		self::$stats[$entryName] = (self::$stats[$entryName] ?? 0) + 1;
	}

	/**
	 * Return a single stats entry by its name.
	 */
	public static function get(string $name): int {
		return self::$stats[$name] ?? 0;
	}

	public static function print(): void {

		self::out(Colors::get("{yellow}Runtime stats:{_}"));

		//
		// Print general stats.
		//

		self::out(Colors::get("{green}General:{_}"));
		self::out("PHP: " . PHP_VERSION);

		$mb = round(memory_get_peak_usage(true) / 1e6, 2);
		self::out("Memory peak: {$mb} MB");

		$duration = round(Func::monotime() - self::$startTime, 2);
		self::out("Runtime duration: {$duration} s");

		//
		// Print gathered stats, if there are any.
		//

		if (self::$stats) {
			self::out(Colors::get("{green}Gathered:{_}"));
			ksort(self::$stats);
			foreach (self::$stats as $name => $value) {
				self::out("- {$name}: {$value}");
			}
		}

	}

	private static function out(string $text): void {
		Term::stderr(Term::line($text));
	}

}
