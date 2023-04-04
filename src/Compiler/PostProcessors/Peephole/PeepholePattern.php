<?php

declare(strict_types=1);

namespace Smuuf\Primi\Compiler\PostProcessors\Peephole;

use Smuuf\Primi\Ex\EngineInternalError;
use Smuuf\Primi\Compiler\BytecodeDLL;

class PeepholePattern {

	use \Smuuf\StrictObject;

	/**
	 * @var array<int, PatternRule>
	 */
	private array $rules = [];

	public function add(
		string|array $opType = [],
		?\Closure $filter = null,
		?\Closure $onMatch = null,
		bool $many = false,
	): self{
		$this->rules[] = new PatternRule($opType, $filter, $onMatch, $many);
		return $this;
	}

	private function getRule(int $index): PatternRule {
		return $this->rules[$index]
			?? throw new EngineInternalError("Pattern rule $index not found");
	}

	public function scan(BytecodeDLL $bytecode): \Generator {

		// A "meta" storage array we pass into "filter" and "onMatch" callbacks
		// when we call them, so the pattern rules can access and modify it
		// for their needs.
		$storage = [];

		// Buffer we fill up with consecutive opcodes which will constitute a
		// matched pattern.
		$buffer = [];

		$ruleIndex = 0;
		$finalRuleIndex = count($this->rules) - 1;
		$rule = $this->getRule($ruleIndex);

		$bytecode->rewind();
		while ($bytecode->valid()) {

			$op = $bytecode->current();
			$i = $bytecode->key();

			if ($rule->tryMatch($op, $storage)) {

				$buffer[] = $op;

				// We're successfully matching rules of the pattern, but
				// we're not done with the whole pattern. Go look for the rest.
				if ($ruleIndex !== $finalRuleIndex) {

					// If the rule is expected many times (more than once),
					// don't advance the rule index.
					if (!$rule->many) {
						$ruleIndex++;
					}

					$bytecode->next();
					$rule = $this->getRule($ruleIndex);
					continue;

				}

				// When a complete match is found, yield back a function the
				// client can call with their own callback, which should
				// return an array (or null) we're going to use as a replacement
				// within the found matching portion of the bytecode.
				yield static function(callable $callback) use (
					$bytecode,
					$i,
					$buffer,
				): void {

					$replacement = $callback($buffer, $i);
					if ($replacement !== null) {

						$bufferLength = count($buffer);
						$foundIndex = $i - $bufferLength + 1;
						$bytecode->splice(
							$foundIndex,
							$bufferLength,
							$replacement,
						);

						// After we modified the DLL via splicing we need to
						// rewind and seek to the position where we want
						// to start our next search.
						$bytecode->seek($foundIndex + count($replacement) - 1);

					}

				};

			} elseif ($buffer && $rule->many) {

				// If the rule didn't match, but it's flagged as "many", we
				// might have simply encountered something that should be
				// matched by the next rule, so we'll try again the same op with
				// the next rule.
				// And do this only if the "many" rule actually matched at
				// least one op (we know that's the case if the buffer is not
				// empty).
				$ruleIndex++;
				$rule = $this->getRule($ruleIndex);
				continue;

			} else {

				// If we just failed our potential match (we can identify that
				// situation because our buffer is not empty) and the matching
				// ultimately failed - and we're gonna need to start matching
				// again from the start of our pattern, we need to rewind the
				// iterator back a bit, so we can try again the item we've just
				// consumed (it might be our first item of our next potential
				// match).
				if ($buffer) {
					$bytecode->seek($i - count($buffer));
				}

			}

			// Reset everything and try again with next opcodes.
			$storage = [];
			$buffer = [];
			$ruleIndex = 0;
			$bytecode->next();
			$rule = $this->getRule($ruleIndex);

		}

	}

}
