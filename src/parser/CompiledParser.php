<?php

namespace Smuuf\Primi;

use hafriedlander\Peg\Parser;

class CompiledParser extends Parser\Packrat {

/* StringLiteral: /("(.|\n)*?"|'(.|\n)*?')/ */
protected $match_StringLiteral_typestack = array('StringLiteral');
function match_StringLiteral ($stack = array()) {
	$matchrule = "StringLiteral"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->rx( '/("(.|\n)*?"|\'(.|\n)*?\')/' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* NumberLiteral: /-?\d+(\.\d+)?/ */
protected $match_NumberLiteral_typestack = array('NumberLiteral');
function match_NumberLiteral ($stack = array()) {
	$matchrule = "NumberLiteral"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->rx( '/-?\d+(\.\d+)?/' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* BoolLiteral: "true" | "false" */
protected $match_BoolLiteral_typestack = array('BoolLiteral');
function match_BoolLiteral ($stack = array()) {
	$matchrule = "BoolLiteral"; $result = $this->construct($matchrule, $matchrule, null);
	$_5 = NULL;
	do {
		$res_2 = $result;
		$pos_2 = $this->pos;
		if (( $subres = $this->literal( 'true' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_5 = TRUE; break;
		}
		$result = $res_2;
		$this->pos = $pos_2;
		if (( $subres = $this->literal( 'false' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_5 = TRUE; break;
		}
		$result = $res_2;
		$this->pos = $pos_2;
		$_5 = FALSE; break;
	}
	while(0);
	if( $_5 === TRUE ) { return $this->finalise($result); }
	if( $_5 === FALSE) { return FALSE; }
}


/* RegexLiteral: "/" /([^\\\/]|\\\/)+/ "/" */
protected $match_RegexLiteral_typestack = array('RegexLiteral');
function match_RegexLiteral ($stack = array()) {
	$matchrule = "RegexLiteral"; $result = $this->construct($matchrule, $matchrule, null);
	$_10 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '/') {
			$this->pos += 1;
			$result["text"] .= '/';
		}
		else { $_10 = FALSE; break; }
		if (( $subres = $this->rx( '/([^\\\\\/]|\\\\\/)+/' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_10 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '/') {
			$this->pos += 1;
			$result["text"] .= '/';
		}
		else { $_10 = FALSE; break; }
		$_10 = TRUE; break;
	}
	while(0);
	if( $_10 === TRUE ) { return $this->finalise($result); }
	if( $_10 === FALSE) { return FALSE; }
}


/* Literal: skip:NumberLiteral | skip:StringLiteral | skip:BoolLiteral | skip:RegexLiteral */
protected $match_Literal_typestack = array('Literal');
function match_Literal ($stack = array()) {
	$matchrule = "Literal"; $result = $this->construct($matchrule, $matchrule, null);
	$_23 = NULL;
	do {
		$res_12 = $result;
		$pos_12 = $this->pos;
		$matcher = 'match_'.'NumberLiteral'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_23 = TRUE; break;
		}
		$result = $res_12;
		$this->pos = $pos_12;
		$_21 = NULL;
		do {
			$res_14 = $result;
			$pos_14 = $this->pos;
			$matcher = 'match_'.'StringLiteral'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_21 = TRUE; break;
			}
			$result = $res_14;
			$this->pos = $pos_14;
			$_19 = NULL;
			do {
				$res_16 = $result;
				$pos_16 = $this->pos;
				$matcher = 'match_'.'BoolLiteral'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "skip" );
					$_19 = TRUE; break;
				}
				$result = $res_16;
				$this->pos = $pos_16;
				$matcher = 'match_'.'RegexLiteral'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "skip" );
					$_19 = TRUE; break;
				}
				$result = $res_16;
				$this->pos = $pos_16;
				$_19 = FALSE; break;
			}
			while(0);
			if( $_19 === TRUE ) { $_21 = TRUE; break; }
			$result = $res_14;
			$this->pos = $pos_14;
			$_21 = FALSE; break;
		}
		while(0);
		if( $_21 === TRUE ) { $_23 = TRUE; break; }
		$result = $res_12;
		$this->pos = $pos_12;
		$_23 = FALSE; break;
	}
	while(0);
	if( $_23 === TRUE ) { return $this->finalise($result); }
	if( $_23 === FALSE) { return FALSE; }
}


/* VariableCore: /([a-zA-Z_][a-zA-Z0-9_]*)/ */
protected $match_VariableCore_typestack = array('VariableCore');
function match_VariableCore ($stack = array()) {
	$matchrule = "VariableCore"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->rx( '/([a-zA-Z_][a-zA-Z0-9_]*)/' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* Variable: ( ( core:VariableCore post:UnaryOperator? ) | ( pre:UnaryOperator? core:VariableCore ) ) ( "[" > dereference:Value > "]" )* */
protected $match_Variable_typestack = array('Variable');
function match_Variable ($stack = array()) {
	$matchrule = "Variable"; $result = $this->construct($matchrule, $matchrule, null);
	$_46 = NULL;
	do {
		$_37 = NULL;
		do {
			$_35 = NULL;
			do {
				$res_26 = $result;
				$pos_26 = $this->pos;
				$_29 = NULL;
				do {
					$matcher = 'match_'.'VariableCore'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres, "core" );
					}
					else { $_29 = FALSE; break; }
					$res_28 = $result;
					$pos_28 = $this->pos;
					$matcher = 'match_'.'UnaryOperator'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres, "post" );
					}
					else {
						$result = $res_28;
						$this->pos = $pos_28;
						unset( $res_28 );
						unset( $pos_28 );
					}
					$_29 = TRUE; break;
				}
				while(0);
				if( $_29 === TRUE ) { $_35 = TRUE; break; }
				$result = $res_26;
				$this->pos = $pos_26;
				$_33 = NULL;
				do {
					$res_31 = $result;
					$pos_31 = $this->pos;
					$matcher = 'match_'.'UnaryOperator'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres, "pre" );
					}
					else {
						$result = $res_31;
						$this->pos = $pos_31;
						unset( $res_31 );
						unset( $pos_31 );
					}
					$matcher = 'match_'.'VariableCore'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres, "core" );
					}
					else { $_33 = FALSE; break; }
					$_33 = TRUE; break;
				}
				while(0);
				if( $_33 === TRUE ) { $_35 = TRUE; break; }
				$result = $res_26;
				$this->pos = $pos_26;
				$_35 = FALSE; break;
			}
			while(0);
			if( $_35 === FALSE) { $_37 = FALSE; break; }
			$_37 = TRUE; break;
		}
		while(0);
		if( $_37 === FALSE) { $_46 = FALSE; break; }
		while (true) {
			$res_45 = $result;
			$pos_45 = $this->pos;
			$_44 = NULL;
			do {
				if (substr($this->string,$this->pos,1) == '[') {
					$this->pos += 1;
					$result["text"] .= '[';
				}
				else { $_44 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'Value'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "dereference" );
				}
				else { $_44 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				if (substr($this->string,$this->pos,1) == ']') {
					$this->pos += 1;
					$result["text"] .= ']';
				}
				else { $_44 = FALSE; break; }
				$_44 = TRUE; break;
			}
			while(0);
			if( $_44 === FALSE) {
				$result = $res_45;
				$this->pos = $pos_45;
				unset( $res_45 );
				unset( $pos_45 );
				break;
			}
		}
		$_46 = TRUE; break;
	}
	while(0);
	if( $_46 === TRUE ) { return $this->finalise($result); }
	if( $_46 === FALSE) { return FALSE; }
}


