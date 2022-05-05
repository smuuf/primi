<?php

namespace Smuuf\Primi\Cli;

use \Smuuf\Primi\Helpers\Colors;

abstract class Term {

	public static function line(string $text = ''): string {
		return "$text\n";
	}

	public static function error(string $text): string {
		return Colors::get("{red}Error:{_} ") . "$text\n";
	}

	public static function debug(string $text): string {
		return Colors::get("{yellow}Debug:{_} ") . "$text\n";
	}

	public static function stderr(string $text) {
		\fwrite(\STDERR, $text);
	}

}
