<?php

namespace Smuuf\Primi;

use hafriedlander\Peg\Parser;

class CompiledParser extends Parser\Packrat {

	// Add these properties so PHPStan doesn't complain about undefined properties.

	/** @var int **/
	public $pos;

	/** @var string **/
	public $string;

/* StringLiteral: / (?:".*?(?<!\\)")|(?:'.*?(?<!\\)') /s */
protected $match_StringLiteral_typestack = array('StringLiteral');
function match_StringLiteral ($stack = []) {
	$matchrule = "StringLiteral"; $result = $this->construct($matchrule, $matchrule, \null);
	if (( $subres = $this->rx( '/ (?:".*?(?<!\\\\)")|(?:\'.*?(?<!\\\\)\') /s' ) ) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* NumberLiteral: /-?\d+(\.\d+)?/ */
protected $match_NumberLiteral_typestack = array('NumberLiteral');
function match_NumberLiteral ($stack = []) {
	$matchrule = "NumberLiteral"; $result = $this->construct($matchrule, $matchrule, \null);
	if (( $subres = $this->rx( '/-?\d+(\.\d+)?/' ) ) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* BoolLiteral: "true" | "false" */
protected $match_BoolLiteral_typestack = array('BoolLiteral');
function match_BoolLiteral ($stack = []) {
	$matchrule = "BoolLiteral"; $result = $this->construct($matchrule, $matchrule, \null);
	$_5 = \null;
	do {
		$res_2 = $result;
		$pos_2 = $this->pos;
		if (( $subres = $this->literal( 'true' ) ) !== \false) {
			$result["text"] .= $subres;
			$_5 = \true; break;
		}
		$result = $res_2;
		$this->pos = $pos_2;
		if (( $subres = $this->literal( 'false' ) ) !== \false) {
			$result["text"] .= $subres;
			$_5 = \true; break;
		}
		$result = $res_2;
		$this->pos = $pos_2;
		$_5 = \false; break;
	}
	while(0);
	if( $_5 === \true ) { return $this->finalise($result); }
	if( $_5 === \false) { return \false; }
}


/* RegexLiteral: "/" /(\\\/|[^\/])+/ "/" */
protected $match_RegexLiteral_typestack = array('RegexLiteral');
function match_RegexLiteral ($stack = []) {
	$matchrule = "RegexLiteral"; $result = $this->construct($matchrule, $matchrule, \null);
	$_10 = \null;
	do {
		if (\substr($this->string,$this->pos,1) === '/') {
			$this->pos += 1;
			$result["text"] .= '/';
		}
		else { $_10 = \false; break; }
		if (( $subres = $this->rx( '/(\\\\\/|[^\/])+/' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_10 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '/') {
			$this->pos += 1;
			$result["text"] .= '/';
		}
		else { $_10 = \false; break; }
		$_10 = \true; break;
	}
	while(0);
	if( $_10 === \true ) { return $this->finalise($result); }
	if( $_10 === \false) { return \false; }
}


/* Literal: skip:NumberLiteral | skip:StringLiteral | skip:BoolLiteral | skip:RegexLiteral */
protected $match_Literal_typestack = array('Literal');
function match_Literal ($stack = []) {
	$matchrule = "Literal"; $result = $this->construct($matchrule, $matchrule, \null);
	$_23 = \null;
	do {
		$res_12 = $result;
		$pos_12 = $this->pos;
		$matcher = 'match_'.'NumberLiteral'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
			$_23 = \true; break;
		}
		$result = $res_12;
		$this->pos = $pos_12;
		$_21 = \null;
		do {
			$res_14 = $result;
			$pos_14 = $this->pos;
			$matcher = 'match_'.'StringLiteral'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "skip" );
				$_21 = \true; break;
			}
			$result = $res_14;
			$this->pos = $pos_14;
			$_19 = \null;
			do {
				$res_16 = $result;
				$pos_16 = $this->pos;
				$matcher = 'match_'.'BoolLiteral'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "skip" );
					$_19 = \true; break;
				}
				$result = $res_16;
				$this->pos = $pos_16;
				$matcher = 'match_'.'RegexLiteral'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "skip" );
					$_19 = \true; break;
				}
				$result = $res_16;
				$this->pos = $pos_16;
				$_19 = \false; break;
			}
			while(0);
			if( $_19 === \true ) { $_21 = \true; break; }
			$result = $res_14;
			$this->pos = $pos_14;
			$_21 = \false; break;
		}
		while(0);
		if( $_21 === \true ) { $_23 = \true; break; }
		$result = $res_12;
		$this->pos = $pos_12;
		$_23 = \false; break;
	}
	while(0);
	if( $_23 === \true ) { return $this->finalise($result); }
	if( $_23 === \false) { return \false; }
}


/* VariableName: /([a-zA-Z_][a-zA-Z0-9_]*)/ */
protected $match_VariableName_typestack = array('VariableName');
function match_VariableName ($stack = []) {
	$matchrule = "VariableName"; $result = $this->construct($matchrule, $matchrule, \null);
	if (( $subres = $this->rx( '/([a-zA-Z_][a-zA-Z0-9_]*)/' ) ) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* Variable: ( core:VariableName post:UnaryOperator? ) | ( pre:UnaryOperator? core:VariableName ) */
protected $match_Variable_typestack = array('Variable');
function match_Variable ($stack = []) {
	$matchrule = "Variable"; $result = $this->construct($matchrule, $matchrule, \null);
	$_35 = \null;
	do {
		$res_26 = $result;
		$pos_26 = $this->pos;
		$_29 = \null;
		do {
			$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "core" );
			}
			else { $_29 = \false; break; }
			$res_28 = $result;
			$pos_28 = $this->pos;
			$matcher = 'match_'.'UnaryOperator'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "post" );
			}
			else {
				$result = $res_28;
				$this->pos = $pos_28;
				unset( $res_28 );
				unset( $pos_28 );
			}
			$_29 = \true; break;
		}
		while(0);
		if( $_29 === \true ) { $_35 = \true; break; }
		$result = $res_26;
		$this->pos = $pos_26;
		$_33 = \null;
		do {
			$res_31 = $result;
			$pos_31 = $this->pos;
			$matcher = 'match_'.'UnaryOperator'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "pre" );
			}
			else {
				$result = $res_31;
				$this->pos = $pos_31;
				unset( $res_31 );
				unset( $pos_31 );
			}
			$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "core" );
			}
			else { $_33 = \false; break; }
			$_33 = \true; break;
		}
		while(0);
		if( $_33 === \true ) { $_35 = \true; break; }
		$result = $res_26;
		$this->pos = $pos_26;
		$_35 = \false; break;
	}
	while(0);
	if( $_35 === \true ) { return $this->finalise($result); }
	if( $_35 === \false) { return \false; }
}