/* ArrayItem: ( key:Expression > ":" )? > value:Expression ) */
protected $match_ArrayItem_typestack = array('ArrayItem');
function match_ArrayItem ($stack = array()) {
	$matchrule = "ArrayItem"; $result = $this->construct($matchrule, $matchrule, null);
	$_55 = NULL;
	do {
		$res_52 = $result;
		$pos_52 = $this->pos;
		$_51 = NULL;
		do {
			$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "key" );
			}
			else { $_51 = FALSE; break; }
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			if (substr($this->string,$this->pos,1) == ':') {
				$this->pos += 1;
				$result["text"] .= ':';
			}
			else { $_51 = FALSE; break; }
			$_51 = TRUE; break;
		}
		while(0);
		if( $_51 === FALSE) {
			$result = $res_52;
			$this->pos = $pos_52;
			unset( $res_52 );
			unset( $pos_52 );
		}
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "value" );
		}
		else { $_55 = FALSE; break; }
		$_55 = TRUE; break;
	}
	while(0);
	if( $_55 === TRUE ) { return $this->finalise($result); }
	if( $_55 === FALSE) { return FALSE; }
}


/* ArrayStructure: "[" > ( > items:ArrayItem ( > "," > items:ArrayItem )* )? > "]" */
protected $match_ArrayStructure_typestack = array('ArrayStructure');
function match_ArrayStructure ($stack = array()) {
	$matchrule = "ArrayStructure"; $result = $this->construct($matchrule, $matchrule, null);
	$_71 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_71 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$res_68 = $result;
		$pos_68 = $this->pos;
		$_67 = NULL;
		do {
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			$matcher = 'match_'.'ArrayItem'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "items" );
			}
			else { $_67 = FALSE; break; }
			while (true) {
				$res_66 = $result;
				$pos_66 = $this->pos;
				$_65 = NULL;
				do {
					if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
					if (substr($this->string,$this->pos,1) == ',') {
						$this->pos += 1;
						$result["text"] .= ',';
					}
					else { $_65 = FALSE; break; }
					if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
					$matcher = 'match_'.'ArrayItem'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres, "items" );
					}
					else { $_65 = FALSE; break; }
					$_65 = TRUE; break;
				}
				while(0);
				if( $_65 === FALSE) {
					$result = $res_66;
					$this->pos = $pos_66;
					unset( $res_66 );
					unset( $pos_66 );
					break;
				}
			}
			$_67 = TRUE; break;
		}
		while(0);
		if( $_67 === FALSE) {
			$result = $res_68;
			$this->pos = $pos_68;
			unset( $res_68 );
			unset( $pos_68 );
		}
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_71 = FALSE; break; }
		$_71 = TRUE; break;
	}
	while(0);
	if( $_71 === TRUE ) { return $this->finalise($result); }
	if( $_71 === FALSE) { return FALSE; }
}


