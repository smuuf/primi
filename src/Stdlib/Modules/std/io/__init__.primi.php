<?php

namespace Smuuf\Primi\Stdlib\Modules;

use Smuuf\Primi\Context;
use Smuuf\Primi\Extensions\PrimiFunc;
use Smuuf\Primi\Helpers\Exceptions;
use Smuuf\Primi\Values\TypeValue;
use Smuuf\Primi\Values\IteratorFactoryValue;
use Smuuf\Primi\Values\StringValue;
use Smuuf\Primi\Stdlib\StaticTypes;
use Smuuf\Primi\Modules\NativeModule;
use Smuuf\Primi\Stdlib\StaticExceptionTypes;
use Smuuf\Primi\Structures\CallArgs;

return new
/**
 * std.io
 */
class extends NativeModule {

	public function execute(Context $ctx): array {

		return [
			'Socket' => new TypeValue(
				'Socket',
				StaticTypes::getObjectType(),
				SocketTypeExtension::execute(),
			),
		];

	}

	#[PrimiFunc(callConv: PrimiFunc::CONV_CALLARGS)]
	public static function open(CallArgs $args, Context $ctx): IteratorFactoryValue {

		$args = $args->extract(['path']);
		$path = $args['path']->getStringValue();

		if (!\file_exists($path) || !\is_readable($path)) {
			Exceptions::piggyback(
				StaticExceptionTypes::getRuntimeErrorType(),
				"Cannot access path '$path'",
			);
		}

		$reader = function() use ($path) {

			$fp = fopen($path, "r");
			while (($line = @stream_get_line($fp, 1024 * 1024, "\n")) !== false) {
				yield new StringValue($line);

			}
			fclose($fp);

		};

		return new IteratorFactoryValue($reader, 'file');

	}

};