/* PropertyGetter: core:VariableName */
protected $match_PropertyGetter_typestack = array('PropertyGetter');
function match_PropertyGetter ($stack = []) {
	$matchrule = "PropertyGetter"; $result = $this->construct($matchrule, $matchrule, \null);
	$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
	$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
	if ($subres !== \false) {
		$this->store( $result, $subres, "core" );
		return $this->finalise($result);
	}
	else { return \false; }
}


/* AnonymousFunction: "function" > "(" > args:FunctionDefinitionArgumentList? > ")" > body:Block | "(" > args:FunctionDefinitionArgumentList? > ")" > "=>" > body:Block */
protected $match_AnonymousFunction_typestack = array('AnonymousFunction');
function match_AnonymousFunction ($stack = []) {
	$matchrule = "AnonymousFunction"; $result = $this->construct($matchrule, $matchrule, \null);
	$_61 = \null;
	do {
		$res_38 = $result;
		$pos_38 = $this->pos;
		$_48 = \null;
		do {
			if (( $subres = $this->literal( 'function' ) ) !== \false) { $result["text"] .= $subres; }
			else { $_48 = \false; break; }
			if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
			if (\substr($this->string,$this->pos,1) === '(') {
				$this->pos += 1;
				$result["text"] .= '(';
			}
			else { $_48 = \false; break; }
			if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
			$res_43 = $result;
			$pos_43 = $this->pos;
			$matcher = 'match_'.'FunctionDefinitionArgumentList'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "args" );
			}
			else {
				$result = $res_43;
				$this->pos = $pos_43;
				unset( $res_43 );
				unset( $pos_43 );
			}
			if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
			if (\substr($this->string,$this->pos,1) === ')') {
				$this->pos += 1;
				$result["text"] .= ')';
			}
			else { $_48 = \false; break; }
			if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "body" );
			}
			else { $_48 = \false; break; }
			$_48 = \true; break;
		}
		while(0);
		if( $_48 === \true ) { $_61 = \true; break; }
		$result = $res_38;
		$this->pos = $pos_38;
		$_59 = \null;
		do {
			if (\substr($this->string,$this->pos,1) === '(') {
				$this->pos += 1;
				$result["text"] .= '(';
			}
			else { $_59 = \false; break; }
			if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
			$res_52 = $result;
			$pos_52 = $this->pos;
			$matcher = 'match_'.'FunctionDefinitionArgumentList'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "args" );
			}
			else {
				$result = $res_52;
				$this->pos = $pos_52;
				unset( $res_52 );
				unset( $pos_52 );
			}
			if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
			if (\substr($this->string,$this->pos,1) === ')') {
				$this->pos += 1;
				$result["text"] .= ')';
			}
			else { $_59 = \false; break; }
			if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
			if (( $subres = $this->literal( '=>' ) ) !== \false) { $result["text"] .= $subres; }
			else { $_59 = \false; break; }
			if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "body" );
			}
			else { $_59 = \false; break; }
			$_59 = \true; break;
		}
		while(0);
		if( $_59 === \true ) { $_61 = \true; break; }
		$result = $res_38;
		$this->pos = $pos_38;
		$_61 = \false; break;
	}
	while(0);
	if( $_61 === \true ) { return $this->finalise($result); }
	if( $_61 === \false) { return \false; }
}


/* ArrayItem: ( key:Expression __ ":" )? __ value:Expression ) */
protected $match_ArrayItem_typestack = array('ArrayItem');
function match_ArrayItem ($stack = []) {
	$matchrule = "ArrayItem"; $result = $this->construct($matchrule, $matchrule, \null);
	$_70 = \null;
	do {
		$res_67 = $result;
		$pos_67 = $this->pos;
		$_66 = \null;
		do {
			$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "key" );
			}
			else { $_66 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_66 = \false; break; }
			if (\substr($this->string,$this->pos,1) === ':') {
				$this->pos += 1;
				$result["text"] .= ':';
			}
			else { $_66 = \false; break; }
			$_66 = \true; break;
		}
		while(0);
		if( $_66 === \false) {
			$result = $res_67;
			$this->pos = $pos_67;
			unset( $res_67 );
			unset( $pos_67 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_70 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "value" );
		}
		else { $_70 = \false; break; }
		$_70 = \true; break;
	}
	while(0);
	if( $_70 === \true ) { return $this->finalise($result); }
	if( $_70 === \false) { return \false; }
}


/* ArrayDefinition: "[" __ ( items:ArrayItem ( __ "," __ items:ArrayItem )* )? __ ( "," __ )? "]" */
protected $match_ArrayDefinition_typestack = array('ArrayDefinition');
function match_ArrayDefinition ($stack = []) {
	$matchrule = "ArrayDefinition"; $result = $this->construct($matchrule, $matchrule, \null);
	$_89 = \null;
	do {
		if (\substr($this->string,$this->pos,1) === '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_89 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_89 = \false; break; }
		$res_82 = $result;
		$pos_82 = $this->pos;
		$_81 = \null;
		do {
			$matcher = 'match_'.'ArrayItem'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "items" );
			}
			else { $_81 = \false; break; }
			while (\true) {
				$res_80 = $result;
				$pos_80 = $this->pos;
				$_79 = \null;
				do {
					$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) { $this->store( $result, $subres ); }
					else { $_79 = \false; break; }
					if (\substr($this->string,$this->pos,1) === ',') {
						$this->pos += 1;
						$result["text"] .= ',';
					}
					else { $_79 = \false; break; }
					$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) { $this->store( $result, $subres ); }
					else { $_79 = \false; break; }
					$matcher = 'match_'.'ArrayItem'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "items" );
					}
					else { $_79 = \false; break; }
					$_79 = \true; break;
				}
				while(0);
				if( $_79 === \false) {
					$result = $res_80;
					$this->pos = $pos_80;
					unset( $res_80 );
					unset( $pos_80 );
					break;
				}
			}
			$_81 = \true; break;
		}
		while(0);
		if( $_81 === \false) {
			$result = $res_82;
			$this->pos = $pos_82;
			unset( $res_82 );
			unset( $pos_82 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_89 = \false; break; }
		$res_87 = $result;
		$pos_87 = $this->pos;
		$_86 = \null;
		do {
			if (\substr($this->string,$this->pos,1) === ',') {
				$this->pos += 1;
				$result["text"] .= ',';
			}
			else { $_86 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_86 = \false; break; }
			$_86 = \true; break;
		}
		while(0);
		if( $_86 === \false) {
			$result = $res_87;
			$this->pos = $pos_87;
			unset( $res_87 );
			unset( $pos_87 );
		}
		if (\substr($this->string,$this->pos,1) === ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_89 = \false; break; }
		$_89 = \true; break;
	}
	while(0);
	if( $_89 === \true ) { return $this->finalise($result); }
	if( $_89 === \false) { return \false; }
}


