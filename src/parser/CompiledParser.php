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


/* ArrayDefinition: "[" SPACE ( SPACE items:ArrayItem ( SPACE "," SPACE items:ArrayItem )* )? SPACE "]" */
protected $match_ArrayDefinition_typestack = array('ArrayDefinition');
function match_ArrayDefinition ($stack = array()) {
	$matchrule = "ArrayDefinition"; $result = $this->construct($matchrule, $matchrule, null);
	$_71 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_71 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_71 = FALSE; break; }
		$res_68 = $result;
		$pos_68 = $this->pos;
		$_67 = NULL;
		do {
			$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_67 = FALSE; break; }
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
					$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) { $this->store( $result, $subres ); }
					else { $_65 = FALSE; break; }
					if (substr($this->string,$this->pos,1) == ',') {
						$this->pos += 1;
						$result["text"] .= ',';
					}
					else { $_65 = FALSE; break; }
					$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) { $this->store( $result, $subres ); }
					else { $_65 = FALSE; break; }
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
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_71 = FALSE; break; }
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


/* Value: skip:Literal | skip:Variable | skip:ArrayDefinition */
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
			$matcher = 'match_'.'ArrayDefinition'; $key = $matcher; $pos = $this->pos;
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


/* ObjectResolutionOperator: "." */
protected $match_ObjectResolutionOperator_typestack = array('ObjectResolutionOperator');
function match_ObjectResolutionOperator ($stack = array()) {
	$matchrule = "ObjectResolutionOperator"; $result = $this->construct($matchrule, $matchrule, null);
	if (substr($this->string,$this->pos,1) == '.') {
		$this->pos += 1;
		$result["text"] .= '.';
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* AddOperator: "+" | "-" */
protected $match_AddOperator_typestack = array('AddOperator');
function match_AddOperator ($stack = array()) {
	$matchrule = "AddOperator"; $result = $this->construct($matchrule, $matchrule, null);
	$_86 = NULL;
	do {
		$res_83 = $result;
		$pos_83 = $this->pos;
		if (substr($this->string,$this->pos,1) == '+') {
			$this->pos += 1;
			$result["text"] .= '+';
			$_86 = TRUE; break;
		}
		$result = $res_83;
		$this->pos = $pos_83;
		if (substr($this->string,$this->pos,1) == '-') {
			$this->pos += 1;
			$result["text"] .= '-';
			$_86 = TRUE; break;
		}
		$result = $res_83;
		$this->pos = $pos_83;
		$_86 = FALSE; break;
	}
	while(0);
	if( $_86 === TRUE ) { return $this->finalise($result); }
	if( $_86 === FALSE) { return FALSE; }
}


/* MultiplyOperator: "*" | "/" */
protected $match_MultiplyOperator_typestack = array('MultiplyOperator');
function match_MultiplyOperator ($stack = array()) {
	$matchrule = "MultiplyOperator"; $result = $this->construct($matchrule, $matchrule, null);
	$_91 = NULL;
	do {
		$res_88 = $result;
		$pos_88 = $this->pos;
		if (substr($this->string,$this->pos,1) == '*') {
			$this->pos += 1;
			$result["text"] .= '*';
			$_91 = TRUE; break;
		}
		$result = $res_88;
		$this->pos = $pos_88;
		if (substr($this->string,$this->pos,1) == '/') {
			$this->pos += 1;
			$result["text"] .= '/';
			$_91 = TRUE; break;
		}
		$result = $res_88;
		$this->pos = $pos_88;
		$_91 = FALSE; break;
	}
	while(0);
	if( $_91 === TRUE ) { return $this->finalise($result); }
	if( $_91 === FALSE) { return FALSE; }
}


/* AssignmentOperator: "=" | "+=" | "-=" | "*=" | "/=" */
protected $match_AssignmentOperator_typestack = array('AssignmentOperator');
function match_AssignmentOperator ($stack = array()) {
	$matchrule = "AssignmentOperator"; $result = $this->construct($matchrule, $matchrule, null);
	$_108 = NULL;
	do {
		$res_93 = $result;
		$pos_93 = $this->pos;
		if (substr($this->string,$this->pos,1) == '=') {
			$this->pos += 1;
			$result["text"] .= '=';
			$_108 = TRUE; break;
		}
		$result = $res_93;
		$this->pos = $pos_93;
		$_106 = NULL;
		do {
			$res_95 = $result;
			$pos_95 = $this->pos;
			if (( $subres = $this->literal( '+=' ) ) !== FALSE) {
				$result["text"] .= $subres;
				$_106 = TRUE; break;
			}
			$result = $res_95;
			$this->pos = $pos_95;
			$_104 = NULL;
			do {
				$res_97 = $result;
				$pos_97 = $this->pos;
				if (( $subres = $this->literal( '-=' ) ) !== FALSE) {
					$result["text"] .= $subres;
					$_104 = TRUE; break;
				}
				$result = $res_97;
				$this->pos = $pos_97;
				$_102 = NULL;
				do {
					$res_99 = $result;
					$pos_99 = $this->pos;
					if (( $subres = $this->literal( '*=' ) ) !== FALSE) {
						$result["text"] .= $subres;
						$_102 = TRUE; break;
					}
					$result = $res_99;
					$this->pos = $pos_99;
					if (( $subres = $this->literal( '/=' ) ) !== FALSE) {
						$result["text"] .= $subres;
						$_102 = TRUE; break;
					}
					$result = $res_99;
					$this->pos = $pos_99;
					$_102 = FALSE; break;
				}
				while(0);
				if( $_102 === TRUE ) { $_104 = TRUE; break; }
				$result = $res_97;
				$this->pos = $pos_97;
				$_104 = FALSE; break;
			}
			while(0);
			if( $_104 === TRUE ) { $_106 = TRUE; break; }
			$result = $res_95;
			$this->pos = $pos_95;
			$_106 = FALSE; break;
		}
		while(0);
		if( $_106 === TRUE ) { $_108 = TRUE; break; }
		$result = $res_93;
		$this->pos = $pos_93;
		$_108 = FALSE; break;
	}
	while(0);
	if( $_108 === TRUE ) { return $this->finalise($result); }
	if( $_108 === FALSE) { return FALSE; }
}


/* ComparisonOperator: "==" | "!=" | ">=" | "<=" | ">" | "<" */
protected $match_ComparisonOperator_typestack = array('ComparisonOperator');
function match_ComparisonOperator ($stack = array()) {
	$matchrule = "ComparisonOperator"; $result = $this->construct($matchrule, $matchrule, null);
	$_129 = NULL;
	do {
		$res_110 = $result;
		$pos_110 = $this->pos;
		if (( $subres = $this->literal( '==' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_129 = TRUE; break;
		}
		$result = $res_110;
		$this->pos = $pos_110;
		$_127 = NULL;
		do {
			$res_112 = $result;
			$pos_112 = $this->pos;
			if (( $subres = $this->literal( '!=' ) ) !== FALSE) {
				$result["text"] .= $subres;
				$_127 = TRUE; break;
			}
			$result = $res_112;
			$this->pos = $pos_112;
			$_125 = NULL;
			do {
				$res_114 = $result;
				$pos_114 = $this->pos;
				if (( $subres = $this->literal( '>=' ) ) !== FALSE) {
					$result["text"] .= $subres;
					$_125 = TRUE; break;
				}
				$result = $res_114;
				$this->pos = $pos_114;
				$_123 = NULL;
				do {
					$res_116 = $result;
					$pos_116 = $this->pos;
					if (( $subres = $this->literal( '<=' ) ) !== FALSE) {
						$result["text"] .= $subres;
						$_123 = TRUE; break;
					}
					$result = $res_116;
					$this->pos = $pos_116;
					$_121 = NULL;
					do {
						$res_118 = $result;
						$pos_118 = $this->pos;
						if (substr($this->string,$this->pos,1) == '>') {
							$this->pos += 1;
							$result["text"] .= '>';
							$_121 = TRUE; break;
						}
						$result = $res_118;
						$this->pos = $pos_118;
						if (substr($this->string,$this->pos,1) == '<') {
							$this->pos += 1;
							$result["text"] .= '<';
							$_121 = TRUE; break;
						}
						$result = $res_118;
						$this->pos = $pos_118;
						$_121 = FALSE; break;
					}
					while(0);
					if( $_121 === TRUE ) { $_123 = TRUE; break; }
					$result = $res_116;
					$this->pos = $pos_116;
					$_123 = FALSE; break;
				}
				while(0);
				if( $_123 === TRUE ) { $_125 = TRUE; break; }
				$result = $res_114;
				$this->pos = $pos_114;
				$_125 = FALSE; break;
			}
			while(0);
			if( $_125 === TRUE ) { $_127 = TRUE; break; }
			$result = $res_112;
			$this->pos = $pos_112;
			$_127 = FALSE; break;
		}
		while(0);
		if( $_127 === TRUE ) { $_129 = TRUE; break; }
		$result = $res_110;
		$this->pos = $pos_110;
		$_129 = FALSE; break;
	}
	while(0);
	if( $_129 === TRUE ) { return $this->finalise($result); }
	if( $_129 === FALSE) { return FALSE; }
}


/* UnaryOperator: "++" | "--" */
protected $match_UnaryOperator_typestack = array('UnaryOperator');
function match_UnaryOperator ($stack = array()) {
	$matchrule = "UnaryOperator"; $result = $this->construct($matchrule, $matchrule, null);
	$_134 = NULL;
	do {
		$res_131 = $result;
		$pos_131 = $this->pos;
		if (( $subres = $this->literal( '++' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_134 = TRUE; break;
		}
		$result = $res_131;
		$this->pos = $pos_131;
		if (( $subres = $this->literal( '--' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_134 = TRUE; break;
		}
		$result = $res_131;
		$this->pos = $pos_131;
		$_134 = FALSE; break;
	}
	while(0);
	if( $_134 === TRUE ) { return $this->finalise($result); }
	if( $_134 === FALSE) { return FALSE; }
}


/* Expression: skip:Assignment | skip:Comparison | skip:Addition */
protected $match_Expression_typestack = array('Expression');
function match_Expression ($stack = array()) {
	$matchrule = "Expression"; $result = $this->construct($matchrule, $matchrule, null);
	$_143 = NULL;
	do {
		$res_136 = $result;
		$pos_136 = $this->pos;
		$matcher = 'match_'.'Assignment'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_143 = TRUE; break;
		}
		$result = $res_136;
		$this->pos = $pos_136;
		$_141 = NULL;
		do {
			$res_138 = $result;
			$pos_138 = $this->pos;
			$matcher = 'match_'.'Comparison'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_141 = TRUE; break;
			}
			$result = $res_138;
			$this->pos = $pos_138;
			$matcher = 'match_'.'Addition'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_141 = TRUE; break;
			}
			$result = $res_138;
			$this->pos = $pos_138;
			$_141 = FALSE; break;
		}
		while(0);
		if( $_141 === TRUE ) { $_143 = TRUE; break; }
		$result = $res_136;
		$this->pos = $pos_136;
		$_143 = FALSE; break;
	}
	while(0);
	if( $_143 === TRUE ) { return $this->finalise($result); }
	if( $_143 === FALSE) { return FALSE; }
}


/* Comparison: left:Addition > op:ComparisonOperator > right:Addition */
protected $match_Comparison_typestack = array('Comparison');
function match_Comparison ($stack = array()) {
	$matchrule = "Comparison"; $result = $this->construct($matchrule, $matchrule, null);
	$_150 = NULL;
	do {
		$matcher = 'match_'.'Addition'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "left" );
		}
		else { $_150 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'ComparisonOperator'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "op" );
		}
		else { $_150 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Addition'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "right" );
		}
		else { $_150 = FALSE; break; }
		$_150 = TRUE; break;
	}
	while(0);
	if( $_150 === TRUE ) { return $this->finalise($result); }
	if( $_150 === FALSE) { return FALSE; }
}


/* Assignment: left:Variable > op:AssignmentOperator > right:Expression */
protected $match_Assignment_typestack = array('Assignment');
function match_Assignment ($stack = array()) {
	$matchrule = "Assignment"; $result = $this->construct($matchrule, $matchrule, null);
	$_157 = NULL;
	do {
		$matcher = 'match_'.'Variable'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "left" );
		}
		else { $_157 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'AssignmentOperator'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "op" );
		}
		else { $_157 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "right" );
		}
		else { $_157 = FALSE; break; }
		$_157 = TRUE; break;
	}
	while(0);
	if( $_157 === TRUE ) { return $this->finalise($result); }
	if( $_157 === FALSE) { return FALSE; }
}


