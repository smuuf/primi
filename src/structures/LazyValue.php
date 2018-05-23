<?php

namespace Smuuf\Primi\Structures;

class LazyValue extends FuncValue {

	const TYPE = "lazy";

	public function getStringValue(): string {
		return "__lazy__";
	}

	public function resolve() {
		return $this->invoke();
	}

}