/* Value: skip:Literal | skip:Variable | skip:ArrayDefinition */
protected $match_Value_typestack = array('Value');
function match_Value ($stack = []) {
	$matchrule = "Value"; $result = $this->construct($matchrule, $matchrule, \null);
	$_98 = \null;
	do {
		$res_91 = $result;
		$pos_91 = $this->pos;
		$matcher = 'match_'.'Literal'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
			$_98 = \true; break;
		}
		$result = $res_91;
		$this->pos = $pos_91;
		$_96 = \null;
		do {
			$res_93 = $result;
			$pos_93 = $this->pos;
			$matcher = 'match_'.'Variable'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "skip" );
				$_96 = \true; break;
			}
			$result = $res_93;
			$this->pos = $pos_93;
			$matcher = 'match_'.'ArrayDefinition'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "skip" );
				$_96 = \true; break;
			}
			$result = $res_93;
			$this->pos = $pos_93;
			$_96 = \false; break;
		}
		while(0);
		if( $_96 === \true ) { $_98 = \true; break; }
		$result = $res_91;
		$this->pos = $pos_91;
		$_98 = \false; break;
	}
	while(0);
	if( $_98 === \true ) { return $this->finalise($result); }
	if( $_98 === \false) { return \false; }
}


/* DereferencableValue: core:Value ( "[" __ dereference:Expression __ "]" )* */
protected $match_DereferencableValue_typestack = array('DereferencableValue');
function match_DereferencableValue ($stack = []) {
	$matchrule = "DereferencableValue"; $result = $this->construct($matchrule, $matchrule, \null);
	$_108 = \null;
	do {
		$matcher = 'match_'.'Value'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "core" );
		}
		else { $_108 = \false; break; }
		while (\true) {
			$res_107 = $result;
			$pos_107 = $this->pos;
			$_106 = \null;
			do {
				if (\substr($this->string,$this->pos,1) === '[') {
					$this->pos += 1;
					$result["text"] .= '[';
				}
				else { $_106 = \false; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_106 = \false; break; }
				$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "dereference" );
				}
				else { $_106 = \false; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_106 = \false; break; }
				if (\substr($this->string,$this->pos,1) === ']') {
					$this->pos += 1;
					$result["text"] .= ']';
				}
				else { $_106 = \false; break; }
				$_106 = \true; break;
			}
			while(0);
			if( $_106 === \false) {
				$result = $res_107;
				$this->pos = $pos_107;
				unset( $res_107 );
				unset( $pos_107 );
				break;
			}
		}
		$_108 = \true; break;
	}
	while(0);
	if( $_108 === \true ) { return $this->finalise($result); }
	if( $_108 === \false) { return \false; }
}


/* VariableVector: core:Variable ( "[" > ( vector:Expression | vector:"" ) > "]" )+ */
protected $match_VariableVector_typestack = array('VariableVector');
function match_VariableVector ($stack = []) {
	$matchrule = "VariableVector"; $result = $this->construct($matchrule, $matchrule, \null);
	$_124 = \null;
	do {
		$matcher = 'match_'.'Variable'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "core" );
		}
		else { $_124 = \false; break; }
		$count = 0;
		while (\true) {
			$res_123 = $result;
			$pos_123 = $this->pos;
			$_122 = \null;
			do {
				if (\substr($this->string,$this->pos,1) === '[') {
					$this->pos += 1;
					$result["text"] .= '[';
				}
				else { $_122 = \false; break; }
				if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
				$_118 = \null;
				do {
					$_116 = \null;
					do {
						$res_113 = $result;
						$pos_113 = $this->pos;
						$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
						if ($subres !== \false) {
							$this->store( $result, $subres, "vector" );
							$_116 = \true; break;
						}
						$result = $res_113;
						$this->pos = $pos_113;
						$stack[] = $result; $result = $this->construct( $matchrule, "vector" ); 
						if (( $subres = $this->literal( '' ) ) !== \false) {
							$result["text"] .= $subres;
							$subres = $result; $result = \array_pop($stack);
							$this->store( $result, $subres, 'vector' );
							$_116 = \true; break;
						}
						else { $result = \array_pop($stack); }
						$result = $res_113;
						$this->pos = $pos_113;
						$_116 = \false; break;
					}
					while(0);
					if( $_116 === \false) { $_118 = \false; break; }
					$_118 = \true; break;
				}
				while(0);
				if( $_118 === \false) { $_122 = \false; break; }
				if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
				if (\substr($this->string,$this->pos,1) === ']') {
					$this->pos += 1;
					$result["text"] .= ']';
				}
				else { $_122 = \false; break; }
				$_122 = \true; break;
			}
			while(0);
			if( $_122 === \false) {
				$result = $res_123;
				$this->pos = $pos_123;
				unset( $res_123 );
				unset( $pos_123 );
				break;
			}
			$count++;
		}
		if ($count >= 1) {  }
		else { $_124 = \false; break; }
		$_124 = \true; break;
	}
	while(0);
	if( $_124 === \true ) { return $this->finalise($result); }
	if( $_124 === \false) { return \false; }
}


/* Mutable: skip:VariableVector | skip:VariableName */
protected $match_Mutable_typestack = array('Mutable');
function match_Mutable ($stack = []) {
	$matchrule = "Mutable"; $result = $this->construct($matchrule, $matchrule, \null);
	$_129 = \null;
	do {
		$res_126 = $result;
		$pos_126 = $this->pos;
		$matcher = 'match_'.'VariableVector'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
			$_129 = \true; break;
		}
		$result = $res_126;
		$this->pos = $pos_126;
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
			$_129 = \true; break;
		}
		$result = $res_126;
		$this->pos = $pos_126;
		$_129 = \false; break;
	}
	while(0);
	if( $_129 === \true ) { return $this->finalise($result); }
	if( $_129 === \false) { return \false; }
}


/* ObjectResolutionOperator: "." */
protected $match_ObjectResolutionOperator_typestack = array('ObjectResolutionOperator');
function match_ObjectResolutionOperator ($stack = []) {
	$matchrule = "ObjectResolutionOperator"; $result = $this->construct($matchrule, $matchrule, \null);
	if (\substr($this->string,$this->pos,1) === '.') {
		$this->pos += 1;
		$result["text"] .= '.';
		return $this->finalise($result);
	}
	else { return \false; }
}


/* AddOperator: "+" | "-" */
protected $match_AddOperator_typestack = array('AddOperator');
function match_AddOperator ($stack = []) {
	$matchrule = "AddOperator"; $result = $this->construct($matchrule, $matchrule, \null);
	$_135 = \null;
	do {
		$res_132 = $result;
		$pos_132 = $this->pos;
		if (\substr($this->string,$this->pos,1) === '+') {
			$this->pos += 1;
			$result["text"] .= '+';
			$_135 = \true; break;
		}
		$result = $res_132;
		$this->pos = $pos_132;
		if (\substr($this->string,$this->pos,1) === '-') {
			$this->pos += 1;
			$result["text"] .= '-';
			$_135 = \true; break;
		}
		$result = $res_132;
		$this->pos = $pos_132;
		$_135 = \false; break;
	}
	while(0);
	if( $_135 === \true ) { return $this->finalise($result); }
	if( $_135 === \false) { return \false; }
}