/* Addition: operands:Multiplication ( > ops:AddOperator > operands:Multiplication)* */
protected $match_Addition_typestack = array('Addition');
function match_Addition ($stack = array()) {
	$matchrule = "Addition"; $result = $this->construct($matchrule, $matchrule, null);
	$_166 = NULL;
	do {
		$matcher = 'match_'.'Multiplication'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "operands" );
		}
		else { $_166 = FALSE; break; }
		while (true) {
			$res_165 = $result;
			$pos_165 = $this->pos;
			$_164 = NULL;
			do {
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'AddOperator'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "ops" );
				}
				else { $_164 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'Multiplication'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "operands" );
				}
				else { $_164 = FALSE; break; }
				$_164 = TRUE; break;
			}
			while(0);
			if( $_164 === FALSE) {
				$result = $res_165;
				$this->pos = $pos_165;
				unset( $res_165 );
				unset( $pos_165 );
				break;
			}
		}
		$_166 = TRUE; break;
	}
	while(0);
	if( $_166 === TRUE ) { return $this->finalise($result); }
	if( $_166 === FALSE) { return FALSE; }
}


/* Multiplication: operands:Operand ( > ops:MultiplyOperator > operands:Operand)* */
protected $match_Multiplication_typestack = array('Multiplication');
function match_Multiplication ($stack = array()) {
	$matchrule = "Multiplication"; $result = $this->construct($matchrule, $matchrule, null);
	$_175 = NULL;
	do {
		$matcher = 'match_'.'Operand'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "operands" );
		}
		else { $_175 = FALSE; break; }
		while (true) {
			$res_174 = $result;
			$pos_174 = $this->pos;
			$_173 = NULL;
			do {
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'MultiplyOperator'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "ops" );
				}
				else { $_173 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'Operand'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "operands" );
				}
				else { $_173 = FALSE; break; }
				$_173 = TRUE; break;
			}
			while(0);
			if( $_173 === FALSE) {
				$result = $res_174;
				$this->pos = $pos_174;
				unset( $res_174 );
				unset( $pos_174 );
				break;
			}
		}
		$_175 = TRUE; break;
	}
	while(0);
	if( $_175 === TRUE ) { return $this->finalise($result); }
	if( $_175 === FALSE) { return FALSE; }
}


