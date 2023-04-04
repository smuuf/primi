<?php

namespace Smuuf\Primi\Compiler\PostProcessors;

use Smuuf\Primi\Compiler\BytecodeDLL;

interface PostProcessorInterface {

	public function process(BytecodeDLL $bytecode): void;

}
