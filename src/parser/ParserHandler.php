<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\Handlers\IReducer;

use \hafriedlander\Peg\Parser;

class ParserHandler extends CompiledParser {

	const RESERVED_WORDS = [
		'echo',
	];

	private $source;
	protected $tree = [];

	// Begin.

	public function Program__construct(&$result) {
		$result['nodes'] = [];
	}

	public function Program_Statement(&$result, $s) {
		$result['nodes'][] = $s;
	}

	public function VariableCore__finalise(&$result) {
		if (\in_array($result['text'], self::RESERVED_WORDS)) {
			$this->error(sprintf("Syntax error: '%s' is a reserved word", $result['text']), $this->pos);
		}
	}

	// End.

	public function __construct($source) {

		$source = self::sanitizeSource($source);

		parent::__construct($source);
		$this->source = $source;

	}

	public function run(): array {

		$result = $this->match_Program();
		if ($result['text'] !== $this->source) {

			// $this->pos is an internal PEG Parser position counter.
			$this->error('Syntax error', $this->pos);

		}

		return self::processAST($result, $this->source);

	}

	protected function error($msg, $position = \false) {

		$line = \false;
		$pos = \false;

		if ($position !== \false) {
			list($line, $pos) = Common::getPositionEstimate($this->source, $position);
		}

		throw new SyntaxErrorException($msg, $line, $pos);

	}

	protected static function sanitizeSource(string $s) {

		// Unify new-lines.
		$s = \preg_replace('#(\r\n)#u', "\n", $s);

		// Ensure newline at the end (parser needs this to be able to correctly
		// parse comments in one line source codes.)
		return rtrim($s) . "\n";

	}

	protected static function processAST(array $ast, string $source): array {

		$ast = self::reduceNode($ast);
		$ast = self::addPositions($ast, $source);

		return $ast;

	}

	/**
	 * Go recursively through each of the nodes and strip unecessary data
	 * in the abstract syntax tree.
	 */
	protected static function reduceNode(array $node) {

		static $reducers = [];

		// If node has "skip" node defined, replace the whole node with the
		// "skip" subnode.
		if (isset($node['skip'])) {
			return self::reduceNode($node['skip']);
		}

		$name = $node['name'] ?? false;
		if ($name !== false) {

			// We have a reducer existence state saved in cache.
			if (isset($reducers[$name])) {

				// Reducer does really exists.
				if ($reducers[$name] !== false) {
					if ($reduced = $reducers[$name]::reduce($node)) {
						return self::reduceNode($reduced);
					}
				}

			} else {

				$handler = HandlerFactory::get($name);
				$reducers[$name] = false;

				if (\is_subclass_of($handler, IReducer::class)) {
					$reducers[$name] = $handler;
					if ($reduced = $handler::reduce($node)) {
						return self::reduceNode($reduced);
					}
				}

			}

		}

		unset($node['_matchrule']);

		foreach ($node as &$item) {
			if (is_array($item)) {
				$item = self::reduceNode($item);
			}
		}

		return $node;

	}

	/**
	 * Recursively iterate the node and its children and add information about
	 * the node's offset (line & position) for later (e.g. error messages).
	 */
	protected static function addPositions(array $node, string $source): array {

		if (isset($node['offset'])) {

			list($line, $pos) = Common::getPositionEstimate($source, $node['offset']);
			$node['line'] = $line;
			$node['pos'] = $pos;

		}

		foreach ($node as $k => &$v) {
			if (\is_array($v)) {
				$v = self::addPositions($v, $source);
			}
		}

		return $node;

	}

}