/* Operand: ( ( "(" > core:Expression > ")" ) | core:FunctionCall | core:Value ) ( ObjectResolutionOperator method:FunctionCall)* */
protected $match_Operand_typestack = array('Operand');
function match_Operand ($stack = array()) {
	$matchrule = "Operand"; $result = $this->construct($matchrule, $matchrule, null);
	$_198 = NULL;
	do {
		$_192 = NULL;
		do {
			$_190 = NULL;
			do {
				$res_177 = $result;
				$pos_177 = $this->pos;
				$_183 = NULL;
				do {
					if (substr($this->string,$this->pos,1) == '(') {
						$this->pos += 1;
						$result["text"] .= '(';
					}
					else { $_183 = FALSE; break; }
					if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
					$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres, "core" );
					}
					else { $_183 = FALSE; break; }
					if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
					if (substr($this->string,$this->pos,1) == ')') {
						$this->pos += 1;
						$result["text"] .= ')';
					}
					else { $_183 = FALSE; break; }
					$_183 = TRUE; break;
				}
				while(0);
				if( $_183 === TRUE ) { $_190 = TRUE; break; }
				$result = $res_177;
				$this->pos = $pos_177;
				$_188 = NULL;
				do {
					$res_185 = $result;
					$pos_185 = $this->pos;
					$matcher = 'match_'.'FunctionCall'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres, "core" );
						$_188 = TRUE; break;
					}
					$result = $res_185;
					$this->pos = $pos_185;
					$matcher = 'match_'.'Value'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres, "core" );
						$_188 = TRUE; break;
					}
					$result = $res_185;
					$this->pos = $pos_185;
					$_188 = FALSE; break;
				}
				while(0);
				if( $_188 === TRUE ) { $_190 = TRUE; break; }
				$result = $res_177;
				$this->pos = $pos_177;
				$_190 = FALSE; break;
			}
			while(0);
			if( $_190 === FALSE) { $_192 = FALSE; break; }
			$_192 = TRUE; break;
		}
		while(0);
		if( $_192 === FALSE) { $_198 = FALSE; break; }
		while (true) {
			$res_197 = $result;
			$pos_197 = $this->pos;
			$_196 = NULL;
			do {
				$matcher = 'match_'.'ObjectResolutionOperator'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_196 = FALSE; break; }
				$matcher = 'match_'.'FunctionCall'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "method" );
				}
				else { $_196 = FALSE; break; }
				$_196 = TRUE; break;
			}
			while(0);
			if( $_196 === FALSE) {
				$result = $res_197;
				$this->pos = $pos_197;
				unset( $res_197 );
				unset( $pos_197 );
				break;
			}
		}
		$_198 = TRUE; break;
	}
	while(0);
	if( $_198 === TRUE ) { return $this->finalise($result); }
	if( $_198 === FALSE) { return FALSE; }
}