/* MultiplyOperator: "*" | "/" */
protected $match_MultiplyOperator_typestack = array('MultiplyOperator');
function match_MultiplyOperator ($stack = []) {
	$matchrule = "MultiplyOperator"; $result = $this->construct($matchrule, $matchrule, \null);
	$_140 = \null;
	do {
		$res_137 = $result;
		$pos_137 = $this->pos;
		if (\substr($this->string,$this->pos,1) === '*') {
			$this->pos += 1;
			$result["text"] .= '*';
			$_140 = \true; break;
		}
		$result = $res_137;
		$this->pos = $pos_137;
		if (\substr($this->string,$this->pos,1) === '/') {
			$this->pos += 1;
			$result["text"] .= '/';
			$_140 = \true; break;
		}
		$result = $res_137;
		$this->pos = $pos_137;
		$_140 = \false; break;
	}
	while(0);
	if( $_140 === \true ) { return $this->finalise($result); }
	if( $_140 === \false) { return \false; }
}


/* AssignmentOperator: "=" */
protected $match_AssignmentOperator_typestack = array('AssignmentOperator');
function match_AssignmentOperator ($stack = []) {
	$matchrule = "AssignmentOperator"; $result = $this->construct($matchrule, $matchrule, \null);
	if (\substr($this->string,$this->pos,1) === '=') {
		$this->pos += 1;
		$result["text"] .= '=';
		return $this->finalise($result);
	}
	else { return \false; }
}


/* ComparisonOperator: "==" | "!=" | ">=" | "<=" | ">" | "<" */
protected $match_ComparisonOperator_typestack = array('ComparisonOperator');
function match_ComparisonOperator ($stack = []) {
	$matchrule = "ComparisonOperator"; $result = $this->construct($matchrule, $matchrule, \null);
	$_162 = \null;
	do {
		$res_143 = $result;
		$pos_143 = $this->pos;
		if (( $subres = $this->literal( '==' ) ) !== \false) {
			$result["text"] .= $subres;
			$_162 = \true; break;
		}
		$result = $res_143;
		$this->pos = $pos_143;
		$_160 = \null;
		do {
			$res_145 = $result;
			$pos_145 = $this->pos;
			if (( $subres = $this->literal( '!=' ) ) !== \false) {
				$result["text"] .= $subres;
				$_160 = \true; break;
			}
			$result = $res_145;
			$this->pos = $pos_145;
			$_158 = \null;
			do {
				$res_147 = $result;
				$pos_147 = $this->pos;
				if (( $subres = $this->literal( '>=' ) ) !== \false) {
					$result["text"] .= $subres;
					$_158 = \true; break;
				}
				$result = $res_147;
				$this->pos = $pos_147;
				$_156 = \null;
				do {
					$res_149 = $result;
					$pos_149 = $this->pos;
					if (( $subres = $this->literal( '<=' ) ) !== \false) {
						$result["text"] .= $subres;
						$_156 = \true; break;
					}
					$result = $res_149;
					$this->pos = $pos_149;
					$_154 = \null;
					do {
						$res_151 = $result;
						$pos_151 = $this->pos;
						if (\substr($this->string,$this->pos,1) === '>') {
							$this->pos += 1;
							$result["text"] .= '>';
							$_154 = \true; break;
						}
						$result = $res_151;
						$this->pos = $pos_151;
						if (\substr($this->string,$this->pos,1) === '<') {
							$this->pos += 1;
							$result["text"] .= '<';
							$_154 = \true; break;
						}
						$result = $res_151;
						$this->pos = $pos_151;
						$_154 = \false; break;
					}
					while(0);
					if( $_154 === \true ) { $_156 = \true; break; }
					$result = $res_149;
					$this->pos = $pos_149;
					$_156 = \false; break;
				}
				while(0);
				if( $_156 === \true ) { $_158 = \true; break; }
				$result = $res_147;
				$this->pos = $pos_147;
				$_158 = \false; break;
			}
			while(0);
			if( $_158 === \true ) { $_160 = \true; break; }
			$result = $res_145;
			$this->pos = $pos_145;
			$_160 = \false; break;
		}
		while(0);
		if( $_160 === \true ) { $_162 = \true; break; }
		$result = $res_143;
		$this->pos = $pos_143;
		$_162 = \false; break;
	}
	while(0);
	if( $_162 === \true ) { return $this->finalise($result); }
	if( $_162 === \false) { return \false; }
}


/* UnaryOperator: "++" | "--" */
protected $match_UnaryOperator_typestack = array('UnaryOperator');
function match_UnaryOperator ($stack = []) {
	$matchrule = "UnaryOperator"; $result = $this->construct($matchrule, $matchrule, \null);
	$_167 = \null;
	do {
		$res_164 = $result;
		$pos_164 = $this->pos;
		if (( $subres = $this->literal( '++' ) ) !== \false) {
			$result["text"] .= $subres;
			$_167 = \true; break;
		}
		$result = $res_164;
		$this->pos = $pos_164;
		if (( $subres = $this->literal( '--' ) ) !== \false) {
			$result["text"] .= $subres;
			$_167 = \true; break;
		}
		$result = $res_164;
		$this->pos = $pos_164;
		$_167 = \false; break;
	}
	while(0);
	if( $_167 === \true ) { return $this->finalise($result); }
	if( $_167 === \false) { return \false; }
}


/* Expression: skip:AnonymousFunction | skip:Assignment | skip:Comparison | skip:Addition */
protected $match_Expression_typestack = array('Expression');
function match_Expression ($stack = []) {
	$matchrule = "Expression"; $result = $this->construct($matchrule, $matchrule, \null);
	$_180 = \null;
	do {
		$res_169 = $result;
		$pos_169 = $this->pos;
		$matcher = 'match_'.'AnonymousFunction'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
			$_180 = \true; break;
		}
		$result = $res_169;
		$this->pos = $pos_169;
		$_178 = \null;
		do {
			$res_171 = $result;
			$pos_171 = $this->pos;
			$matcher = 'match_'.'Assignment'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "skip" );
				$_178 = \true; break;
			}
			$result = $res_171;
			$this->pos = $pos_171;
			$_176 = \null;
			do {
				$res_173 = $result;
				$pos_173 = $this->pos;
				$matcher = 'match_'.'Comparison'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "skip" );
					$_176 = \true; break;
				}
				$result = $res_173;
				$this->pos = $pos_173;
				$matcher = 'match_'.'Addition'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "skip" );
					$_176 = \true; break;
				}
				$result = $res_173;
				$this->pos = $pos_173;
				$_176 = \false; break;
			}
			while(0);
			if( $_176 === \true ) { $_178 = \true; break; }
			$result = $res_171;
			$this->pos = $pos_171;
			$_178 = \false; break;
		}
		while(0);
		if( $_178 === \true ) { $_180 = \true; break; }
		$result = $res_169;
		$this->pos = $pos_169;
		$_180 = \false; break;
	}
	while(0);
	if( $_180 === \true ) { return $this->finalise($result); }
	if( $_180 === \false) { return \false; }
}


