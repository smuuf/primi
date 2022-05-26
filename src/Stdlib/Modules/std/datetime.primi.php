<?php

declare(strict_types=1);

namespace Smuuf\Primi\Stdlib\Modules;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Stdlib\StaticTypes;
use \Smuuf\Primi\Stdlib\TypeExtensions\Stdlib\DatetimeTypeExtension;
use \Smuuf\Primi\Stdlib\TypeExtensions\Stdlib\DurationTypeExtension;
use \Smuuf\Primi\Values\TypeValue;
use \Smuuf\Primi\Modules\NativeModule;
use \Smuuf\Primi\Modules\AllowedInSandboxTrait;

return new
/**
 * Tools for date and time related operations.
 */
class extends NativeModule {

	use AllowedInSandboxTrait;

	public function execute(Context $ctx): array {

		return [
			'Datetime' => new TypeValue(
				'Datetime',
				StaticTypes::getObjectType(),
				DatetimeTypeExtension::execute(),
			),
			'Duration' => new TypeValue(
				'Duration',
				StaticTypes::getObjectType(),
				DurationTypeExtension::execute(),
			),
		];

	}

};
