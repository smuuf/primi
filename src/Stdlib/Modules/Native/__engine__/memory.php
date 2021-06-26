<?php

namespace Smuuf\Primi\Stdlib\Modules;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Values\DictValue;
use \Smuuf\Primi\Values\NullValue;
use \Smuuf\Primi\Values\NumberValue;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Extensions\Module;

/**
 * Native 'time' module.
 */
return new class extends Module {

	public function execute(Context $ctx): array {

		return [
			'get_current' => [self::class, 'get_current'],
			'get_peak' => [self::class, 'get_peak'],
			'gc_run' => [self::class, 'gc_run'],
			'gc_status' => [self::class, 'gc_status'],
		];

	}

	/**
	 * _**Only in [CLI](https://w.wiki/QPE)**_.
	 *
	 * Returns memory peak usage used by Primi _(engine behind the scenes)_ in
	 * bytes.
	 */
	public static function get_peak(): NumberValue {
		return NumberValue::build((string) \memory_get_peak_usage());
	}

	/**
	 * _**Only in [CLI](https://w.wiki/QPE)**_.
	 *
	 * Returns current memory usage used by Primi _(engine behind the scenes)_
	 * in bytes.
	 */
	public static function get_current(): NumberValue {
		return NumberValue::build((string) \memory_get_usage());
	}

	/**
	 * _**Only in [CLI](https://w.wiki/QPE)**_.
	 *
	 * Run PHP garbage collection. Return the number of cycles collected.
	 * See https://www.php.net/manual/en/features.gc.collecting-cycles.php
	 */
	public static function gc_run(): NullValue {
		return NullValue::build((string) \gc_collect_cycles());
	}

	/**
	 * _**Only in [CLI](https://w.wiki/QPE)**_.
	 *
	 * Get PHP garbage collection stats.
	 * See https://www.php.net/manual/en/function.gc-status.php
	 */
	public static function gc_status(): DictValue {
		return AbstractValue::buildAuto(\gc_status());
	}

};
