<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

class EchoStatement extends \Smuuf\Primi\Object implements IHandler {

	public static function handle(array $node, Context $context) {

		$handler = HandlerFactory::get($node['subject']['name']);
		$value = $handler::handle($node['subject'], $context);

		if (is_scalar($value)) {
			echo $value;
		} else {
			print_r($value);
		}

	}

}