/* Comparison: left:Addition > op:ComparisonOperator > right:Addition */
protected $match_Comparison_typestack = array('Comparison');
function match_Comparison ($stack = []) {
	$matchrule = "Comparison"; $result = $this->construct($matchrule, $matchrule, \null);
	$_187 = \null;
	do {
		$matcher = 'match_'.'Addition'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "left" );
		}
		else { $_187 = \false; break; }
		if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
		$matcher = 'match_'.'ComparisonOperator'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "op" );
		}
		else { $_187 = \false; break; }
		if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Addition'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "right" );
		}
		else { $_187 = \false; break; }
		$_187 = \true; break;
	}
	while(0);
	if( $_187 === \true ) { return $this->finalise($result); }
	if( $_187 === \false) { return \false; }
}


/* Assignment: left:Mutable > op:AssignmentOperator > right:Expression */
protected $match_Assignment_typestack = array('Assignment');
function match_Assignment ($stack = []) {
	$matchrule = "Assignment"; $result = $this->construct($matchrule, $matchrule, \null);
	$_194 = \null;
	do {
		$matcher = 'match_'.'Mutable'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "left" );
		}
		else { $_194 = \false; break; }
		if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
		$matcher = 'match_'.'AssignmentOperator'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "op" );
		}
		else { $_194 = \false; break; }
		if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "right" );
		}
		else { $_194 = \false; break; }
		$_194 = \true; break;
	}
	while(0);
	if( $_194 === \true ) { return $this->finalise($result); }
	if( $_194 === \false) { return \false; }
}


/* Addition: operands:Multiplication ( > ops:AddOperator > operands:Multiplication)* */
protected $match_Addition_typestack = array('Addition');
function match_Addition ($stack = []) {
	$matchrule = "Addition"; $result = $this->construct($matchrule, $matchrule, \null);
	$_203 = \null;
	do {
		$matcher = 'match_'.'Multiplication'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "operands" );
		}
		else { $_203 = \false; break; }
		while (\true) {
			$res_202 = $result;
			$pos_202 = $this->pos;
			$_201 = \null;
			do {
				if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
				$matcher = 'match_'.'AddOperator'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "ops" );
				}
				else { $_201 = \false; break; }
				if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
				$matcher = 'match_'.'Multiplication'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "operands" );
				}
				else { $_201 = \false; break; }
				$_201 = \true; break;
			}
			while(0);
			if( $_201 === \false) {
				$result = $res_202;
				$this->pos = $pos_202;
				unset( $res_202 );
				unset( $pos_202 );
				break;
			}
		}
		$_203 = \true; break;
	}
	while(0);
	if( $_203 === \true ) { return $this->finalise($result); }
	if( $_203 === \false) { return \false; }
}


/* Multiplication: operands:Operand ( > ops:MultiplyOperator > operands:Operand)* */
protected $match_Multiplication_typestack = array('Multiplication');
function match_Multiplication ($stack = []) {
	$matchrule = "Multiplication"; $result = $this->construct($matchrule, $matchrule, \null);
	$_212 = \null;
	do {
		$matcher = 'match_'.'Operand'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "operands" );
		}
		else { $_212 = \false; break; }
		while (\true) {
			$res_211 = $result;
			$pos_211 = $this->pos;
			$_210 = \null;
			do {
				if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
				$matcher = 'match_'.'MultiplyOperator'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "ops" );
				}
				else { $_210 = \false; break; }
				if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
				$matcher = 'match_'.'Operand'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "operands" );
				}
				else { $_210 = \false; break; }
				$_210 = \true; break;
			}
			while(0);
			if( $_210 === \false) {
				$result = $res_211;
				$this->pos = $pos_211;
				unset( $res_211 );
				unset( $pos_211 );
				break;
			}
		}
		$_212 = \true; break;
	}
	while(0);
	if( $_212 === \true ) { return $this->finalise($result); }
	if( $_212 === \false) { return \false; }
}


/* Chain: ObjectResolutionOperator ( core:MethodCall | core:PropertyGetter ) ( chain:Chain )? */
protected $match_Chain_typestack = array('Chain');
function match_Chain ($stack = []) {
	$matchrule = "Chain"; $result = $this->construct($matchrule, $matchrule, \null);
	$_225 = \null;
	do {
		$matcher = 'match_'.'ObjectResolutionOperator'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_225 = \false; break; }
		$_220 = \null;
		do {
			$_218 = \null;
			do {
				$res_215 = $result;
				$pos_215 = $this->pos;
				$matcher = 'match_'.'MethodCall'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "core" );
					$_218 = \true; break;
				}
				$result = $res_215;
				$this->pos = $pos_215;
				$matcher = 'match_'.'PropertyGetter'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "core" );
					$_218 = \true; break;
				}
				$result = $res_215;
				$this->pos = $pos_215;
				$_218 = \false; break;
			}
			while(0);
			if( $_218 === \false) { $_220 = \false; break; }
			$_220 = \true; break;
		}
		while(0);
		if( $_220 === \false) { $_225 = \false; break; }
		$res_224 = $result;
		$pos_224 = $this->pos;
		$_223 = \null;
		do {
			$matcher = 'match_'.'Chain'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "chain" );
			}
			else { $_223 = \false; break; }
			$_223 = \true; break;
		}
		while(0);
		if( $_223 === \false) {
			$result = $res_224;
			$this->pos = $pos_224;
			unset( $res_224 );
			unset( $pos_224 );
		}
		$_225 = \true; break;
	}
	while(0);
	if( $_225 === \true ) { return $this->finalise($result); }
	if( $_225 === \false) { return \false; }
}


