<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

/**
 * Errors that represent some malfunction in Primi interpreter engine. These
 * should not happen - and if they do, it means that Primi itself contains an
 * error (some kind of unhandled edge-case, for example).
 */
class EngineInternalError extends EngineError {

}
