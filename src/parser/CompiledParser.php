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


/* Literal: skip:NumberLiteral | skip:StringLiteral | skip:BoolLiteral */
protected $match_Literal_typestack = array('Literal');
function match_Literal ($stack = array()) {
	$matchrule = "Literal"; $result = $this->construct($matchrule, $matchrule, null);
	$_14 = NULL;
	do {
		$res_7 = $result;
		$pos_7 = $this->pos;
		$matcher = 'match_'.'NumberLiteral'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_14 = TRUE; break;
		}
		$result = $res_7;
		$this->pos = $pos_7;
		$_12 = NULL;
		do {
			$res_9 = $result;
			$pos_9 = $this->pos;
			$matcher = 'match_'.'StringLiteral'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_12 = TRUE; break;
			}
			$result = $res_9;
			$this->pos = $pos_9;
			$matcher = 'match_'.'BoolLiteral'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_12 = TRUE; break;
			}
			$result = $res_9;
			$this->pos = $pos_9;
			$_12 = FALSE; break;
		}
		while(0);
		if( $_12 === TRUE ) { $_14 = TRUE; break; }
		$result = $res_7;
		$this->pos = $pos_7;
		$_14 = FALSE; break;
	}
	while(0);
	if( $_14 === TRUE ) { return $this->finalise($result); }
	if( $_14 === FALSE) { return FALSE; }
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
	$_37 = NULL;
	do {
		$_28 = NULL;
		do {
			$_26 = NULL;
			do {
				$res_17 = $result;
				$pos_17 = $this->pos;
				$_20 = NULL;
				do {
					$matcher = 'match_'.'VariableCore'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres, "core" );
					}
					else { $_20 = FALSE; break; }
					$res_19 = $result;
					$pos_19 = $this->pos;
					$matcher = 'match_'.'UnaryOperator'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres, "post" );
					}
					else {
						$result = $res_19;
						$this->pos = $pos_19;
						unset( $res_19 );
						unset( $pos_19 );
					}
					$_20 = TRUE; break;
				}
				while(0);
				if( $_20 === TRUE ) { $_26 = TRUE; break; }
				$result = $res_17;
				$this->pos = $pos_17;
				$_24 = NULL;
				do {
					$res_22 = $result;
					$pos_22 = $this->pos;
					$matcher = 'match_'.'UnaryOperator'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres, "pre" );
					}
					else {
						$result = $res_22;
						$this->pos = $pos_22;
						unset( $res_22 );
						unset( $pos_22 );
					}
					$matcher = 'match_'.'VariableCore'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres, "core" );
					}
					else { $_24 = FALSE; break; }
					$_24 = TRUE; break;
				}
				while(0);
				if( $_24 === TRUE ) { $_26 = TRUE; break; }
				$result = $res_17;
				$this->pos = $pos_17;
				$_26 = FALSE; break;
			}
			while(0);
			if( $_26 === FALSE) { $_28 = FALSE; break; }
			$_28 = TRUE; break;
		}
		while(0);
		if( $_28 === FALSE) { $_37 = FALSE; break; }
		while (true) {
			$res_36 = $result;
			$pos_36 = $this->pos;
			$_35 = NULL;
			do {
				if (substr($this->string,$this->pos,1) == '[') {
					$this->pos += 1;
					$result["text"] .= '[';
				}
				else { $_35 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'Value'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "dereference" );
				}
				else { $_35 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				if (substr($this->string,$this->pos,1) == ']') {
					$this->pos += 1;
					$result["text"] .= ']';
				}
				else { $_35 = FALSE; break; }
				$_35 = TRUE; break;
			}
			while(0);
			if( $_35 === FALSE) {
				$result = $res_36;
				$this->pos = $pos_36;
				unset( $res_36 );
				unset( $pos_36 );
				break;
			}
		}
		$_37 = TRUE; break;
	}
	while(0);
	if( $_37 === TRUE ) { return $this->finalise($result); }
	if( $_37 === FALSE) { return FALSE; }
}


