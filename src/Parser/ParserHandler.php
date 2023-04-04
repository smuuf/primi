<?php

declare(strict_types=1);

namespace Smuuf\Primi\Parser;

use Smuuf\Primi\Ex\InternalSyntaxError;
use Smuuf\Primi\Parser\Compiled\PrimiParser;
use Smuuf\Primi\Helpers\Func;
use Smuuf\Primi\Helpers\Timer;
use Smuuf\Primi\Handlers\HandlerFactory;

class ParserHandler {

	/** Primi source code as string that is to be parsed and executed. */
	private string $source;

	/** @var array<string, scalar> Parser statistics. */
	private array $stats = [];

	/** Compiled parser object. */
	private PrimiParser $parser;

	public function __construct(string $source) {

		$class = ParserFactory::getParserClass();
		$source = self::sanitizeSource($source);

		$this->parser = new $class($source);
		$this->source = $source;

	}

	/**
	 * Return parser stats.
	 *
	 * @return array<string, float>
	 */
	public function getStats(): array {
		return $this->stats;
	}

	/**
	 * @return array
	 * @phpstan-return TypeDef_AstNode
	 */
	public function run(): array {

		$t = (new Timer)->start();
		$result = $this->parser->match_Program();
		$this->stats['Parsing AST'] = $t->get();

		if ($result['text'] !== $this->source) {

			$farthestRuleLabel = GrammarRulesLabels::getRuleLabel(
				$this->parser->getFarthestRule(),
			);

			$reason = $farthestRuleLabel
				? "after $farthestRuleLabel"
				: \null;

			throw new InternalSyntaxError(
				$this->parser->getFarthestPos(),
				$reason,
			);

		}

		$t = (new Timer)->start();
		$processed = $this->processAST($result);
		$this->stats['AST postprocess total'] = $t->get();

		return $processed;

	}

	private static function sanitizeSource(string $s): string {

		// Unify new-lines.
		$s = \str_replace("\r\n", "\n", $s);

		// Ensure newline at the end (parser needs this to be able to correctly
		// parse comments in one line source codes.)
		return \rtrim($s) . "\n";

	}

	/**
	 * @param array $ast
	 * @phpstan-param TypeDef_AstNode $ast
	 * @return TypeDef_AstNode $ast
	 */
	private function processAST(array $ast): array {

		$t = (new Timer)->start();
		self::preprocessNode($ast);
		$this->stats['AST nodes preprocessing'] = $t->get();

		return $ast;

	}

	/**
	 * Go recursively through each of the nodes and strip unnecessary data
	 * in the abstract syntax tree.
	 *
	 * @param array $node
	 * @phpstan-param TypeDef_AstNode $node
	 * @throws InternalSyntaxError
	 */
	private function preprocessNode(array &$node): void {

		// Add information about the node's offset (line & position) for
		// later use (e.g. for informative error messages).
		if (isset($node['offset'])) {

			[$line, $pos] = Func::get_position_estimate(
				$this->source,
				$node['offset'],
			);

			$node['_l'] = $line;
			$node['_p'] = $pos;

		}

		// If node has "skip" node defined, replace the whole node with the
		// "skip" subnode.
		unset($node['_matchrule']);
		foreach ($node as &$item) {

			while ($inner = ($item['skip'] ?? \false)) {
				$item = $inner;
			}

			if (\is_array($item)) {
				self::preprocessNode($item);
			}

		}

		if (!isset($node['name'])) {
			return;
		}

		if (!$handler = HandlerFactory::tryGetFor($node['name'])) {
			return;
		}

		// If a handler knows how to reduce its node, let it.
		$handler::reduce($node);

	}

}