/* Operand: ( core:FunctionCall | "(" > core:Expression > ")" | core:DereferencableValue ) ( chain:Chain )? */
protected $match_Operand_typestack = array('Operand');
function match_Operand ($stack = []) {
	$matchrule = "Operand"; $result = $this->construct($matchrule, $matchrule, \null);
	$_247 = \null;
	do {
		$_242 = \null;
		do {
			$_240 = \null;
			do {
				$res_227 = $result;
				$pos_227 = $this->pos;
				$matcher = 'match_'.'FunctionCall'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "core" );
					$_240 = \true; break;
				}
				$result = $res_227;
				$this->pos = $pos_227;
				$_238 = \null;
				do {
					$res_229 = $result;
					$pos_229 = $this->pos;
					$_235 = \null;
					do {
						if (\substr($this->string,$this->pos,1) === '(') {
							$this->pos += 1;
							$result["text"] .= '(';
						}
						else { $_235 = \false; break; }
						if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
						$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
						if ($subres !== \false) {
							$this->store( $result, $subres, "core" );
						}
						else { $_235 = \false; break; }
						if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
						if (\substr($this->string,$this->pos,1) === ')') {
							$this->pos += 1;
							$result["text"] .= ')';
						}
						else { $_235 = \false; break; }
						$_235 = \true; break;
					}
					while(0);
					if( $_235 === \true ) { $_238 = \true; break; }
					$result = $res_229;
					$this->pos = $pos_229;
					$matcher = 'match_'.'DereferencableValue'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "core" );
						$_238 = \true; break;
					}
					$result = $res_229;
					$this->pos = $pos_229;
					$_238 = \false; break;
				}
				while(0);
				if( $_238 === \true ) { $_240 = \true; break; }
				$result = $res_227;
				$this->pos = $pos_227;
				$_240 = \false; break;
			}
			while(0);
			if( $_240 === \false) { $_242 = \false; break; }
			$_242 = \true; break;
		}
		while(0);
		if( $_242 === \false) { $_247 = \false; break; }
		$res_246 = $result;
		$pos_246 = $this->pos;
		$_245 = \null;
		do {
			$matcher = 'match_'.'Chain'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "chain" );
			}
			else { $_245 = \false; break; }
			$_245 = \true; break;
		}
		while(0);
		if( $_245 === \false) {
			$result = $res_246;
			$this->pos = $pos_246;
			unset( $res_246 );
			unset( $pos_246 );
		}
		$_247 = \true; break;
	}
	while(0);
	if( $_247 === \true ) { return $this->finalise($result); }
	if( $_247 === \false) { return \false; }
}


/* MethodCall: method:VariableName "(" __ args:ArgumentList? __ ")" */
protected $match_MethodCall_typestack = array('MethodCall');
function match_MethodCall ($stack = []) {
	$matchrule = "MethodCall"; $result = $this->construct($matchrule, $matchrule, \null);
	$_255 = \null;
	do {
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "method" );
		}
		else { $_255 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_255 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_255 = \false; break; }
		$res_252 = $result;
		$pos_252 = $this->pos;
		$matcher = 'match_'.'ArgumentList'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "args" );
		}
		else {
			$result = $res_252;
			$this->pos = $pos_252;
			unset( $res_252 );
			unset( $pos_252 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_255 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_255 = \false; break; }
		$_255 = \true; break;
	}
	while(0);
	if( $_255 === \true ) { return $this->finalise($result); }
	if( $_255 === \false) { return \false; }
}


/* FunctionCall: ( variable:VariableName | "(" > value:Expression > ")" ) "(" __ args:ArgumentList? __ ")" */
protected $match_FunctionCall_typestack = array('FunctionCall');
function match_FunctionCall ($stack = []) {
	$matchrule = "FunctionCall"; $result = $this->construct($matchrule, $matchrule, \null);
	$_275 = \null;
	do {
		$_268 = \null;
		do {
			$_266 = \null;
			do {
				$res_257 = $result;
				$pos_257 = $this->pos;
				$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "variable" );
					$_266 = \true; break;
				}
				$result = $res_257;
				$this->pos = $pos_257;
				$_264 = \null;
				do {
					if (\substr($this->string,$this->pos,1) === '(') {
						$this->pos += 1;
						$result["text"] .= '(';
					}
					else { $_264 = \false; break; }
					if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
					$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "value" );
					}
					else { $_264 = \false; break; }
					if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
					if (\substr($this->string,$this->pos,1) === ')') {
						$this->pos += 1;
						$result["text"] .= ')';
					}
					else { $_264 = \false; break; }
					$_264 = \true; break;
				}
				while(0);
				if( $_264 === \true ) { $_266 = \true; break; }
				$result = $res_257;
				$this->pos = $pos_257;
				$_266 = \false; break;
			}
			while(0);
			if( $_266 === \false) { $_268 = \false; break; }
			$_268 = \true; break;
		}
		while(0);
		if( $_268 === \false) { $_275 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_275 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_275 = \false; break; }
		$res_272 = $result;
		$pos_272 = $this->pos;
		$matcher = 'match_'.'ArgumentList'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "args" );
		}
		else {
			$result = $res_272;
			$this->pos = $pos_272;
			unset( $res_272 );
			unset( $pos_272 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_275 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_275 = \false; break; }
		$_275 = \true; break;
	}
	while(0);
	if( $_275 === \true ) { return $this->finalise($result); }
	if( $_275 === \false) { return \false; }
}


/* ArgumentList: args:Expression ( __ "," __ args:Expression )* */
protected $match_ArgumentList_typestack = array('ArgumentList');
function match_ArgumentList ($stack = []) {
	$matchrule = "ArgumentList"; $result = $this->construct($matchrule, $matchrule, \null);
	$_284 = \null;
	do {
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "args" );
		}
		else { $_284 = \false; break; }
		while (\true) {
			$res_283 = $result;
			$pos_283 = $this->pos;
			$_282 = \null;
			do {
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_282 = \false; break; }
				if (\substr($this->string,$this->pos,1) === ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_282 = \false; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_282 = \false; break; }
				$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "args" );
				}
				else { $_282 = \false; break; }
				$_282 = \true; break;
			}
			while(0);
			if( $_282 === \false) {
				$result = $res_283;
				$this->pos = $pos_283;
				unset( $res_283 );
				unset( $pos_283 );
				break;
			}
		}
		$_284 = \true; break;
	}
	while(0);
	if( $_284 === \true ) { return $this->finalise($result); }
	if( $_284 === \false) { return \false; }
}


/* FunctionDefinitionArgumentList: skip:VariableName ( __ "," __ skip:VariableName )* */
protected $match_FunctionDefinitionArgumentList_typestack = array('FunctionDefinitionArgumentList');
function match_FunctionDefinitionArgumentList ($stack = []) {
	$matchrule = "FunctionDefinitionArgumentList"; $result = $this->construct($matchrule, $matchrule, \null);
	$_293 = \null;
	do {
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
		}
		else { $_293 = \false; break; }
		while (\true) {
			$res_292 = $result;
			$pos_292 = $this->pos;
			$_291 = \null;
			do {
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_291 = \false; break; }
				if (\substr($this->string,$this->pos,1) === ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_291 = \false; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_291 = \false; break; }
				$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "skip" );
				}
				else { $_291 = \false; break; }
				$_291 = \true; break;
			}
			while(0);
			if( $_291 === \false) {
				$result = $res_292;
				$this->pos = $pos_292;
				unset( $res_292 );
				unset( $pos_292 );
				break;
			}
		}
		$_293 = \true; break;
	}
	while(0);
	if( $_293 === \true ) { return $this->finalise($result); }
	if( $_293 === \false) { return \false; }
}