/* ArrayItem: ( key:Expression > ":" )? > value:Expression ) */
protected $match_ArrayItem_typestack = array('ArrayItem');
function match_ArrayItem ($stack = array()) {
	$matchrule = "ArrayItem"; $result = $this->construct($matchrule, $matchrule, null);
	$_46 = NULL;
	do {
		$res_43 = $result;
		$pos_43 = $this->pos;
		$_42 = NULL;
		do {
			$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "key" );
			}
			else { $_42 = FALSE; break; }
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			if (substr($this->string,$this->pos,1) == ':') {
				$this->pos += 1;
				$result["text"] .= ':';
			}
			else { $_42 = FALSE; break; }
			$_42 = TRUE; break;
		}
		while(0);
		if( $_42 === FALSE) {
			$result = $res_43;
			$this->pos = $pos_43;
			unset( $res_43 );
			unset( $pos_43 );
		}
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "value" );
		}
		else { $_46 = FALSE; break; }
		$_46 = TRUE; break;
	}
	while(0);
	if( $_46 === TRUE ) { return $this->finalise($result); }
	if( $_46 === FALSE) { return FALSE; }
}


/* ArrayStructure: "[" > ( > items:ArrayItem ( > "," > items:ArrayItem )* )? > "]" */
protected $match_ArrayStructure_typestack = array('ArrayStructure');
function match_ArrayStructure ($stack = array()) {
	$matchrule = "ArrayStructure"; $result = $this->construct($matchrule, $matchrule, null);
	$_62 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_62 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$res_59 = $result;
		$pos_59 = $this->pos;
		$_58 = NULL;
		do {
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			$matcher = 'match_'.'ArrayItem'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "items" );
			}
			else { $_58 = FALSE; break; }
			while (true) {
				$res_57 = $result;
				$pos_57 = $this->pos;
				$_56 = NULL;
				do {
					if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
					if (substr($this->string,$this->pos,1) == ',') {
						$this->pos += 1;
						$result["text"] .= ',';
					}
					else { $_56 = FALSE; break; }
					if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
					$matcher = 'match_'.'ArrayItem'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres, "items" );
					}
					else { $_56 = FALSE; break; }
					$_56 = TRUE; break;
				}
				while(0);
				if( $_56 === FALSE) {
					$result = $res_57;
					$this->pos = $pos_57;
					unset( $res_57 );
					unset( $pos_57 );
					break;
				}
			}
			$_58 = TRUE; break;
		}
		while(0);
		if( $_58 === FALSE) {
			$result = $res_59;
			$this->pos = $pos_59;
			unset( $res_59 );
			unset( $pos_59 );
		}
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_62 = FALSE; break; }
		$_62 = TRUE; break;
	}
	while(0);
	if( $_62 === TRUE ) { return $this->finalise($result); }
	if( $_62 === FALSE) { return FALSE; }
}


/* Value: skip:Literal | skip:Variable | skip:ArrayStructure */
protected $match_Value_typestack = array('Value');
function match_Value ($stack = array()) {
	$matchrule = "Value"; $result = $this->construct($matchrule, $matchrule, null);
	$_71 = NULL;
	do {
		$res_64 = $result;
		$pos_64 = $this->pos;
		$matcher = 'match_'.'Literal'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_71 = TRUE; break;
		}
		$result = $res_64;
		$this->pos = $pos_64;
		$_69 = NULL;
		do {
			$res_66 = $result;
			$pos_66 = $this->pos;
			$matcher = 'match_'.'Variable'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_69 = TRUE; break;
			}
			$result = $res_66;
			$this->pos = $pos_66;
			$matcher = 'match_'.'ArrayStructure'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_69 = TRUE; break;
			}
			$result = $res_66;
			$this->pos = $pos_66;
			$_69 = FALSE; break;
		}
		while(0);
		if( $_69 === TRUE ) { $_71 = TRUE; break; }
		$result = $res_64;
		$this->pos = $pos_64;
		$_71 = FALSE; break;
	}
	while(0);
	if( $_71 === TRUE ) { return $this->finalise($result); }
	if( $_71 === FALSE) { return FALSE; }
}


