<?php

namespace Smuuf\Primi;

trait WatchLifecycle {

    private $stackCounter = 0;
    private $alreadyVisualized = [];
    private $stack = [];

	public function __construct() {
        $hash = md5(spl_object_hash($this));
        $this->add($hash);
        $visual = $this->visualize();
		echo "Created: $hash $visual\n";
        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
	}

	public function __destruct() {
        $visual = $this->visualize();
        $hash = md5(spl_object_hash($this));
        $this->remove($hash);
		echo "Removed: $hash $visual\n";
        if (method_exists(parent::class, '__destruct')) {
            parent::__destruct();
        }
	}

    private function add($hash) {
        $this->stack[++$this->stackCounter] = $hash;
    }

    private function remove($hash) {
        unset($this->stack[array_search($hash, $this->stack, true)]);
    }

    private function visualize() {
        var_dump($this->stack);
        if (!$this->stack) return "x";
        $max = max(array_keys($this->stack));
        $return = null;
        foreach (range(1, $max) as $pos) {
            // If such position exists in our watching stack, display a character.
            // If this stack item was not displayed yet, display a special character.
            $return .= isset($this->stack[$pos]) ? (isset($this->alreadyVisualized[$pos]) ? "|" : "o") : " ";
            $this->alreadyVisualized[$pos] = true;
        }
        return $return;
    }

}