/* Value: skip:Literal | skip:Variable | skip:ArrayStructure */
protected $match_Value_typestack = array('Value');
function match_Value ($stack = array()) {
	$matchrule = "Value"; $result = $this->construct($matchrule, $matchrule, null);
	$_80 = NULL;
	do {
		$res_73 = $result;
		$pos_73 = $this->pos;
		$matcher = 'match_'.'Literal'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_80 = TRUE; break;
		}
		$result = $res_73;
		$this->pos = $pos_73;
		$_78 = NULL;
		do {
			$res_75 = $result;
			$pos_75 = $this->pos;
			$matcher = 'match_'.'Variable'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_78 = TRUE; break;
			}
			$result = $res_75;
			$this->pos = $pos_75;
			$matcher = 'match_'.'ArrayStructure'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_78 = TRUE; break;
			}
			$result = $res_75;
			$this->pos = $pos_75;
			$_78 = FALSE; break;
		}
		while(0);
		if( $_78 === TRUE ) { $_80 = TRUE; break; }
		$result = $res_73;
		$this->pos = $pos_73;
		$_80 = FALSE; break;
	}
	while(0);
	if( $_80 === TRUE ) { return $this->finalise($result); }
	if( $_80 === FALSE) { return FALSE; }
}


/* AddOperator: "+" | "-" */
protected $match_AddOperator_typestack = array('AddOperator');
function match_AddOperator ($stack = array()) {
	$matchrule = "AddOperator"; $result = $this->construct($matchrule, $matchrule, null);
	$_85 = NULL;
	do {
		$res_82 = $result;
		$pos_82 = $this->pos;
		if (substr($this->string,$this->pos,1) == '+') {
			$this->pos += 1;
			$result["text"] .= '+';
			$_85 = TRUE; break;
		}
		$result = $res_82;
		$this->pos = $pos_82;
		if (substr($this->string,$this->pos,1) == '-') {
			$this->pos += 1;
			$result["text"] .= '-';
			$_85 = TRUE; break;
		}
		$result = $res_82;
		$this->pos = $pos_82;
		$_85 = FALSE; break;
	}
	while(0);
	if( $_85 === TRUE ) { return $this->finalise($result); }
	if( $_85 === FALSE) { return FALSE; }
}


/* MultiplyOperator: "*" | "/" */
protected $match_MultiplyOperator_typestack = array('MultiplyOperator');
function match_MultiplyOperator ($stack = array()) {
	$matchrule = "MultiplyOperator"; $result = $this->construct($matchrule, $matchrule, null);
	$_90 = NULL;
	do {
		$res_87 = $result;
		$pos_87 = $this->pos;
		if (substr($this->string,$this->pos,1) == '*') {
			$this->pos += 1;
			$result["text"] .= '*';
			$_90 = TRUE; break;
		}
		$result = $res_87;
		$this->pos = $pos_87;
		if (substr($this->string,$this->pos,1) == '/') {
			$this->pos += 1;
			$result["text"] .= '/';
			$_90 = TRUE; break;
		}
		$result = $res_87;
		$this->pos = $pos_87;
		$_90 = FALSE; break;
	}
	while(0);
	if( $_90 === TRUE ) { return $this->finalise($result); }
	if( $_90 === FALSE) { return FALSE; }
}


/* AssignmentOperator: "=" | "+=" | "-=" | "*=" | "/=" */
protected $match_AssignmentOperator_typestack = array('AssignmentOperator');
function match_AssignmentOperator ($stack = array()) {
	$matchrule = "AssignmentOperator"; $result = $this->construct($matchrule, $matchrule, null);
	$_107 = NULL;
	do {
		$res_92 = $result;
		$pos_92 = $this->pos;
		if (substr($this->string,$this->pos,1) == '=') {
			$this->pos += 1;
			$result["text"] .= '=';
			$_107 = TRUE; break;
		}
		$result = $res_92;
		$this->pos = $pos_92;
		$_105 = NULL;
		do {
			$res_94 = $result;
			$pos_94 = $this->pos;
			if (( $subres = $this->literal( '+=' ) ) !== FALSE) {
				$result["text"] .= $subres;
				$_105 = TRUE; break;
			}
			$result = $res_94;
			$this->pos = $pos_94;
			$_103 = NULL;
			do {
				$res_96 = $result;
				$pos_96 = $this->pos;
				if (( $subres = $this->literal( '-=' ) ) !== FALSE) {
					$result["text"] .= $subres;
					$_103 = TRUE; break;
				}
				$result = $res_96;
				$this->pos = $pos_96;
				$_101 = NULL;
				do {
					$res_98 = $result;
					$pos_98 = $this->pos;
					if (( $subres = $this->literal( '*=' ) ) !== FALSE) {
						$result["text"] .= $subres;
						$_101 = TRUE; break;
					}
					$result = $res_98;
					$this->pos = $pos_98;
					if (( $subres = $this->literal( '/=' ) ) !== FALSE) {
						$result["text"] .= $subres;
						$_101 = TRUE; break;
					}
					$result = $res_98;
					$this->pos = $pos_98;
					$_101 = FALSE; break;
				}
				while(0);
				if( $_101 === TRUE ) { $_103 = TRUE; break; }
				$result = $res_96;
				$this->pos = $pos_96;
				$_103 = FALSE; break;
			}
			while(0);
			if( $_103 === TRUE ) { $_105 = TRUE; break; }
			$result = $res_94;
			$this->pos = $pos_94;
			$_105 = FALSE; break;
		}
		while(0);
		if( $_105 === TRUE ) { $_107 = TRUE; break; }
		$result = $res_92;
		$this->pos = $pos_92;
		$_107 = FALSE; break;
	}
	while(0);
	if( $_107 === TRUE ) { return $this->finalise($result); }
	if( $_107 === FALSE) { return FALSE; }
}