/* AddOperator: "+" | "-" */
protected $match_AddOperator_typestack = array('AddOperator');
function match_AddOperator ($stack = array()) {
	$matchrule = "AddOperator"; $result = $this->construct($matchrule, $matchrule, null);
	$_76 = NULL;
	do {
		$res_73 = $result;
		$pos_73 = $this->pos;
		if (substr($this->string,$this->pos,1) == '+') {
			$this->pos += 1;
			$result["text"] .= '+';
			$_76 = TRUE; break;
		}
		$result = $res_73;
		$this->pos = $pos_73;
		if (substr($this->string,$this->pos,1) == '-') {
			$this->pos += 1;
			$result["text"] .= '-';
			$_76 = TRUE; break;
		}
		$result = $res_73;
		$this->pos = $pos_73;
		$_76 = FALSE; break;
	}
	while(0);
	if( $_76 === TRUE ) { return $this->finalise($result); }
	if( $_76 === FALSE) { return FALSE; }
}


/* MultiplyOperator: "*" | "/" */
protected $match_MultiplyOperator_typestack = array('MultiplyOperator');
function match_MultiplyOperator ($stack = array()) {
	$matchrule = "MultiplyOperator"; $result = $this->construct($matchrule, $matchrule, null);
	$_81 = NULL;
	do {
		$res_78 = $result;
		$pos_78 = $this->pos;
		if (substr($this->string,$this->pos,1) == '*') {
			$this->pos += 1;
			$result["text"] .= '*';
			$_81 = TRUE; break;
		}
		$result = $res_78;
		$this->pos = $pos_78;
		if (substr($this->string,$this->pos,1) == '/') {
			$this->pos += 1;
			$result["text"] .= '/';
			$_81 = TRUE; break;
		}
		$result = $res_78;
		$this->pos = $pos_78;
		$_81 = FALSE; break;
	}
	while(0);
	if( $_81 === TRUE ) { return $this->finalise($result); }
	if( $_81 === FALSE) { return FALSE; }
}


/* AssignmentOperator: "=" | "+=" | "-=" | "*=" | "/=" */
protected $match_AssignmentOperator_typestack = array('AssignmentOperator');
function match_AssignmentOperator ($stack = array()) {
	$matchrule = "AssignmentOperator"; $result = $this->construct($matchrule, $matchrule, null);
	$_98 = NULL;
	do {
		$res_83 = $result;
		$pos_83 = $this->pos;
		if (substr($this->string,$this->pos,1) == '=') {
			$this->pos += 1;
			$result["text"] .= '=';
			$_98 = TRUE; break;
		}
		$result = $res_83;
		$this->pos = $pos_83;
		$_96 = NULL;
		do {
			$res_85 = $result;
			$pos_85 = $this->pos;
			if (( $subres = $this->literal( '+=' ) ) !== FALSE) {
				$result["text"] .= $subres;
				$_96 = TRUE; break;
			}
			$result = $res_85;
			$this->pos = $pos_85;
			$_94 = NULL;
			do {
				$res_87 = $result;
				$pos_87 = $this->pos;
				if (( $subres = $this->literal( '-=' ) ) !== FALSE) {
					$result["text"] .= $subres;
					$_94 = TRUE; break;
				}
				$result = $res_87;
				$this->pos = $pos_87;
				$_92 = NULL;
				do {
					$res_89 = $result;
					$pos_89 = $this->pos;
					if (( $subres = $this->literal( '*=' ) ) !== FALSE) {
						$result["text"] .= $subres;
						$_92 = TRUE; break;
					}
					$result = $res_89;
					$this->pos = $pos_89;
					if (( $subres = $this->literal( '/=' ) ) !== FALSE) {
						$result["text"] .= $subres;
						$_92 = TRUE; break;
					}
					$result = $res_89;
					$this->pos = $pos_89;
					$_92 = FALSE; break;
				}
				while(0);
				if( $_92 === TRUE ) { $_94 = TRUE; break; }
				$result = $res_87;
				$this->pos = $pos_87;
				$_94 = FALSE; break;
			}
			while(0);
			if( $_94 === TRUE ) { $_96 = TRUE; break; }
			$result = $res_85;
			$this->pos = $pos_85;
			$_96 = FALSE; break;
		}
		while(0);
		if( $_96 === TRUE ) { $_98 = TRUE; break; }
		$result = $res_83;
		$this->pos = $pos_83;
		$_98 = FALSE; break;
	}
	while(0);
	if( $_98 === TRUE ) { return $this->finalise($result); }
	if( $_98 === FALSE) { return FALSE; }
}