/* FunctionCall: function:VariableCore > "(" > args:FunctionCallArgumentList? > ")" */
protected $match_FunctionCall_typestack = array('FunctionCall');
function match_FunctionCall ($stack = array()) {
	$matchrule = "FunctionCall"; $result = $this->construct($matchrule, $matchrule, null);
	$_207 = NULL;
	do {
		$matcher = 'match_'.'VariableCore'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "function" );
		}
		else { $_207 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_207 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$res_204 = $result;
		$pos_204 = $this->pos;
		$matcher = 'match_'.'FunctionCallArgumentList'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "args" );
		}
		else {
			$result = $res_204;
			$this->pos = $pos_204;
			unset( $res_204 );
			unset( $pos_204 );
		}
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_207 = FALSE; break; }
		$_207 = TRUE; break;
	}
	while(0);
	if( $_207 === TRUE ) { return $this->finalise($result); }
	if( $_207 === FALSE) { return FALSE; }
}


/* FunctionCallArgumentList: skip:Operand ( > "," > skip:Operand )* */
protected $match_FunctionCallArgumentList_typestack = array('FunctionCallArgumentList');
function match_FunctionCallArgumentList ($stack = array()) {
	$matchrule = "FunctionCallArgumentList"; $result = $this->construct($matchrule, $matchrule, null);
	$_216 = NULL;
	do {
		$matcher = 'match_'.'Operand'; $key = $matcher; $pos = $this->pos;
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
				$matcher = 'match_'.'Operand'; $key = $matcher; $pos = $this->pos;
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


/* FunctionDefinitionArgumentList: skip:VariableCore ( > "," > skip:VariableCore )* */
protected $match_FunctionDefinitionArgumentList_typestack = array('FunctionDefinitionArgumentList');
function match_FunctionDefinitionArgumentList ($stack = array()) {
	$matchrule = "FunctionDefinitionArgumentList"; $result = $this->construct($matchrule, $matchrule, null);
	$_225 = NULL;
	do {
		$matcher = 'match_'.'VariableCore'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
		}
		else { $_225 = FALSE; break; }
		while (true) {
			$res_224 = $result;
			$pos_224 = $this->pos;
			$_223 = NULL;
			do {
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				if (substr($this->string,$this->pos,1) == ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_223 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'VariableCore'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "skip" );
				}
				else { $_223 = FALSE; break; }
				$_223 = TRUE; break;
			}
			while(0);
			if( $_223 === FALSE) {
				$result = $res_224;
				$this->pos = $pos_224;
				unset( $res_224 );
				unset( $pos_224 );
				break;
			}
		}
		$_225 = TRUE; break;
	}
	while(0);
	if( $_225 === TRUE ) { return $this->finalise($result); }
	if( $_225 === FALSE) { return FALSE; }
}


