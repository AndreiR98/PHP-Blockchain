<?php
declare(strict_types=1);

namespace ECDSA;

Class Math {

	public static function hexlify($str){
		$str = unpack("H*", $str);
		$str = $str[1];
		return $str;
	}
	public static function unhexlify($str){
		$str = pack("H*", $str);
		return $str;
	}
	public static function hex2int($hex){
		$v = [
			'0'=>'0',
   		    '1'=>'1',
   		    '2'=>'2',
   		    '3'=>'3',
   		    '4'=>'4',
   		    '5'=>'5',
   		    '6'=>'6',
   		    '7'=>'7',
   		    '8'=>'8',
   		    '9'=>'9',
   		    'a'=>'10',
   		    'b'=>'11',
   		    'c'=>'12',
   		    'd'=>'13',
   		    'e'=>'14',
   		    'f'=>'15'
   	    ];

   	    $split = str_split($hex);
   	    $len = count($split)-1;
   	    $value = 0;

   	    foreach($split as $s=>$p){
   	    	$value += $v[$p]*gmp_pow(16, ($len-$s));
   	    }

   	    return $value;
	}

	public static function int2hex($int){
		$hex = [];
		while ($int > 0){
			[$q, $r] = gmp_div_qr($int, 16);
			$int = $q;
			array_push($hex, dechex((int)$r));
		}

		$hex = implode("", array_reverse($hex));

		return $hex;
	}
}