/* ComparisonOperator: "==" | "!=" | ">=" | "<=" | ">" | "<" */
protected $match_ComparisonOperator_typestack = array('ComparisonOperator');
function match_ComparisonOperator ($stack = array()) {
	$matchrule = "ComparisonOperator"; $result = $this->construct($matchrule, $matchrule, null);
	$_119 = NULL;
	do {
		$res_100 = $result;
		$pos_100 = $this->pos;
		if (( $subres = $this->literal( '==' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_119 = TRUE; break;
		}
		$result = $res_100;
		$this->pos = $pos_100;
		$_117 = NULL;
		do {
			$res_102 = $result;
			$pos_102 = $this->pos;
			if (( $subres = $this->literal( '!=' ) ) !== FALSE) {
				$result["text"] .= $subres;
				$_117 = TRUE; break;
			}
			$result = $res_102;
			$this->pos = $pos_102;
			$_115 = NULL;
			do {
				$res_104 = $result;
				$pos_104 = $this->pos;
				if (( $subres = $this->literal( '>=' ) ) !== FALSE) {
					$result["text"] .= $subres;
					$_115 = TRUE; break;
				}
				$result = $res_104;
				$this->pos = $pos_104;
				$_113 = NULL;
				do {
					$res_106 = $result;
					$pos_106 = $this->pos;
					if (( $subres = $this->literal( '<=' ) ) !== FALSE) {
						$result["text"] .= $subres;
						$_113 = TRUE; break;
					}
					$result = $res_106;
					$this->pos = $pos_106;
					$_111 = NULL;
					do {
						$res_108 = $result;
						$pos_108 = $this->pos;
						if (substr($this->string,$this->pos,1) == '>') {
							$this->pos += 1;
							$result["text"] .= '>';
							$_111 = TRUE; break;
						}
						$result = $res_108;
						$this->pos = $pos_108;
						if (substr($this->string,$this->pos,1) == '<') {
							$this->pos += 1;
							$result["text"] .= '<';
							$_111 = TRUE; break;
						}
						$result = $res_108;
						$this->pos = $pos_108;
						$_111 = FALSE; break;
					}
					while(0);
					if( $_111 === TRUE ) { $_113 = TRUE; break; }
					$result = $res_106;
					$this->pos = $pos_106;
					$_113 = FALSE; break;
				}
				while(0);
				if( $_113 === TRUE ) { $_115 = TRUE; break; }
				$result = $res_104;
				$this->pos = $pos_104;
				$_115 = FALSE; break;
			}
			while(0);
			if( $_115 === TRUE ) { $_117 = TRUE; break; }
			$result = $res_102;
			$this->pos = $pos_102;
			$_117 = FALSE; break;
		}
		while(0);
		if( $_117 === TRUE ) { $_119 = TRUE; break; }
		$result = $res_100;
		$this->pos = $pos_100;
		$_119 = FALSE; break;
	}
	while(0);
	if( $_119 === TRUE ) { return $this->finalise($result); }
	if( $_119 === FALSE) { return FALSE; }
}


/* UnaryOperator: "++" | "--" */
protected $match_UnaryOperator_typestack = array('UnaryOperator');
function match_UnaryOperator ($stack = array()) {
	$matchrule = "UnaryOperator"; $result = $this->construct($matchrule, $matchrule, null);
	$_124 = NULL;
	do {
		$res_121 = $result;
		$pos_121 = $this->pos;
		if (( $subres = $this->literal( '++' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_124 = TRUE; break;
		}
		$result = $res_121;
		$this->pos = $pos_121;
		if (( $subres = $this->literal( '--' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_124 = TRUE; break;
		}
		$result = $res_121;
		$this->pos = $pos_121;
		$_124 = FALSE; break;
	}
	while(0);
	if( $_124 === TRUE ) { return $this->finalise($result); }
	if( $_124 === FALSE) { return FALSE; }
}


/* Expression: skip:Assignment | skip:Comparison | skip:Addition */
protected $match_Expression_typestack = array('Expression');
function match_Expression ($stack = array()) {
	$matchrule = "Expression"; $result = $this->construct($matchrule, $matchrule, null);
	$_133 = NULL;
	do {
		$res_126 = $result;
		$pos_126 = $this->pos;
		$matcher = 'match_'.'Assignment'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_133 = TRUE; break;
		}
		$result = $res_126;
		$this->pos = $pos_126;
		$_131 = NULL;
		do {
			$res_128 = $result;
			$pos_128 = $this->pos;
			$matcher = 'match_'.'Comparison'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_131 = TRUE; break;
			}
			$result = $res_128;
			$this->pos = $pos_128;
			$matcher = 'match_'.'Addition'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_131 = TRUE; break;
			}
			$result = $res_128;
			$this->pos = $pos_128;
			$_131 = FALSE; break;
		}
		while(0);
		if( $_131 === TRUE ) { $_133 = TRUE; break; }
		$result = $res_126;
		$this->pos = $pos_126;
		$_133 = FALSE; break;
	}
	while(0);
	if( $_133 === TRUE ) { return $this->finalise($result); }
	if( $_133 === FALSE) { return FALSE; }
}


/* Comparison: left:Addition > op:ComparisonOperator > right:Addition */
protected $match_Comparison_typestack = array('Comparison');
function match_Comparison ($stack = array()) {
	$matchrule = "Comparison"; $result = $this->construct($matchrule, $matchrule, null);
	$_140 = NULL;
	do {
		$matcher = 'match_'.'Addition'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "left" );
		}
		else { $_140 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'ComparisonOperator'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "op" );
		}
		else { $_140 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Addition'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "right" );
		}
		else { $_140 = FALSE; break; }
		$_140 = TRUE; break;
	}
	while(0);
	if( $_140 === TRUE ) { return $this->finalise($result); }
	if( $_140 === FALSE) { return FALSE; }
}