/* FunctionDefinition: "function" [ function:VariableCore SPACE "(" > args:FunctionDefinitionArgumentList? > ")" SPACE body:Block */
protected $match_FunctionDefinition_typestack = array('FunctionDefinition');
function match_FunctionDefinition ($stack = array()) {
	$matchrule = "FunctionDefinition"; $result = $this->construct($matchrule, $matchrule, null);
	$_238 = NULL;
	do {
		if (( $subres = $this->literal( 'function' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_238 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_238 = FALSE; break; }
		$matcher = 'match_'.'VariableCore'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "function" );
		}
		else { $_238 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_238 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_238 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$res_233 = $result;
		$pos_233 = $this->pos;
		$matcher = 'match_'.'FunctionDefinitionArgumentList'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "args" );
		}
		else {
			$result = $res_233;
			$this->pos = $pos_233;
			unset( $res_233 );
			unset( $pos_233 );
		}
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_238 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_238 = FALSE; break; }
		$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "body" );
		}
		else { $_238 = FALSE; break; }
		$_238 = TRUE; break;
	}
	while(0);
	if( $_238 === TRUE ) { return $this->finalise($result); }
	if( $_238 === FALSE) { return FALSE; }
}


/* IfStatement: "if" SPACE "(" > left:Expression > ")" SPACE ( right:Block ) > */
protected $match_IfStatement_typestack = array('IfStatement');
function match_IfStatement ($stack = array()) {
	$matchrule = "IfStatement"; $result = $this->construct($matchrule, $matchrule, null);
	$_252 = NULL;
	do {
		if (( $subres = $this->literal( 'if' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_252 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_252 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_252 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "left" );
		}
		else { $_252 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_252 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_252 = FALSE; break; }
		$_249 = NULL;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "right" );
			}
			else { $_249 = FALSE; break; }
			$_249 = TRUE; break;
		}
		while(0);
		if( $_249 === FALSE) { $_252 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$_252 = TRUE; break;
	}
	while(0);
	if( $_252 === TRUE ) { return $this->finalise($result); }
	if( $_252 === FALSE) { return FALSE; }
}