/* ComparisonOperator: "==" | "!=" | ">=" | "<=" | ">" | "<" */
protected $match_ComparisonOperator_typestack = array('ComparisonOperator');
function match_ComparisonOperator ($stack = array()) {
	$matchrule = "ComparisonOperator"; $result = $this->construct($matchrule, $matchrule, null);
	$_128 = NULL;
	do {
		$res_109 = $result;
		$pos_109 = $this->pos;
		if (( $subres = $this->literal( '==' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_128 = TRUE; break;
		}
		$result = $res_109;
		$this->pos = $pos_109;
		$_126 = NULL;
		do {
			$res_111 = $result;
			$pos_111 = $this->pos;
			if (( $subres = $this->literal( '!=' ) ) !== FALSE) {
				$result["text"] .= $subres;
				$_126 = TRUE; break;
			}
			$result = $res_111;
			$this->pos = $pos_111;
			$_124 = NULL;
			do {
				$res_113 = $result;
				$pos_113 = $this->pos;
				if (( $subres = $this->literal( '>=' ) ) !== FALSE) {
					$result["text"] .= $subres;
					$_124 = TRUE; break;
				}
				$result = $res_113;
				$this->pos = $pos_113;
				$_122 = NULL;
				do {
					$res_115 = $result;
					$pos_115 = $this->pos;
					if (( $subres = $this->literal( '<=' ) ) !== FALSE) {
						$result["text"] .= $subres;
						$_122 = TRUE; break;
					}
					$result = $res_115;
					$this->pos = $pos_115;
					$_120 = NULL;
					do {
						$res_117 = $result;
						$pos_117 = $this->pos;
						if (substr($this->string,$this->pos,1) == '>') {
							$this->pos += 1;
							$result["text"] .= '>';
							$_120 = TRUE; break;
						}
						$result = $res_117;
						$this->pos = $pos_117;
						if (substr($this->string,$this->pos,1) == '<') {
							$this->pos += 1;
							$result["text"] .= '<';
							$_120 = TRUE; break;
						}
						$result = $res_117;
						$this->pos = $pos_117;
						$_120 = FALSE; break;
					}
					while(0);
					if( $_120 === TRUE ) { $_122 = TRUE; break; }
					$result = $res_115;
					$this->pos = $pos_115;
					$_122 = FALSE; break;
				}
				while(0);
				if( $_122 === TRUE ) { $_124 = TRUE; break; }
				$result = $res_113;
				$this->pos = $pos_113;
				$_124 = FALSE; break;
			}
			while(0);
			if( $_124 === TRUE ) { $_126 = TRUE; break; }
			$result = $res_111;
			$this->pos = $pos_111;
			$_126 = FALSE; break;
		}
		while(0);
		if( $_126 === TRUE ) { $_128 = TRUE; break; }
		$result = $res_109;
		$this->pos = $pos_109;
		$_128 = FALSE; break;
	}
	while(0);
	if( $_128 === TRUE ) { return $this->finalise($result); }
	if( $_128 === FALSE) { return FALSE; }
}


/* UnaryOperator: "++" | "--" */
protected $match_UnaryOperator_typestack = array('UnaryOperator');
function match_UnaryOperator ($stack = array()) {
	$matchrule = "UnaryOperator"; $result = $this->construct($matchrule, $matchrule, null);
	$_133 = NULL;
	do {
		$res_130 = $result;
		$pos_130 = $this->pos;
		if (( $subres = $this->literal( '++' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_133 = TRUE; break;
		}
		$result = $res_130;
		$this->pos = $pos_130;
		if (( $subres = $this->literal( '--' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_133 = TRUE; break;
		}
		$result = $res_130;
		$this->pos = $pos_130;
		$_133 = FALSE; break;
	}
	while(0);
	if( $_133 === TRUE ) { return $this->finalise($result); }
	if( $_133 === FALSE) { return FALSE; }
}


/* Expression: skip:Assignment | skip:Comparison | skip:Addition */
protected $match_Expression_typestack = array('Expression');
function match_Expression ($stack = array()) {
	$matchrule = "Expression"; $result = $this->construct($matchrule, $matchrule, null);
	$_142 = NULL;
	do {
		$res_135 = $result;
		$pos_135 = $this->pos;
		$matcher = 'match_'.'Assignment'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_142 = TRUE; break;
		}
		$result = $res_135;
		$this->pos = $pos_135;
		$_140 = NULL;
		do {
			$res_137 = $result;
			$pos_137 = $this->pos;
			$matcher = 'match_'.'Comparison'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_140 = TRUE; break;
			}
			$result = $res_137;
			$this->pos = $pos_137;
			$matcher = 'match_'.'Addition'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_140 = TRUE; break;
			}
			$result = $res_137;
			$this->pos = $pos_137;
			$_140 = FALSE; break;
		}
		while(0);
		if( $_140 === TRUE ) { $_142 = TRUE; break; }
		$result = $res_135;
		$this->pos = $pos_135;
		$_142 = FALSE; break;
	}
	while(0);
	if( $_142 === TRUE ) { return $this->finalise($result); }
	if( $_142 === FALSE) { return FALSE; }
}


/* Comparison: left:Addition > op:ComparisonOperator > right:Addition */
protected $match_Comparison_typestack = array('Comparison');
function match_Comparison ($stack = array()) {
	$matchrule = "Comparison"; $result = $this->construct($matchrule, $matchrule, null);
	$_149 = NULL;
	do {
		$matcher = 'match_'.'Addition'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "left" );
		}
		else { $_149 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'ComparisonOperator'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "op" );
		}
		else { $_149 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Addition'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "right" );
		}
		else { $_149 = FALSE; break; }
		$_149 = TRUE; break;
	}
	while(0);
	if( $_149 === TRUE ) { return $this->finalise($result); }
	if( $_149 === FALSE) { return FALSE; }
}


