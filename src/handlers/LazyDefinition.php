<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Structures\LazyValue;
use \Smuuf\Primi\Structures\FnContainer;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

class LazyDefinition extends \Smuuf\Primi\StrictObject implements IHandler {

	public static function handle(array $node, Context $context) {

		$fn = FnContainer::build($node['body'], [], $context);
		return new LazyValue($fn);

	}

}