/* Assignment: left:Variable > op:AssignmentOperator > right:Expression */
protected $match_Assignment_typestack = array('Assignment');
function match_Assignment ($stack = array()) {
	$matchrule = "Assignment"; $result = $this->construct($matchrule, $matchrule, null);
	$_147 = NULL;
	do {
		$matcher = 'match_'.'Variable'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "left" );
		}
		else { $_147 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'AssignmentOperator'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "op" );
		}
		else { $_147 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "right" );
		}
		else { $_147 = FALSE; break; }
		$_147 = TRUE; break;
	}
	while(0);
	if( $_147 === TRUE ) { return $this->finalise($result); }
	if( $_147 === FALSE) { return FALSE; }
}


/* Addition: operands:Multiplication ( > ops:AddOperator > operands:Multiplication)* */
protected $match_Addition_typestack = array('Addition');
function match_Addition ($stack = array()) {
	$matchrule = "Addition"; $result = $this->construct($matchrule, $matchrule, null);
	$_156 = NULL;
	do {
		$matcher = 'match_'.'Multiplication'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "operands" );
		}
		else { $_156 = FALSE; break; }
		while (true) {
			$res_155 = $result;
			$pos_155 = $this->pos;
			$_154 = NULL;
			do {
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'AddOperator'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "ops" );
				}
				else { $_154 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'Multiplication'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "operands" );
				}
				else { $_154 = FALSE; break; }
				$_154 = TRUE; break;
			}
			while(0);
			if( $_154 === FALSE) {
				$result = $res_155;
				$this->pos = $pos_155;
				unset( $res_155 );
				unset( $pos_155 );
				break;
			}
		}
		$_156 = TRUE; break;
	}
	while(0);
	if( $_156 === TRUE ) { return $this->finalise($result); }
	if( $_156 === FALSE) { return FALSE; }
}