/* Assignment: left:Variable > op:AssignmentOperator > right:Expression */
protected $match_Assignment_typestack = array('Assignment');
function match_Assignment ($stack = array()) {
	$matchrule = "Assignment"; $result = $this->construct($matchrule, $matchrule, null);
	$_156 = NULL;
	do {
		$matcher = 'match_'.'Variable'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "left" );
		}
		else { $_156 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'AssignmentOperator'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "op" );
		}
		else { $_156 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "right" );
		}
		else { $_156 = FALSE; break; }
		$_156 = TRUE; break;
	}
	while(0);
	if( $_156 === TRUE ) { return $this->finalise($result); }
	if( $_156 === FALSE) { return FALSE; }
}


/* Addition: operands:Multiplication ( > ops:AddOperator > operands:Multiplication)* */
protected $match_Addition_typestack = array('Addition');
function match_Addition ($stack = array()) {
	$matchrule = "Addition"; $result = $this->construct($matchrule, $matchrule, null);
	$_165 = NULL;
	do {
		$matcher = 'match_'.'Multiplication'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "operands" );
		}
		else { $_165 = FALSE; break; }
		while (true) {
			$res_164 = $result;
			$pos_164 = $this->pos;
			$_163 = NULL;
			do {
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'AddOperator'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "ops" );
				}
				else { $_163 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'Multiplication'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "operands" );
				}
				else { $_163 = FALSE; break; }
				$_163 = TRUE; break;
			}
			while(0);
			if( $_163 === FALSE) {
				$result = $res_164;
				$this->pos = $pos_164;
				unset( $res_164 );
				unset( $pos_164 );
				break;
			}
		}
		$_165 = TRUE; break;
	}
	while(0);
	if( $_165 === TRUE ) { return $this->finalise($result); }
	if( $_165 === FALSE) { return FALSE; }
}


/* Multiplication: operands:Operand ( > ops:MultiplyOperator > operands:Operand)* */
protected $match_Multiplication_typestack = array('Multiplication');
function match_Multiplication ($stack = array()) {
	$matchrule = "Multiplication"; $result = $this->construct($matchrule, $matchrule, null);
	$_174 = NULL;
	do {
		$matcher = 'match_'.'Operand'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "operands" );
		}
		else { $_174 = FALSE; break; }
		while (true) {
			$res_173 = $result;
			$pos_173 = $this->pos;
			$_172 = NULL;
			do {
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'MultiplyOperator'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "ops" );
				}
				else { $_172 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'Operand'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "operands" );
				}
				else { $_172 = FALSE; break; }
				$_172 = TRUE; break;
			}
			while(0);
			if( $_172 === FALSE) {
				$result = $res_173;
				$this->pos = $pos_173;
				unset( $res_173 );
				unset( $pos_173 );
				break;
			}
		}
		$_174 = TRUE; break;
	}
	while(0);
	if( $_174 === TRUE ) { return $this->finalise($result); }
	if( $_174 === FALSE) { return FALSE; }
}


/* Operand: ( "(" > skip:Expression > ")" ) | skip:FunctionCall | skip:Value */
protected $match_Operand_typestack = array('Operand');
function match_Operand ($stack = array()) {
	$matchrule = "Operand"; $result = $this->construct($matchrule, $matchrule, null);
	$_189 = NULL;
	do {
		$res_176 = $result;
		$pos_176 = $this->pos;
		$_182 = NULL;
		do {
			if (substr($this->string,$this->pos,1) == '(') {
				$this->pos += 1;
				$result["text"] .= '(';
			}
			else { $_182 = FALSE; break; }
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
			}
			else { $_182 = FALSE; break; }
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			if (substr($this->string,$this->pos,1) == ')') {
				$this->pos += 1;
				$result["text"] .= ')';
			}
			else { $_182 = FALSE; break; }
			$_182 = TRUE; break;
		}
		while(0);
		if( $_182 === TRUE ) { $_189 = TRUE; break; }
		$result = $res_176;
		$this->pos = $pos_176;
		$_187 = NULL;
		do {
			$res_184 = $result;
			$pos_184 = $this->pos;
			$matcher = 'match_'.'FunctionCall'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_187 = TRUE; break;
			}
			$result = $res_184;
			$this->pos = $pos_184;
			$matcher = 'match_'.'Value'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_187 = TRUE; break;
			}
			$result = $res_184;
			$this->pos = $pos_184;
			$_187 = FALSE; break;
		}
		while(0);
		if( $_187 === TRUE ) { $_189 = TRUE; break; }
		$result = $res_176;
		$this->pos = $pos_176;
		$_189 = FALSE; break;
	}
	while(0);
	if( $_189 === TRUE ) { return $this->finalise($result); }
	if( $_189 === FALSE) { return FALSE; }
}


