<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers\Traits;

use \Smuuf\Primi\Helpers\Func;

/**
 * Use external scope for watching life-cycles, as different classes that
 * use the WatchLifecycle trait don't share the one trait's static properties.
 */
abstract class WatchLifecycleScope {

	public static int $instanceCounter = 0;
	public static int $stackCounter = 0;

	/** @var array<int, bool> */
	public static array $alreadyVisualized = [];

	/** @var array<int, string> */
	public static array $stack = [];

}

/**
 * Currently supports only classes without additional parent constructors/destructors.
 */
trait WatchLifecycle {

	/** @var int Every unique instance of "watched" class's number. */
	private static $instanceCounter;

	public function watchLifecycle() {

		// Assign a new, globally unique counter's number for this new object.
		self::$instanceCounter = WatchLifecycleScope::$instanceCounter++;

		// Build a completely unique hash for this object's instance.
		$hash = self::getHash($this);

		// Add this object to global watch stack.
		$this->add($hash);

		// Build a unique true color for this instance.
		$colorHash = self::truecolor($hash, $hash);
		$visual = $this->visualize();

		// Visualize newly created object with a pretty sign.
		echo "+  $colorHash $visual\n";

	}

	public function __destruct() {

		// Build visualization string before removing this object from watched stack.
		$visual = $this->visualize();

		// Remove this object from global watch stack.
		$hash = self::getHash($this);
		$this->remove($hash);

		$colorHash = self::truecolor($hash, $hash);
		echo " - $colorHash $visual\n";

	}

	/**
	 * Add this currently watched instance to global stack.
	 */
	private function add(string $hash): void {
		WatchLifecycleScope::$stack[++WatchLifecycleScope::$stackCounter] = $hash;
	}

	/**
	 * Remove this currently watched instance from global stack.
	 */
	private function remove(string $hash): void {
		unset(
			WatchLifecycleScope::$stack[
				array_search($hash, WatchLifecycleScope::$stack, true)
			]
		);
	}

	private function visualize(): string {

		if (!WatchLifecycleScope::$stack) return "x";

		$max = max(array_keys(WatchLifecycleScope::$stack));
		$return = null;

		foreach (range(1, $max) as $pos) {

			// If such position exists in our watching stack, display a character.
			// If this stack item was not displayed yet, display a special character.
			$return .= isset(WatchLifecycleScope::$stack[$pos])
				? (isset(WatchLifecycleScope::$alreadyVisualized[$pos]) ? "|" : "o")
				: " ";
			WatchLifecycleScope::$alreadyVisualized[$pos] = true;

		}

		return $return;

	}

	/**
	 * Return a completely unique hash for this object's instance.
	 * Uniqueness is based off a 1) global counter, 2) class name, 3) spl_object_hash.
	 *
	 * Now that I'm thinking about it, the "1)" should be enough, but, well...
	 */
	private static function getHash(object $object): string {

		return Func::string_hash(
			self::$instanceCounter
			. get_class($object)
			. spl_object_hash($object)
		);

	}

	private static function truecolor(string $hex, string $content): string {

		$r = self::numpad(hexdec(substr($hex, 0, 2)) + 32);
		$g = self::numpad(hexdec(substr($hex, 2, 2)) + 32);
		$b = self::numpad(hexdec(substr($hex, 4, 2)) + 32);

		$out = sprintf("\x1b[38;2;%s;%s;%sm", $r, $g, $b);
		$out .= $content . "\033[0m"; // Reset colors.
		return $out;

	}

	/**
	 * Return int number as hex and ensure it's of length of 3, padded with zeroes.
	 */
	private static function numpad(int $n): string {
		return str_pad((string) $n, 3, "0", STR_PAD_LEFT);
	}

}
