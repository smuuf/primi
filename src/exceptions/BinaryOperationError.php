<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

use \Smuuf\Primi\Structures\Value;

class BinaryOperationError extends RuntimeError {

	protected $op;
	protected $left;
	protected $right;

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