/* Multiplication: operands:Operand ( > ops:MultiplyOperator > operands:Operand)* */
protected $match_Multiplication_typestack = array('Multiplication');
function match_Multiplication ($stack = array()) {
	$matchrule = "Multiplication"; $result = $this->construct($matchrule, $matchrule, null);
	$_165 = NULL;
	do {
		$matcher = 'match_'.'Operand'; $key = $matcher; $pos = $this->pos;
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
				$matcher = 'match_'.'MultiplyOperator'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "ops" );
				}
				else { $_163 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'Operand'; $key = $matcher; $pos = $this->pos;
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


/* Operand: ( "(" > skip:Expression > ")" ) | skip:Value */
protected $match_Operand_typestack = array('Operand');
function match_Operand ($stack = array()) {
	$matchrule = "Operand"; $result = $this->construct($matchrule, $matchrule, null);
	$_176 = NULL;
	do {
		$res_167 = $result;
		$pos_167 = $this->pos;
		$_173 = NULL;
		do {
			if (substr($this->string,$this->pos,1) == '(') {
				$this->pos += 1;
				$result["text"] .= '(';
			}
			else { $_173 = FALSE; break; }
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
			}
			else { $_173 = FALSE; break; }
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			if (substr($this->string,$this->pos,1) == ')') {
				$this->pos += 1;
				$result["text"] .= ')';
			}
			else { $_173 = FALSE; break; }
			$_173 = TRUE; break;
		}
		while(0);
		if( $_173 === TRUE ) { $_176 = TRUE; break; }
		$result = $res_167;
		$this->pos = $pos_167;
		$matcher = 'match_'.'Value'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_176 = TRUE; break;
		}
		$result = $res_167;
		$this->pos = $pos_167;
		$_176 = FALSE; break;
	}
	while(0);
	if( $_176 === TRUE ) { return $this->finalise($result); }
	if( $_176 === FALSE) { return FALSE; }
}


/* IfStatement: "if" > "(" > left:Expression > ")" > ( right:Block ) > */
protected $match_IfStatement_typestack = array('IfStatement');
function match_IfStatement ($stack = array()) {
	$matchrule = "IfStatement"; $result = $this->construct($matchrule, $matchrule, null);
	$_190 = NULL;
	do {
		if (( $subres = $this->literal( 'if' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_190 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_190 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "left" );
		}
		else { $_190 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_190 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$_187 = NULL;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "right" );
			}
			else { $_187 = FALSE; break; }
			$_187 = TRUE; break;
		}
		while(0);
		if( $_187 === FALSE) { $_190 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$_190 = TRUE; break;
	}
	while(0);
	if( $_190 === TRUE ) { return $this->finalise($result); }
	if( $_190 === FALSE) { return FALSE; }
}