/* WhileStatement: "while" SPACE "(" > left:Expression > ")" SPACE ( right:Block ) > */
protected $match_WhileStatement_typestack = array('WhileStatement');
function match_WhileStatement ($stack = array()) {
	$matchrule = "WhileStatement"; $result = $this->construct($matchrule, $matchrule, null);
	$_266 = NULL;
	do {
		if (( $subres = $this->literal( 'while' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_266 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_266 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_266 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "left" );
		}
		else { $_266 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_266 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_266 = FALSE; break; }
		$_263 = NULL;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "right" );
			}
			else { $_263 = FALSE; break; }
			$_263 = TRUE; break;
		}
		while(0);
		if( $_263 === FALSE) { $_266 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$_266 = TRUE; break;
	}
	while(0);
	if( $_266 === TRUE ) { return $this->finalise($result); }
	if( $_266 === FALSE) { return FALSE; }
}


/* ForeachStatement: "foreach" SPACE "(" > left:Expression SPACE "as" SPACE item:VariableCore SPACE ")" SPACE ( right:Block ) > */
protected $match_ForeachStatement_typestack = array('ForeachStatement');
function match_ForeachStatement ($stack = array()) {
	$matchrule = "ForeachStatement"; $result = $this->construct($matchrule, $matchrule, null);
	$_284 = NULL;
	do {
		if (( $subres = $this->literal( 'foreach' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_284 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_284 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_284 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "left" );
		}
		else { $_284 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_284 = FALSE; break; }
		if (( $subres = $this->literal( 'as' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_284 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_284 = FALSE; break; }
		$matcher = 'match_'.'VariableCore'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "item" );
		}
		else { $_284 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_284 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_284 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_284 = FALSE; break; }
		$_281 = NULL;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "right" );
			}
			else { $_281 = FALSE; break; }
			$_281 = TRUE; break;
		}
		while(0);
		if( $_281 === FALSE) { $_284 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$_284 = TRUE; break;
	}
	while(0);
	if( $_284 === TRUE ) { return $this->finalise($result); }
	if( $_284 === FALSE) { return FALSE; }
}