/* FunctionCall: function:VariableCore > "(" > args:FunctionCallArgumentList? > ")" */
protected $match_FunctionCall_typestack = array('FunctionCall');
function match_FunctionCall ($stack = array()) {
	$matchrule = "FunctionCall"; $result = $this->construct($matchrule, $matchrule, null);
	$_198 = NULL;
	do {
		$matcher = 'match_'.'VariableCore'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "function" );
		}
		else { $_198 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_198 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$res_195 = $result;
		$pos_195 = $this->pos;
		$matcher = 'match_'.'FunctionCallArgumentList'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "args" );
		}
		else {
			$result = $res_195;
			$this->pos = $pos_195;
			unset( $res_195 );
			unset( $pos_195 );
		}
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_198 = FALSE; break; }
		$_198 = TRUE; break;
	}
	while(0);
	if( $_198 === TRUE ) { return $this->finalise($result); }
	if( $_198 === FALSE) { return FALSE; }
}


/* FunctionCallArgumentList: skip:Operand ( > "," > skip:Operand )* */
protected $match_FunctionCallArgumentList_typestack = array('FunctionCallArgumentList');
function match_FunctionCallArgumentList ($stack = array()) {
	$matchrule = "FunctionCallArgumentList"; $result = $this->construct($matchrule, $matchrule, null);
	$_207 = NULL;
	do {
		$matcher = 'match_'.'Operand'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
		}
		else { $_207 = FALSE; break; }
		while (true) {
			$res_206 = $result;
			$pos_206 = $this->pos;
			$_205 = NULL;
			do {
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				if (substr($this->string,$this->pos,1) == ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_205 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'Operand'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "skip" );
				}
				else { $_205 = FALSE; break; }
				$_205 = TRUE; break;
			}
			while(0);
			if( $_205 === FALSE) {
				$result = $res_206;
				$this->pos = $pos_206;
				unset( $res_206 );
				unset( $pos_206 );
				break;
			}
		}
		$_207 = TRUE; break;
	}
	while(0);
	if( $_207 === TRUE ) { return $this->finalise($result); }
	if( $_207 === FALSE) { return FALSE; }
}


/* FunctionDeclarationArgumentList: skip:VariableCore ( > "," > skip:VariableCore )* */
protected $match_FunctionDeclarationArgumentList_typestack = array('FunctionDeclarationArgumentList');
function match_FunctionDeclarationArgumentList ($stack = array()) {
	$matchrule = "FunctionDeclarationArgumentList"; $result = $this->construct($matchrule, $matchrule, null);
	$_216 = NULL;
	do {
		$matcher = 'match_'.'VariableCore'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
		}
		else { $_216 = FALSE; break; }
		while (true) {
			$res_215 = $result;
			$pos_215 = $this->pos;
			$_214 = NULL;
			do {
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				if (substr($this->string,$this->pos,1) == ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_214 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'VariableCore'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "skip" );
				}
				else { $_214 = FALSE; break; }
				$_214 = TRUE; break;
			}
			while(0);
			if( $_214 === FALSE) {
				$result = $res_215;
				$this->pos = $pos_215;
				unset( $res_215 );
				unset( $pos_215 );
				break;
			}
		}
		$_216 = TRUE; break;
	}
	while(0);
	if( $_216 === TRUE ) { return $this->finalise($result); }
	if( $_216 === FALSE) { return FALSE; }
}


/* FunctionDeclaration: "function" [ function:VariableCore SPACE "(" > args:FunctionDeclarationArgumentList? > ")" SPACE body:Block */
protected $match_FunctionDeclaration_typestack = array('FunctionDeclaration');
function match_FunctionDeclaration ($stack = array()) {
	$matchrule = "FunctionDeclaration"; $result = $this->construct($matchrule, $matchrule, null);
	$_229 = NULL;
	do {
		if (( $subres = $this->literal( 'function' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_229 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_229 = FALSE; break; }
		$matcher = 'match_'.'VariableCore'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "function" );
		}
		else { $_229 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_229 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_229 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$res_224 = $result;
		$pos_224 = $this->pos;
		$matcher = 'match_'.'FunctionDeclarationArgumentList'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "args" );
		}
		else {
			$result = $res_224;
			$this->pos = $pos_224;
			unset( $res_224 );
			unset( $pos_224 );
		}
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_229 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_229 = FALSE; break; }
		$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "body" );
		}
		else { $_229 = FALSE; break; }
		$_229 = TRUE; break;
	}
	while(0);
	if( $_229 === TRUE ) { return $this->finalise($result); }
	if( $_229 === FALSE) { return FALSE; }
}


/* IfStatement: "if" SPACE "(" > left:Expression > ")" SPACE ( right:Block ) > */
protected $match_IfStatement_typestack = array('IfStatement');
function match_IfStatement ($stack = array()) {
	$matchrule = "IfStatement"; $result = $this->construct($matchrule, $matchrule, null);
	$_243 = NULL;
	do {
		if (( $subres = $this->literal( 'if' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_243 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_243 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_243 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "left" );
		}
		else { $_243 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_243 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_243 = FALSE; break; }
		$_240 = NULL;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "right" );
			}
			else { $_240 = FALSE; break; }
			$_240 = TRUE; break;
		}
		while(0);
		if( $_240 === FALSE) { $_243 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$_243 = TRUE; break;
	}
	while(0);
	if( $_243 === TRUE ) { return $this->finalise($result); }
	if( $_243 === FALSE) { return FALSE; }
}