/* WhileStatement: "while" > "(" > left:Expression > ")" > ( right:Block ) > */
protected $match_WhileStatement_typestack = array('WhileStatement');
function match_WhileStatement ($stack = array()) {
	$matchrule = "WhileStatement"; $result = $this->construct($matchrule, $matchrule, null);
	$_204 = NULL;
	do {
		if (( $subres = $this->literal( 'while' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_204 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_204 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "left" );
		}
		else { $_204 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_204 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$_201 = NULL;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "right" );
			}
			else { $_201 = FALSE; break; }
			$_201 = TRUE; break;
		}
		while(0);
		if( $_201 === FALSE) { $_204 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$_204 = TRUE; break;
	}
	while(0);
	if( $_204 === TRUE ) { return $this->finalise($result); }
	if( $_204 === FALSE) { return FALSE; }
}


/* ForeachStatement: "foreach" > "(" > left:Expression > "as" > item:VariableCore > ")" > ( right:Block ) > */
protected $match_ForeachStatement_typestack = array('ForeachStatement');
function match_ForeachStatement ($stack = array()) {
	$matchrule = "ForeachStatement"; $result = $this->construct($matchrule, $matchrule, null);
	$_222 = NULL;
	do {
		if (( $subres = $this->literal( 'foreach' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_222 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_222 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "left" );
		}
		else { $_222 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (( $subres = $this->literal( 'as' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_222 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'VariableCore'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "item" );
		}
		else { $_222 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_222 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$_219 = NULL;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "right" );
			}
			else { $_219 = FALSE; break; }
			$_219 = TRUE; break;
		}
		while(0);
		if( $_219 === FALSE) { $_222 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$_222 = TRUE; break;
	}
	while(0);
	if( $_222 === TRUE ) { return $this->finalise($result); }
	if( $_222 === FALSE) { return FALSE; }
}


/* BlockStatements: skip:IfStatement | skip:WhileStatement | skip:ForeachStatement */
protected $match_BlockStatements_typestack = array('BlockStatements');
function match_BlockStatements ($stack = array()) {
	$matchrule = "BlockStatements"; $result = $this->construct($matchrule, $matchrule, null);
	$_231 = NULL;
	do {
		$res_224 = $result;
		$pos_224 = $this->pos;
		$matcher = 'match_'.'IfStatement'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_231 = TRUE; break;
		}
		$result = $res_224;
		$this->pos = $pos_224;
		$_229 = NULL;
		do {
			$res_226 = $result;
			$pos_226 = $this->pos;
			$matcher = 'match_'.'WhileStatement'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_229 = TRUE; break;
			}
			$result = $res_226;
			$this->pos = $pos_226;
			$matcher = 'match_'.'ForeachStatement'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_229 = TRUE; break;
			}
			$result = $res_226;
			$this->pos = $pos_226;
			$_229 = FALSE; break;
		}
		while(0);
		if( $_229 === TRUE ) { $_231 = TRUE; break; }
		$result = $res_224;
		$this->pos = $pos_224;
		$_231 = FALSE; break;
	}
	while(0);
	if( $_231 === TRUE ) { return $this->finalise($result); }
	if( $_231 === FALSE) { return FALSE; }
}


