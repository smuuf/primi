<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Structures\ValueFriends;

abstract class Extension extends ValueFriends {

	/** @var Context Interpreter context. */
	private $context;

	public function __construct(Context $context) {
		$this->context = $context;
	}

	final protected function getContext(): Context {
		return $this->context;
	}

}