/* BlockStatements: skip:IfStatement | skip:WhileStatement | skip:ForeachStatement | skip:FunctionDefinition */
protected $match_BlockStatements_typestack = array('BlockStatements');
function match_BlockStatements ($stack = array()) {
	$matchrule = "BlockStatements"; $result = $this->construct($matchrule, $matchrule, null);
	$_297 = NULL;
	do {
		$res_286 = $result;
		$pos_286 = $this->pos;
		$matcher = 'match_'.'IfStatement'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_297 = TRUE; break;
		}
		$result = $res_286;
		$this->pos = $pos_286;
		$_295 = NULL;
		do {
			$res_288 = $result;
			$pos_288 = $this->pos;
			$matcher = 'match_'.'WhileStatement'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_295 = TRUE; break;
			}
			$result = $res_288;
			$this->pos = $pos_288;
			$_293 = NULL;
			do {
				$res_290 = $result;
				$pos_290 = $this->pos;
				$matcher = 'match_'.'ForeachStatement'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "skip" );
					$_293 = TRUE; break;
				}
				$result = $res_290;
				$this->pos = $pos_290;
				$matcher = 'match_'.'FunctionDefinition'; $key = $matcher; $pos = $this->pos;
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
			if( $_293 === TRUE ) { $_295 = TRUE; break; }
			$result = $res_288;
			$this->pos = $pos_288;
			$_295 = FALSE; break;
		}
		while(0);
		if( $_295 === TRUE ) { $_297 = TRUE; break; }
		$result = $res_286;
		$this->pos = $pos_286;
		$_297 = FALSE; break;
	}
	while(0);
	if( $_297 === TRUE ) { return $this->finalise($result); }
	if( $_297 === FALSE) { return FALSE; }
}


/* CommandStatements: skip:EchoStatement | skip:ReturnStatement */
protected $match_CommandStatements_typestack = array('CommandStatements');
function match_CommandStatements ($stack = array()) {
	$matchrule = "CommandStatements"; $result = $this->construct($matchrule, $matchrule, null);
	$_302 = NULL;
	do {
		$res_299 = $result;
		$pos_299 = $this->pos;
		$matcher = 'match_'.'EchoStatement'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_302 = TRUE; break;
		}
		$result = $res_299;
		$this->pos = $pos_299;
		$matcher = 'match_'.'ReturnStatement'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_302 = TRUE; break;
		}
		$result = $res_299;
		$this->pos = $pos_299;
		$_302 = FALSE; break;
	}
	while(0);
	if( $_302 === TRUE ) { return $this->finalise($result); }
	if( $_302 === FALSE) { return FALSE; }
}


