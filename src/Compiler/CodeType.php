<?php

declare(strict_types=1);

namespace Smuuf\Primi\Compiler;

/**
 * For keeping track of bytecode code type during compilation. E.g. we need
 * to know when we are inside function so we can allow compilation of "return"
 * statement.
 */
enum CodeType {

	case CodeGlobal;
	case CodeFunction;
	case CodeClass;

}
