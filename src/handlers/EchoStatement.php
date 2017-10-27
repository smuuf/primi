<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

class EchoStatement extends \Smuuf\Primi\StrictObject implements IHandler {

	public static function handle(array $node, Context $context) {

		$handler = HandlerFactory::get($node['subject']['name']);
		$returned = $handler::handle($node['subject'], $context);
		$value = $returned->getPhpValue();

		if (\is_scalar($value)) {
			echo $value;
		} else {
			print_r($value);
		}

	}

}
