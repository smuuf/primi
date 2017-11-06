<?php

namespace Smuuf\Primi;

/**
 * Use external context for watching lifecycles, as different classes that
 * use the WatchLifecycle triat don't share the one trait's static properties.
 */
abstract class WatchLifecycleContext {

    public static $instanceCounter = 0;
    public static $stackCounter = 0;
    public static $alreadyVisualized = [];
    public static $stack = [];

}

/**
 * Currently supports only classes without additional parent constructors/destructors.
 */
trait WatchLifecycle {

    /** @var int Every unique instance of "watched" class's number. */
    private static $instanceCounter;

    public function __construct() {

        // Assign a new, globally unique counter's number for this new object.
        self::$instanceCounter = WatchLifecycleContext::$instanceCounter++;

        // Build a completely unique hash for this object's instance.
        $hash = self::getHash($this);

        // Add this object to global watch stack.
        $this->add($hash);

        // Build a unique true color for this instance.
        $colorhash = self::truecolor($hash, $hash);
        $visual = $this->visualize();

        // Visualize newly created object with a pretty sign.
        echo "+  $colorhash $visual\n";

	}

	public function __destruct() {

        // Build visualisation string before removing this object from watched stack.
        $visual = $this->visualize();

        // Remove this object from global watch stack.
        $hash = self::getHash($this);
        $this->remove($hash);

        $colorhash = self::truecolor($hash, $hash);
        echo " - $colorhash $visual\n";

	}

    private function add($hash) {
        WatchLifecycleContext::$stack[++WatchLifecycleContext::$stackCounter] = $hash;
    }

    private function remove($hash) {
        unset(WatchLifecycleContext::$stack[array_search($hash, WatchLifecycleContext::$stack, true)]);
    }

    private function visualize() {

        if (!WatchLifecycleContext::$stack) return "x";

        $max = max(array_keys(WatchLifecycleContext::$stack));
        $return = null;

        foreach (range(1, $max) as $pos) {

            // If such position exists in our watching stack, display a character.
            // If this stack item was not displayed yet, display a special character.
            $return .= isset(WatchLifecycleContext::$stack[$pos])
                ? (isset(WatchLifecycleContext::$alreadyVisualized[$pos]) ? "|" : "o")
                : " ";
            WatchLifecycleContext::$alreadyVisualized[$pos] = true;

        }

        return $return;

    }

    /**
     * Return a completely unique hash for this object's instance.
     * Uniqueness is based off a 1) global counter, 2) class name, 3) spl_object_hash.
     *
     * Now that I'm thinking about it, the "1)" should be enough, but, well...
     */
    private static function getHash($object): string {
        return substr(md5(self::$instanceCounter . get_class($object) . spl_object_hash($object)), 0, 8);
    }

    private static function truecolor(string $hex, string $content) {

        $r = self::hexnumpad(substr($hex, 0, 2) + 32);
        $g = self::hexnumpad(substr($hex, 2, 2) + 32);
        $b = self::hexnumpad(substr($hex, 4, 2) + 32);

        $out = sprintf("\x1b[38;2;%s;%s;%sm", $r, $g, $b);
        $out .= $content . "\033[0m"; // Reset colors.
        return $out;

    }

    /**
     * Return int number as hex and ensure it's of length of 3, padded with zeroes.
     */
    private static function hexnumpad(int $n) {
        return str_pad(hexdec($n), 3, "0", STR_PAD_LEFT);
    }

}
