<?php

namespace Smuuf\Primi;

class StrictObject {

	/**
	 * Used when trying to access undeclared or inaccessible property.
	 *
	 * @throws \LogicException
	 * @param $name
	 * @return mixed
	 */
	public function __get(string $name) {

		throw new \LogicException(sprintf(
			"Cannot read an undeclared property '%s' in %s.",
			$name,
			get_called_class()
		));

	}

	/**
	 * Used when trying to write to any undeclared or inaccessible property.
	 *
	 * @throws \LogicException
	 * @param $name
	 * @param $value
	 */
	public function __set(string $name, $value) {

		throw new \LogicException(sprintf(
			"Cannot write to an undeclared property '%s' in %s",
			$name,
			get_called_class()
		));

	}

}