/* FunctionDefinition: "function" [ function:VariableName __ "(" __ args:FunctionDefinitionArgumentList? __ ")" __ body:Block */
protected $match_FunctionDefinition_typestack = array('FunctionDefinition');
function match_FunctionDefinition ($stack = []) {
	$matchrule = "FunctionDefinition"; $result = $this->construct($matchrule, $matchrule, \null);
	$_306 = \null;
	do {
		if (( $subres = $this->literal( 'function' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_306 = \false; break; }
		if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
		else { $_306 = \false; break; }
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "function" );
		}
		else { $_306 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_306 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_306 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_306 = \false; break; }
		$res_301 = $result;
		$pos_301 = $this->pos;
		$matcher = 'match_'.'FunctionDefinitionArgumentList'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "args" );
		}
		else {
			$result = $res_301;
			$this->pos = $pos_301;
			unset( $res_301 );
			unset( $pos_301 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_306 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_306 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_306 = \false; break; }
		$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "body" );
		}
		else { $_306 = \false; break; }
		$_306 = \true; break;
	}
	while(0);
	if( $_306 === \true ) { return $this->finalise($result); }
	if( $_306 === \false) { return \false; }
}


/* IfStatement: "if" __ "(" __ left:Expression __ ")" __ ( right:Block ) */
protected $match_IfStatement_typestack = array('IfStatement');
function match_IfStatement ($stack = []) {
	$matchrule = "IfStatement"; $result = $this->construct($matchrule, $matchrule, \null);
	$_319 = \null;
	do {
		if (( $subres = $this->literal( 'if' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_319 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_319 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_319 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_319 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "left" );
		}
		else { $_319 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_319 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_319 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_319 = \false; break; }
		$_317 = \null;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "right" );
			}
			else { $_317 = \false; break; }
			$_317 = \true; break;
		}
		while(0);
		if( $_317 === \false) { $_319 = \false; break; }
		$_319 = \true; break;
	}
	while(0);
	if( $_319 === \true ) { return $this->finalise($result); }
	if( $_319 === \false) { return \false; }
}


/* WhileStatement: "while" __ "(" __ left:Expression __ ")" __ ( right:Block ) */
protected $match_WhileStatement_typestack = array('WhileStatement');
function match_WhileStatement ($stack = []) {
	$matchrule = "WhileStatement"; $result = $this->construct($matchrule, $matchrule, \null);
	$_332 = \null;
	do {
		if (( $subres = $this->literal( 'while' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_332 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_332 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_332 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_332 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "left" );
		}
		else { $_332 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_332 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_332 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_332 = \false; break; }
		$_330 = \null;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "right" );
			}
			else { $_330 = \false; break; }
			$_330 = \true; break;
		}
		while(0);
		if( $_330 === \false) { $_332 = \false; break; }
		$_332 = \true; break;
	}
	while(0);
	if( $_332 === \true ) { return $this->finalise($result); }
	if( $_332 === \false) { return \false; }
}


/* ForeachStatement: "foreach" __ "(" __ left:Expression __ "as" __ item:VariableName __ ")" __ ( right:Block ) */
protected $match_ForeachStatement_typestack = array('ForeachStatement');
function match_ForeachStatement ($stack = []) {
	$matchrule = "ForeachStatement"; $result = $this->construct($matchrule, $matchrule, \null);
	$_349 = \null;
	do {
		if (( $subres = $this->literal( 'foreach' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_349 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_349 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_349 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_349 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "left" );
		}
		else { $_349 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_349 = \false; break; }
		if (( $subres = $this->literal( 'as' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_349 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_349 = \false; break; }
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "item" );
		}
		else { $_349 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_349 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_349 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_349 = \false; break; }
		$_347 = \null;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "right" );
			}
			else { $_347 = \false; break; }
			$_347 = \true; break;
		}
		while(0);
		if( $_347 === \false) { $_349 = \false; break; }
		$_349 = \true; break;
	}
	while(0);
	if( $_349 === \true ) { return $this->finalise($result); }
	if( $_349 === \false) { return \false; }
}


/* BlockStatements: skip:IfStatement | skip:WhileStatement | skip:ForeachStatement | skip:FunctionDefinition */
protected $match_BlockStatements_typestack = array('BlockStatements');
function match_BlockStatements ($stack = []) {
	$matchrule = "BlockStatements"; $result = $this->construct($matchrule, $matchrule, \null);
	$_362 = \null;
	do {
		$res_351 = $result;
		$pos_351 = $this->pos;
		$matcher = 'match_'.'IfStatement'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
			$_362 = \true; break;
		}
		$result = $res_351;
		$this->pos = $pos_351;
		$_360 = \null;
		do {
			$res_353 = $result;
			$pos_353 = $this->pos;
			$matcher = 'match_'.'WhileStatement'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "skip" );
				$_360 = \true; break;
			}
			$result = $res_353;
			$this->pos = $pos_353;
			$_358 = \null;
			do {
				$res_355 = $result;
				$pos_355 = $this->pos;
				$matcher = 'match_'.'ForeachStatement'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "skip" );
					$_358 = \true; break;
				}
				$result = $res_355;
				$this->pos = $pos_355;
				$matcher = 'match_'.'FunctionDefinition'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "skip" );
					$_358 = \true; break;
				}
				$result = $res_355;
				$this->pos = $pos_355;
				$_358 = \false; break;
			}
			while(0);
			if( $_358 === \true ) { $_360 = \true; break; }
			$result = $res_353;
			$this->pos = $pos_353;
			$_360 = \false; break;
		}
		while(0);
		if( $_360 === \true ) { $_362 = \true; break; }
		$result = $res_351;
		$this->pos = $pos_351;
		$_362 = \false; break;
	}
	while(0);
	if( $_362 === \true ) { return $this->finalise($result); }
	if( $_362 === \false) { return \false; }
}


/* CommandStatements: skip:EchoStatement | skip:ReturnStatement */
protected $match_CommandStatements_typestack = array('CommandStatements');
function match_CommandStatements ($stack = []) {
	$matchrule = "CommandStatements"; $result = $this->construct($matchrule, $matchrule, \null);
	$_367 = \null;
	do {
		$res_364 = $result;
		$pos_364 = $this->pos;
		$matcher = 'match_'.'EchoStatement'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
			$_367 = \true; break;
		}
		$result = $res_364;
		$this->pos = $pos_364;
		$matcher = 'match_'.'ReturnStatement'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
			$_367 = \true; break;
		}
		$result = $res_364;
		$this->pos = $pos_364;
		$_367 = \false; break;
	}
	while(0);
	if( $_367 === \true ) { return $this->finalise($result); }
	if( $_367 === \false) { return \false; }
}