/* WhileStatement: "while" SPACE "(" > left:Expression > ")" SPACE ( right:Block ) > */
protected $match_WhileStatement_typestack = array('WhileStatement');
function match_WhileStatement ($stack = array()) {
	$matchrule = "WhileStatement"; $result = $this->construct($matchrule, $matchrule, null);
	$_257 = NULL;
	do {
		if (( $subres = $this->literal( 'while' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_257 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_257 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_257 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "left" );
		}
		else { $_257 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_257 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_257 = FALSE; break; }
		$_254 = NULL;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "right" );
			}
			else { $_254 = FALSE; break; }
			$_254 = TRUE; break;
		}
		while(0);
		if( $_254 === FALSE) { $_257 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$_257 = TRUE; break;
	}
	while(0);
	if( $_257 === TRUE ) { return $this->finalise($result); }
	if( $_257 === FALSE) { return FALSE; }
}


/* ForeachStatement: "foreach" SPACE "(" > left:Expression SPACE "as" SPACE item:VariableCore SPACE ")" SPACE ( right:Block ) > */
protected $match_ForeachStatement_typestack = array('ForeachStatement');
function match_ForeachStatement ($stack = array()) {
	$matchrule = "ForeachStatement"; $result = $this->construct($matchrule, $matchrule, null);
	$_275 = NULL;
	do {
		if (( $subres = $this->literal( 'foreach' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_275 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_275 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_275 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "left" );
		}
		else { $_275 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_275 = FALSE; break; }
		if (( $subres = $this->literal( 'as' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_275 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_275 = FALSE; break; }
		$matcher = 'match_'.'VariableCore'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "item" );
		}
		else { $_275 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_275 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_275 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_275 = FALSE; break; }
		$_272 = NULL;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "right" );
			}
			else { $_272 = FALSE; break; }
			$_272 = TRUE; break;
		}
		while(0);
		if( $_272 === FALSE) { $_275 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$_275 = TRUE; break;
	}
	while(0);
	if( $_275 === TRUE ) { return $this->finalise($result); }
	if( $_275 === FALSE) { return FALSE; }
}


/* BlockStatements: skip:IfStatement | skip:WhileStatement | skip:ForeachStatement | skip:FunctionDeclaration */
protected $match_BlockStatements_typestack = array('BlockStatements');
function match_BlockStatements ($stack = array()) {
	$matchrule = "BlockStatements"; $result = $this->construct($matchrule, $matchrule, null);
	$_288 = NULL;
	do {
		$res_277 = $result;
		$pos_277 = $this->pos;
		$matcher = 'match_'.'IfStatement'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_288 = TRUE; break;
		}
		$result = $res_277;
		$this->pos = $pos_277;
		$_286 = NULL;
		do {
			$res_279 = $result;
			$pos_279 = $this->pos;
			$matcher = 'match_'.'WhileStatement'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_286 = TRUE; break;
			}
			$result = $res_279;
			$this->pos = $pos_279;
			$_284 = NULL;
			do {
				$res_281 = $result;
				$pos_281 = $this->pos;
				$matcher = 'match_'.'ForeachStatement'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "skip" );
					$_284 = TRUE; break;
				}
				$result = $res_281;
				$this->pos = $pos_281;
				$matcher = 'match_'.'FunctionDeclaration'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "skip" );
					$_284 = TRUE; break;
				}
				$result = $res_281;
				$this->pos = $pos_281;
				$_284 = FALSE; break;
			}
			while(0);
			if( $_284 === TRUE ) { $_286 = TRUE; break; }
			$result = $res_279;
			$this->pos = $pos_279;
			$_286 = FALSE; break;
		}
		while(0);
		if( $_286 === TRUE ) { $_288 = TRUE; break; }
		$result = $res_277;
		$this->pos = $pos_277;
		$_288 = FALSE; break;
	}
	while(0);
	if( $_288 === TRUE ) { return $this->finalise($result); }
	if( $_288 === FALSE) { return FALSE; }
}


/* CommandStatements: skip:EchoStatement | skip:ReturnStatement */
protected $match_CommandStatements_typestack = array('CommandStatements');
function match_CommandStatements ($stack = array()) {
	$matchrule = "CommandStatements"; $result = $this->construct($matchrule, $matchrule, null);
	$_293 = NULL;
	do {
		$res_290 = $result;
		$pos_290 = $this->pos;
		$matcher = 'match_'.'EchoStatement'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_293 = TRUE; break;
		}
		$result = $res_290;
		$this->pos = $pos_290;
		$matcher = 'match_'.'ReturnStatement'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_293 = TRUE; break;
		}
		$result = $res_290;
		$this->pos = $pos_290;
		$_293 = FALSE; break;
	}
	while(0);
	if( $_293 === TRUE ) { return $this->finalise($result); }
	if( $_293 === FALSE) { return FALSE; }
}


