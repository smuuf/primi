<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\Structures\Value;

class InternalBinaryOperationxception extends InternalException {

	protected $passedCount;
	protected $expectedCount;

	public function __construct(
		string $op,
		Value $left,
		Value $right
	) {

		parent::__construct();

		$this->op = $op;
		$this->left = $left;
		$this->right = $right;

	}

	public function getOperator() {
		return $this->op;
	}

	public function getLeft() {
		return $this->left;
	}

	public function getRight() {
		return $this->right;
	}

}
