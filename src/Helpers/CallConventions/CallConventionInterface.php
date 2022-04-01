<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers\CallConventions;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Structures\CallArgs;

interface CallConventionInterface {

	public function call(CallArgs $args, Context $ctx): ?AbstractValue;

}