/* CommandStatements: skip:EchoStatement */
protected $match_CommandStatements_typestack = array('CommandStatements');
function match_CommandStatements ($stack = array()) {
	$matchrule = "CommandStatements"; $result = $this->construct($matchrule, $matchrule, null);
	$matcher = 'match_'.'EchoStatement'; $key = $matcher; $pos = $this->pos;
	$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
	if ($subres !== FALSE) {
		$this->store( $result, $subres, "skip" );
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* EchoStatement: "echo" > subject:Expression */
protected $match_EchoStatement_typestack = array('EchoStatement');
function match_EchoStatement ($stack = array()) {
	$matchrule = "EchoStatement"; $result = $this->construct($matchrule, $matchrule, null);
	$_237 = NULL;
	do {
		if (( $subres = $this->literal( 'echo' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_237 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "subject" );
		}
		else { $_237 = FALSE; break; }
		$_237 = TRUE; break;
	}
	while(0);
	if( $_237 === TRUE ) { return $this->finalise($result); }
	if( $_237 === FALSE) { return FALSE; }
}


/* Statement: ( skip:BlockStatements SPACE ";"? ) | ( ( skip:CommandStatements | skip:Expression ) SPACE ";" ) */
protected $match_Statement_typestack = array('Statement');
function match_Statement ($stack = array()) {
	$matchrule = "Statement"; $result = $this->construct($matchrule, $matchrule, null);
	$_256 = NULL;
	do {
		$res_239 = $result;
		$pos_239 = $this->pos;
		$_243 = NULL;
		do {
			$matcher = 'match_'.'BlockStatements'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
			}
			else { $_243 = FALSE; break; }
			$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_243 = FALSE; break; }
			$res_242 = $result;
			$pos_242 = $this->pos;
			if (substr($this->string,$this->pos,1) == ';') {
				$this->pos += 1;
				$result["text"] .= ';';
			}
			else {
				$result = $res_242;
				$this->pos = $pos_242;
				unset( $res_242 );
				unset( $pos_242 );
			}
			$_243 = TRUE; break;
		}
		while(0);
		if( $_243 === TRUE ) { $_256 = TRUE; break; }
		$result = $res_239;
		$this->pos = $pos_239;
		$_254 = NULL;
		do {
			$_250 = NULL;
			do {
				$_248 = NULL;
				do {
					$res_245 = $result;
					$pos_245 = $this->pos;
					$matcher = 'match_'.'CommandStatements'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres, "skip" );
						$_248 = TRUE; break;
					}
					$result = $res_245;
					$this->pos = $pos_245;
					$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres, "skip" );
						$_248 = TRUE; break;
					}
					$result = $res_245;
					$this->pos = $pos_245;
					$_248 = FALSE; break;
				}
				while(0);
				if( $_248 === FALSE) { $_250 = FALSE; break; }
				$_250 = TRUE; break;
			}
			while(0);
			if( $_250 === FALSE) { $_254 = FALSE; break; }
			$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_254 = FALSE; break; }
			if (substr($this->string,$this->pos,1) == ';') {
				$this->pos += 1;
				$result["text"] .= ';';
			}
			else { $_254 = FALSE; break; }
			$_254 = TRUE; break;
		}
		while(0);
		if( $_254 === TRUE ) { $_256 = TRUE; break; }
		$result = $res_239;
		$this->pos = $pos_239;
		$_256 = FALSE; break;
	}
	while(0);
	if( $_256 === TRUE ) { return $this->finalise($result); }
	if( $_256 === FALSE) { return FALSE; }
}


/* Block: "{" > skip:Program > "}" */
protected $match_Block_typestack = array('Block');
function match_Block ($stack = array()) {
	$matchrule = "Block"; $result = $this->construct($matchrule, $matchrule, null);
	$_263 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '{') {
			$this->pos += 1;
			$result["text"] .= '{';
		}
		else { $_263 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Program'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
		}
		else { $_263 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == '}') {
			$this->pos += 1;
			$result["text"] .= '}';
		}
		else { $_263 = FALSE; break; }
		$_263 = TRUE; break;
	}
	while(0);
	if( $_263 === TRUE ) { return $this->finalise($result); }
	if( $_263 === FALSE) { return FALSE; }
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
	$_273 = NULL;
	do {
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$count = 0;
		while (true) {
			$res_271 = $result;
			$pos_271 = $this->pos;
			$_270 = NULL;
			do {
				$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_270 = FALSE; break; }
				$matcher = 'match_'.'Statement'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_270 = FALSE; break; }
				$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_270 = FALSE; break; }
				$_270 = TRUE; break;
			}
			while(0);
			if( $_270 === FALSE) {
				$result = $res_271;
				$this->pos = $pos_271;
				unset( $res_271 );
				unset( $pos_271 );
				break;
			}
			$count++;
		}
		if ($count >= 1) {  }
		else { $_273 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$_273 = TRUE; break;
	}
	while(0);
	if( $_273 === TRUE ) { return $this->finalise($result); }
	if( $_273 === FALSE) { return FALSE; }
}




}