<?php

namespace Smuuf\Primi;

class ExtensionHub extends \Smuuf\Primi\StrictObject {

	protected static $extensions = [];

	public static function register($extension, string $target = null) {

		// Handle possible group registering.
		if (is_array($extension)) {
			foreach ($extension as $ext => $target) {
				self::register($ext, $target);
			}
			return;
		}

		if (!is_subclass_of($extension, \Smuuf\Primi\Extension::class)) {
			throw new \LogicException("Unable to register '$extension' extension.");
		}

		if (!isset(self::$extensions[$target])) {
			self::$extensions[$target] = [];
		}

		// Make the latest registered extensions to be the first.
		// This enables the client to overload stuff that is already registered.
		array_unshift(self::$extensions[$target], $extension);

	}

	public static function get(string $target) {
		return self::$extensions[$target] ?? [];
	}

}

ExtensionHub::register([
	\Smuuf\Primi\Stl\StringExtension::class => \Smuuf\Primi\Structures\StringValue::class,
	\Smuuf\Primi\Stl\NumberExtension::class => \Smuuf\Primi\Structures\NumberValue::class,
	\Smuuf\Primi\Stl\ArrayExtension::class => \Smuuf\Primi\Structures\ArrayValue::class,
]);
