<?php

namespace Smuuf\Primi;

abstract class Helpers extends \Smuuf\Primi\StrictObject {

	/**
	 * Takes array as reference and ensures its contents are represented in a form of indexed sub-arrays.
	 * This comes handy if we want to be sure that multiple sub-nodes (which PHP-PEG parser returns) are universally
	 * iterable.
	 */
	public static function ensureIndexed(array &$array): void {

		if (!isset($array[0])) {
			$array = [$array];
		}

	}

}
