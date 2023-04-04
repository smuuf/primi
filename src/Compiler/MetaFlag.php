<?php

declare(strict_types=1);

namespace Smuuf\Primi\Compiler;

/**
 * Various types of meta-flags to be used as keys for passing additional
 * information during compilation of AST into bytecode.
 */
enum MetaFlag {

	case TargetNames;
	case BreakJumpTargetLabel;
	case ContinueJumpTargetLabel;
	case ComplexArgs;
	case InLoop;

}
