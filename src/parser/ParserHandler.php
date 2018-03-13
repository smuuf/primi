<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\HandlerFactory;
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
			list($line, $pos) = Helpers::getPositionEstimate($this->source, $position);
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

		$ast = self::reduceAST($ast, \true);
		$ast = self::addPositions($ast, $source);

		return $ast;

	}

	/**
	 * Go recursively through each of the node elements and if ...
	 * 1) Node contains 'skip' element ---> replace the node with the contents of that 'skip' element.
	 * 2) Node contains 'skip' element ---> replace the node with the contents of that 'skip' element.
	 * 3) Node's elements contains nested nodes ---> reduce them too.
	 * 4) Aggressive mode is enabled ---> remove unnecessary elements created by PHP-PEG parser itself.
	 */
	protected static function reduceAST(array $node, $aggressive = false): array {

		static $aggresivelyRemove = ['_matchrule'];

		// Case 1)
		if (isset($node['skip'])) {
			return self::reduceAST($node['skip'], $aggressive);
		}

		// Allow each type of handler handle its own reduction.
		if (
			isset($node['name'])
			&& ($handler = HandlerFactory::get($node['name']))
			&& \is_subclass_of($handler, \Smuuf\Primi\Handlers\IReducer::class)
		) {
			if ($reduced = $handler::reduce($node)) {
				return self::reduceAST($reduced, $aggressive);
			}
		}

		foreach ($node as $k => &$v) {

			if (\is_array($v)) {
				$v = self::reduceAST($v, $aggressive);
			}

			if (!$aggressive) continue;
			if (\in_array($k, $aggresivelyRemove, \true)) {
				unset($node[$k]);
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

			list($line, $pos) = Helpers::getPositionEstimate($source, $node['offset']);
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