/* EchoStatement: "echo" [ subject:Expression */
protected $match_EchoStatement_typestack = array('EchoStatement');
function match_EchoStatement ($stack = array()) {
	$matchrule = "EchoStatement"; $result = $this->construct($matchrule, $matchrule, null);
	$_307 = NULL;
	do {
		if (( $subres = $this->literal( 'echo' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_307 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_307 = FALSE; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "subject" );
		}
		else { $_307 = FALSE; break; }
		$_307 = TRUE; break;
	}
	while(0);
	if( $_307 === TRUE ) { return $this->finalise($result); }
	if( $_307 === FALSE) { return FALSE; }
}


/* ReturnStatement: "return" ( [ subject:Expression )? */
protected $match_ReturnStatement_typestack = array('ReturnStatement');
function match_ReturnStatement ($stack = array()) {
	$matchrule = "ReturnStatement"; $result = $this->construct($matchrule, $matchrule, null);
	$_314 = NULL;
	do {
		if (( $subres = $this->literal( 'return' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_314 = FALSE; break; }
		$res_313 = $result;
		$pos_313 = $this->pos;
		$_312 = NULL;
		do {
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			else { $_312 = FALSE; break; }
			$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "subject" );
			}
			else { $_312 = FALSE; break; }
			$_312 = TRUE; break;
		}
		while(0);
		if( $_312 === FALSE) {
			$result = $res_313;
			$this->pos = $pos_313;
			unset( $res_313 );
			unset( $pos_313 );
		}
		$_314 = TRUE; break;
	}
	while(0);
	if( $_314 === TRUE ) { return $this->finalise($result); }
	if( $_314 === FALSE) { return FALSE; }
}


/* Statement: ( skip:BlockStatements SPACE ";"? ) | ( ( skip:CommandStatements | skip:Expression ) SPACE ";" ) */
protected $match_Statement_typestack = array('Statement');
function match_Statement ($stack = array()) {
	$matchrule = "Statement"; $result = $this->construct($matchrule, $matchrule, null);
	$_333 = NULL;
	do {
		$res_316 = $result;
		$pos_316 = $this->pos;
		$_320 = NULL;
		do {
			$matcher = 'match_'.'BlockStatements'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
			}
			else { $_320 = FALSE; break; }
			$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_320 = FALSE; break; }
			$res_319 = $result;
			$pos_319 = $this->pos;
			if (substr($this->string,$this->pos,1) == ';') {
				$this->pos += 1;
				$result["text"] .= ';';
			}
			else {
				$result = $res_319;
				$this->pos = $pos_319;
				unset( $res_319 );
				unset( $pos_319 );
			}
			$_320 = TRUE; break;
		}
		while(0);
		if( $_320 === TRUE ) { $_333 = TRUE; break; }
		$result = $res_316;
		$this->pos = $pos_316;
		$_331 = NULL;
		do {
			$_327 = NULL;
			do {
				$_325 = NULL;
				do {
					$res_322 = $result;
					$pos_322 = $this->pos;
					$matcher = 'match_'.'CommandStatements'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres, "skip" );
						$_325 = TRUE; break;
					}
					$result = $res_322;
					$this->pos = $pos_322;
					$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres, "skip" );
						$_325 = TRUE; break;
					}
					$result = $res_322;
					$this->pos = $pos_322;
					$_325 = FALSE; break;
				}
				while(0);
				if( $_325 === FALSE) { $_327 = FALSE; break; }
				$_327 = TRUE; break;
			}
			while(0);
			if( $_327 === FALSE) { $_331 = FALSE; break; }
			$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_331 = FALSE; break; }
			if (substr($this->string,$this->pos,1) == ';') {
				$this->pos += 1;
				$result["text"] .= ';';
			}
			else { $_331 = FALSE; break; }
			$_331 = TRUE; break;
		}
		while(0);
		if( $_331 === TRUE ) { $_333 = TRUE; break; }
		$result = $res_316;
		$this->pos = $pos_316;
		$_333 = FALSE; break;
	}
	while(0);
	if( $_333 === TRUE ) { return $this->finalise($result); }
	if( $_333 === FALSE) { return FALSE; }
}


/* Block: "{" > skip:Program > "}" */
protected $match_Block_typestack = array('Block');
function match_Block ($stack = array()) {
	$matchrule = "Block"; $result = $this->construct($matchrule, $matchrule, null);
	$_340 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '{') {
			$this->pos += 1;
			$result["text"] .= '{';
		}
		else { $_340 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Program'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
		}
		else { $_340 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == '}') {
			$this->pos += 1;
			$result["text"] .= '}';
		}
		else { $_340 = FALSE; break; }
		$_340 = TRUE; break;
	}
	while(0);
	if( $_340 === TRUE ) { return $this->finalise($result); }
	if( $_340 === FALSE) { return FALSE; }
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
	$_350 = NULL;
	do {
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$count = 0;
		while (true) {
			$res_348 = $result;
			$pos_348 = $this->pos;
			$_347 = NULL;
			do {
				$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_347 = FALSE; break; }
				$matcher = 'match_'.'Statement'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_347 = FALSE; break; }
				$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_347 = FALSE; break; }
				$_347 = TRUE; break;
			}
			while(0);
			if( $_347 === FALSE) {
				$result = $res_348;
				$this->pos = $pos_348;
				unset( $res_348 );
				unset( $pos_348 );
				break;
			}
			$count++;
		}
		if ($count >= 1) {  }
		else { $_350 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$_350 = TRUE; break;
	}
	while(0);
	if( $_350 === TRUE ) { return $this->finalise($result); }
	if( $_350 === FALSE) { return FALSE; }
}




}
