<?php

namespace Smuuf\Primi\Structures;

abstract class Value extends \Smuuf\Primi\Object {

	const TYPE_NUMBER = 0x01;
	const TYPE_STRING = 0x02;
	const TYPE_BOOL = 0x03;
	const TYPE_ARRAY = 0x04;
	const TYPE_REGEX = 0x05;

	/** @var mixed Value **/
	protected $value;

	public function getPhpValue() {
		return $this->value;
	}

}
