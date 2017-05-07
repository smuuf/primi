<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;

interface IHandler {

	public static function handle(array $node, Context $context);

}