<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

/**
 * Errors that represent runtime errors within the engine, but which might
 * not be necessarily caused by faulty logic in the engine itself. It may be
 * that someone is using Primi in some unexpected or just plainly wrong way.
 */
class EngineError extends EngineException {

}
