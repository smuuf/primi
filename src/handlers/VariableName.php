<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;

class VariableName extends \Smuuf\Primi\Object implements IHandler {

	public static function handle(array $node, Context $context) {
		return $node['text'];
	}

}
