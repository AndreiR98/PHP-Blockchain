<?php
namespace ECDSA;

class Key{

	public $d;

	public $x;

	public $y;

	public $KID;

	public $curve;

	public function __construct($pem='', $KID='', $curve, $algorithm){
		if(openssl_pkey_get_private($pem)){
			$res = openssl_pkey_get_private($pem);
			$key_res = openssl_pkey_get_details($res)['ec'];
            
            $this->d = $key_res['d'];
			$this->x = $key_res['x'];
			$this->y = $key_res['y'];
		}else{
			$res = openssl_pkey_get_public($pem);
			$key_res = openssl_pkey_get_details($res)['ec'];

            $this->d = 1;
			$this->x = $key_res['x'];
			$this->y = $key_res['y'];
		}

		$this->KID = $KID;
		$this->curve = $curve;
		$this->algorithm = $algorithm;
	}
}
?>