<?php

namespace Smuuf\Primi\Cli;

use \Smuuf\Primi\Helpers\Colors;

abstract class Term {

	/**
	 * @param string $text
	 * @return string
	 */
	public static function line($text = '') {
		return "$text\n";
	}

	/**
	 * @param string $text
	 * @return string
	 */
	public static function error($text) {
		return Colors::get("{red}Error:{_} ") . "$text\n";
	}

	/**
	 * @param string $text
	 * @return string
	 */
	public static function debug($text) {
		return Colors::get("{yellow}Debug:{_} ") . "$text\n";
	}

}
