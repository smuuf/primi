<?php

namespace Smuuf\Primi\Tasks;

use Smuuf\Primi\Context;

interface TaskInterface {

	public function execute(Context $ctx): void;

}