/* EchoStatement: "echo" [ subject:Expression */
protected $match_EchoStatement_typestack = array('EchoStatement');
function match_EchoStatement ($stack = array()) {
	$matchrule = "EchoStatement"; $result = $this->construct($matchrule, $matchrule, null);
	$_298 = NULL;
	do {
		if (( $subres = $this->literal( 'echo' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_298 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_298 = FALSE; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "subject" );
		}
		else { $_298 = FALSE; break; }
		$_298 = TRUE; break;
	}
	while(0);
	if( $_298 === TRUE ) { return $this->finalise($result); }
	if( $_298 === FALSE) { return FALSE; }
}


/* ReturnStatement: "return" ( [ subject:Expression )? */
protected $match_ReturnStatement_typestack = array('ReturnStatement');
function match_ReturnStatement ($stack = array()) {
	$matchrule = "ReturnStatement"; $result = $this->construct($matchrule, $matchrule, null);
	$_305 = NULL;
	do {
		if (( $subres = $this->literal( 'return' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_305 = FALSE; break; }
		$res_304 = $result;
		$pos_304 = $this->pos;
		$_303 = NULL;
		do {
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			else { $_303 = FALSE; break; }
			$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "subject" );
			}
			else { $_303 = FALSE; break; }
			$_303 = TRUE; break;
		}
		while(0);
		if( $_303 === FALSE) {
			$result = $res_304;
			$this->pos = $pos_304;
			unset( $res_304 );
			unset( $pos_304 );
		}
		$_305 = TRUE; break;
	}
	while(0);
	if( $_305 === TRUE ) { return $this->finalise($result); }
	if( $_305 === FALSE) { return FALSE; }
}


/* Statement: ( skip:BlockStatements SPACE ";"? ) | ( ( skip:CommandStatements | skip:Expression ) SPACE ";" ) */
protected $match_Statement_typestack = array('Statement');
function match_Statement ($stack = array()) {
	$matchrule = "Statement"; $result = $this->construct($matchrule, $matchrule, null);
	$_324 = NULL;
	do {
		$res_307 = $result;
		$pos_307 = $this->pos;
		$_311 = NULL;
		do {
			$matcher = 'match_'.'BlockStatements'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
			}
			else { $_311 = FALSE; break; }
			$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_311 = FALSE; break; }
			$res_310 = $result;
			$pos_310 = $this->pos;
			if (substr($this->string,$this->pos,1) == ';') {
				$this->pos += 1;
				$result["text"] .= ';';
			}
			else {
				$result = $res_310;
				$this->pos = $pos_310;
				unset( $res_310 );
				unset( $pos_310 );
			}
			$_311 = TRUE; break;
		}
		while(0);
		if( $_311 === TRUE ) { $_324 = TRUE; break; }
		$result = $res_307;
		$this->pos = $pos_307;
		$_322 = NULL;
		do {
			$_318 = NULL;
			do {
				$_316 = NULL;
				do {
					$res_313 = $result;
					$pos_313 = $this->pos;
					$matcher = 'match_'.'CommandStatements'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres, "skip" );
						$_316 = TRUE; break;
					}
					$result = $res_313;
					$this->pos = $pos_313;
					$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres, "skip" );
						$_316 = TRUE; break;
					}
					$result = $res_313;
					$this->pos = $pos_313;
					$_316 = FALSE; break;
				}
				while(0);
				if( $_316 === FALSE) { $_318 = FALSE; break; }
				$_318 = TRUE; break;
			}
			while(0);
			if( $_318 === FALSE) { $_322 = FALSE; break; }
			$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_322 = FALSE; break; }
			if (substr($this->string,$this->pos,1) == ';') {
				$this->pos += 1;
				$result["text"] .= ';';
			}
			else { $_322 = FALSE; break; }
			$_322 = TRUE; break;
		}
		while(0);
		if( $_322 === TRUE ) { $_324 = TRUE; break; }
		$result = $res_307;
		$this->pos = $pos_307;
		$_324 = FALSE; break;
	}
	while(0);
	if( $_324 === TRUE ) { return $this->finalise($result); }
	if( $_324 === FALSE) { return FALSE; }
}


/* Block: "{" > skip:Program > "}" */
protected $match_Block_typestack = array('Block');
function match_Block ($stack = array()) {
	$matchrule = "Block"; $result = $this->construct($matchrule, $matchrule, null);
	$_331 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '{') {
			$this->pos += 1;
			$result["text"] .= '{';
		}
		else { $_331 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Program'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
		}
		else { $_331 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == '}') {
			$this->pos += 1;
			$result["text"] .= '}';
		}
		else { $_331 = FALSE; break; }
		$_331 = TRUE; break;
	}
	while(0);
	if( $_331 === TRUE ) { return $this->finalise($result); }
	if( $_331 === FALSE) { return FALSE; }
}


/* SPACE: /([\s\n]*)/ */
protected $match_SPACE_typestack = array('SPACE');
function match_SPACE ($stack = array()) {
	$matchrule = "SPACE"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->rx( '/([\s\n]*)/' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* Program: > ( SPACE Statement SPACE )+ > */
protected $match_Program_typestack = array('Program');
function match_Program ($stack = array()) {
	$matchrule = "Program"; $result = $this->construct($matchrule, $matchrule, null);
	$_341 = NULL;
	do {
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$count = 0;
		while (true) {
			$res_339 = $result;
			$pos_339 = $this->pos;
			$_338 = NULL;
			do {
				$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_338 = FALSE; break; }
				$matcher = 'match_'.'Statement'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_338 = FALSE; break; }
				$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_338 = FALSE; break; }
				$_338 = TRUE; break;
			}
			while(0);
			if( $_338 === FALSE) {
				$result = $res_339;
				$this->pos = $pos_339;
				unset( $res_339 );
				unset( $pos_339 );
				break;
			}
			$count++;
		}
		if ($count >= 1) {  }
		else { $_341 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$_341 = TRUE; break;
	}
	while(0);
	if( $_341 === TRUE ) { return $this->finalise($result); }
	if( $_341 === FALSE) { return FALSE; }
}




}