<?php

namespace Smuuf\Primi;

/**
 * Use external context for watching lifecycles, as different classes that
 * use the WatchLifecycle triat don't share the one trait's static properties.
 */
abstract class WatchLifecycleContext {

    public static $stackCounter = 0;
    public static $alreadyVisualized = [];
    public static $stack = [];

}

trait WatchLifecycle {

    private static function hasParents($object) {
        return count(class_parents($object)) === 0;
    }

	public function __construct() {

        $hash = self::getHash($this);
        $this->add($hash);

        $colorhash = self::truecolor($hash, $hash);
        $visual = $this->visualize();
        echo "+  $colorhash $visual\n";

        if (self::hasParents($this) && method_exists(parent::class, '__construct')) {
            parent::__construct();
        }

	}

	public function __destruct() {

        $visual = $this->visualize();
        $hash = self::getHash($this);
        $this->remove($hash);

        $colorhash = self::truecolor($hash, $hash);
        echo " - $colorhash $visual\n";

        if (self::hasParents($this) && method_exists(parent::class, '__destruct')) {
            parent::__destruct();
        }

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

    private static function getHash($object) {
        return substr(md5(get_class($object) . spl_object_hash($object)), 0, 8);
    }

    private static function truecolor(string $hex, string $content) {

        $r = hexdec(substr($hex, 0, 2)) + 32;
        $g = hexdec(substr($hex, 2, 2)) + 32;
        $b = hexdec(substr($hex, 4, 2)) + 32;

        $out = sprintf("\x1b[38;2;%s;%s;%sm", $r, $g, $b);
        $out .= $content . "\033[0m"; // Reset colors.
        return $out;

    }

}