/* EchoStatement: "echo" [ subject:Expression */
protected $match_EchoStatement_typestack = array('EchoStatement');
function match_EchoStatement ($stack = []) {
	$matchrule = "EchoStatement"; $result = $this->construct($matchrule, $matchrule, \null);
	$_372 = \null;
	do {
		if (( $subres = $this->literal( 'echo' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_372 = \false; break; }
		if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
		else { $_372 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "subject" );
		}
		else { $_372 = \false; break; }
		$_372 = \true; break;
	}
	while(0);
	if( $_372 === \true ) { return $this->finalise($result); }
	if( $_372 === \false) { return \false; }
}


/* ReturnStatement: "return" ( [ subject:Expression )? */
protected $match_ReturnStatement_typestack = array('ReturnStatement');
function match_ReturnStatement ($stack = []) {
	$matchrule = "ReturnStatement"; $result = $this->construct($matchrule, $matchrule, \null);
	$_379 = \null;
	do {
		if (( $subres = $this->literal( 'return' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_379 = \false; break; }
		$res_378 = $result;
		$pos_378 = $this->pos;
		$_377 = \null;
		do {
			if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
			else { $_377 = \false; break; }
			$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "subject" );
			}
			else { $_377 = \false; break; }
			$_377 = \true; break;
		}
		while(0);
		if( $_377 === \false) {
			$result = $res_378;
			$this->pos = $pos_378;
			unset( $res_378 );
			unset( $pos_378 );
		}
		$_379 = \true; break;
	}
	while(0);
	if( $_379 === \true ) { return $this->finalise($result); }
	if( $_379 === \false) { return \false; }
}


/* Statement: skip:BlockStatements | skip:CommandStatements | skip:Expression */
protected $match_Statement_typestack = array('Statement');
function match_Statement ($stack = []) {
	$matchrule = "Statement"; $result = $this->construct($matchrule, $matchrule, \null);
	$_388 = \null;
	do {
		$res_381 = $result;
		$pos_381 = $this->pos;
		$matcher = 'match_'.'BlockStatements'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
			$_388 = \true; break;
		}
		$result = $res_381;
		$this->pos = $pos_381;
		$_386 = \null;
		do {
			$res_383 = $result;
			$pos_383 = $this->pos;
			$matcher = 'match_'.'CommandStatements'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "skip" );
				$_386 = \true; break;
			}
			$result = $res_383;
			$this->pos = $pos_383;
			$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "skip" );
				$_386 = \true; break;
			}
			$result = $res_383;
			$this->pos = $pos_383;
			$_386 = \false; break;
		}
		while(0);
		if( $_386 === \true ) { $_388 = \true; break; }
		$result = $res_381;
		$this->pos = $pos_381;
		$_388 = \false; break;
	}
	while(0);
	if( $_388 === \true ) { return $this->finalise($result); }
	if( $_388 === \false) { return \false; }
}


/* Block: "{" __ ( skip:Program )? __ "}" */
protected $match_Block_typestack = array('Block');
function match_Block ($stack = []) {
	$matchrule = "Block"; $result = $this->construct($matchrule, $matchrule, \null);
	$_397 = \null;
	do {
		if (\substr($this->string,$this->pos,1) === '{') {
			$this->pos += 1;
			$result["text"] .= '{';
		}
		else { $_397 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_397 = \false; break; }
		$res_394 = $result;
		$pos_394 = $this->pos;
		$_393 = \null;
		do {
			$matcher = 'match_'.'Program'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "skip" );
			}
			else { $_393 = \false; break; }
			$_393 = \true; break;
		}
		while(0);
		if( $_393 === \false) {
			$result = $res_394;
			$this->pos = $pos_394;
			unset( $res_394 );
			unset( $pos_394 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_397 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '}') {
			$this->pos += 1;
			$result["text"] .= '}';
		}
		else { $_397 = \false; break; }
		$_397 = \true; break;
	}
	while(0);
	if( $_397 === \true ) { return $this->finalise($result); }
	if( $_397 === \false) { return \false; }
}


/* __: / [\s\n]* / */
protected $match____typestack = array('__');
function match___ ($stack = []) {
	$matchrule = "__"; $result = $this->construct($matchrule, $matchrule, \null);
	if (( $subres = $this->rx( '/ [\s\n]* /' ) ) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* NL: /(\/\/[^\n]*)?\n/ */
protected $match_NL_typestack = array('NL');
function match_NL ($stack = []) {
	$matchrule = "NL"; $result = $this->construct($matchrule, $matchrule, \null);
	if (( $subres = $this->rx( '/(\/\/[^\n]*)?\n/' ) ) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* SEP: NL | ";" */
protected $match_SEP_typestack = array('SEP');
function match_SEP ($stack = []) {
	$matchrule = "SEP"; $result = $this->construct($matchrule, $matchrule, \null);
	$_404 = \null;
	do {
		$res_401 = $result;
		$pos_401 = $this->pos;
		$matcher = 'match_'.'NL'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres );
			$_404 = \true; break;
		}
		$result = $res_401;
		$this->pos = $pos_401;
		if (\substr($this->string,$this->pos,1) === ';') {
			$this->pos += 1;
			$result["text"] .= ';';
			$_404 = \true; break;
		}
		$result = $res_401;
		$this->pos = $pos_401;
		$_404 = \false; break;
	}
	while(0);
	if( $_404 === \true ) { return $this->finalise($result); }
	if( $_404 === \false) { return \false; }
}


/* Program: ( !/$/ __ Statement? > SEP )+ __ */
protected $match_Program_typestack = array('Program');
function match_Program ($stack = []) {
	$matchrule = "Program"; $result = $this->construct($matchrule, $matchrule, \null);
	$_414 = \null;
	do {
		$count = 0;
		while (\true) {
			$res_412 = $result;
			$pos_412 = $this->pos;
			$_411 = \null;
			do {
				$res_406 = $result;
				$pos_406 = $this->pos;
				if (( $subres = $this->rx( '/$/' ) ) !== \false) {
					$result["text"] .= $subres;
					$result = $res_406;
					$this->pos = $pos_406;
					$_411 = \false; break;
				}
				else {
					$result = $res_406;
					$this->pos = $pos_406;
				}
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_411 = \false; break; }
				$res_408 = $result;
				$pos_408 = $this->pos;
				$matcher = 'match_'.'Statement'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else {
					$result = $res_408;
					$this->pos = $pos_408;
					unset( $res_408 );
					unset( $pos_408 );
				}
				if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
				$matcher = 'match_'.'SEP'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_411 = \false; break; }
				$_411 = \true; break;
			}
			while(0);
			if( $_411 === \false) {
				$result = $res_412;
				$this->pos = $pos_412;
				unset( $res_412 );
				unset( $pos_412 );
				break;
			}
			$count++;
		}
		if ($count >= 1) {  }
		else { $_414 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_414 = \false; break; }
		$_414 = \true; break;
	}
	while(0);
	if( $_414 === \true ) { return $this->finalise($result); }
	if( $_414 === \false) { return \false; }
}




}
