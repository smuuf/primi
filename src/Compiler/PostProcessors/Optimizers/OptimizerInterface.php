<?php

namespace Smuuf\Primi\Compiler\PostProcessors\Optimizers;

use Smuuf\Primi\Compiler\BytecodeDLL;

interface OptimizerInterface {

	public function optimize(BytecodeDLL $bytecode): bool;

}
