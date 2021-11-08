<?php

namespace Smuuf\Primi\Stdlib\Modules;

use \Smuuf\Primi\Values\DictValue;
use \Smuuf\Primi\Values\NumberValue;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Helpers\Interned;
use \Smuuf\Primi\Modules\NativeModule;

/**
 * Native 'std.runtime.memory' module.
 */
return new class extends NativeModule {

	/**
	 * _**Only in [CLI](https://w.wiki/QPE)**_.
	 *
	 * Returns memory peak usage used by Primi _(engine behind the scenes)_ in
	 * bytes.
	 *
	 * @primi.function(no-stack)
	 */
	public static function get_peak(): NumberValue {
		return Interned::number((string) \memory_get_peak_usage());
	}

	/**
	 * _**Only in [CLI](https://w.wiki/QPE)**_.
	 *
	 * Returns current memory usage used by Primi _(engine behind the scenes)_
	 * in bytes.
	 *
	 * @primi.function(no-stack)
	 */
	public static function get_current(): NumberValue {
		return Interned::number((string) \memory_get_usage());
	}

	/**
	 * _**Only in [CLI](https://w.wiki/QPE)**_.
	 *
	 * Run PHP garbage collection. Return the number of cycles collected.
	 * See https://www.php.net/manual/en/features.gc.collecting-cycles.php
	 *
	 * @primi.function(no-stack)
	 */
	public static function gc_run(): NumberValue {
		return Interned::number((string) \gc_collect_cycles());
	}

	/**
	 * _**Only in [CLI](https://w.wiki/QPE)**_.
	 *
	 * Get PHP garbage collection stats.
	 * See https://www.php.net/manual/en/function.gc-status.php
	 *
	 * @primi.function(no-stack)
	 */
	public static function gc_status(): DictValue {
		return AbstractValue::buildAuto(\gc_status());
	}

};
