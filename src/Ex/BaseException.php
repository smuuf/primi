<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

abstract class BaseException extends \Exception {

	use \Smuuf\StrictObject;

	// All exceptions thrown by Primi will extend this base exception class.

}
