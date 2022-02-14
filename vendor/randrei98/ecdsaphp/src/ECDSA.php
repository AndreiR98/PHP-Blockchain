<?php

/**
 * Class EcDSA
 *
 * @package ecdsa/ecdsaphp
 * @author  Rotaru Andrei <rotaru.andrei98@gmail.com>
 */

namespace ECDSA;

use ECDSA\PointJacobi;
use ECDSA\Math;
use ECDSA\Curves;
use ECDSA\Algorithms;

Class ECDSA {
    /**
     * @param string $message
     * 
     * 
     * @param pem $secretKey
     *
     * @return array
     */
    public static function Sign($message, $Key){        
        //Recover the secret key from pem by KID
        $secretKey = Math::hex2int(Math::hexlify($Key->d));

        $curve = $Key->curve;
        $algorithm = $Key->algorithm;

        $order = $curve->N;
        
        //Recover the hash method for this curve
        $hash = $algorithm->hash;

        
        $k  = (Math::hex2int(hash_hmac($hash, $message, $secretKey))) % $order;

        [$x, $y] = $curve->generator();

        $h = (Math::hex2int(openssl_digest($message, $hash))) % $order;

        $generator = new PointJacobi(new ECpoint($x, $y, 1), $curve);

        $rx = $generator->_mul($k)->to_affine()['x'];

        $s = (gmp_invert($k, $order)*($h + ($rx * $secretKey) % $order)) % $order;
       
        return ['r'=>Math::unhexlify(Math::int2hex($rx)), 's'=>Math::unhexlify(Math::int2hex($s))];

        

    }

    /**
     * @param string $message
     * 
     * 
     * @param array(r, s) $signature
     * 
     * @param byte key
     *
     * @return bool
     */

    public static function Verify($message, $signature, $key){

        $Px = Math::hex2int(Math::hexlify($key->x));
        $Py = Math::hex2int(Math::hexlify($key->y));

        $r = Math::hex2int(Math::hexlify($signature['r']));
        $s = Math::hex2int(Math::hexlify($signature['s']));

        $curve = $key->curve;
        $algorithm = $key->algorithm;
        $order = $curve->order();

        [$x, $y] = $curve->generator();

        $generator = new PointJacobi(new ECpoint($x, $y, 1), $curve);
        
        $publicPoint = new PointJacobi(new ECpoint($Px, $Py, 1), $curve);

        $hash = (Math::hex2int(openssl_digest($message, $algorithm->hash))) % $order;

        $c = gmp_invert($s, $order);
        $u1 = ($hash * $c) % $order;
        $u2 = ($r * $c) % $order;

        $pu1 = $generator->_mul($u1);
        $pu2 = $publicPoint->_mul($u2);

        $publicVerificationPoint = $pu1->add($pu2);
        $vx = $publicVerificationPoint->xs() % $order;
      
        return $r == $vx;
    }



    /**
     * @param int secretKey
     * 
     * 
     * @param object Curve
     *
     * @return object JacobiPoint
     */
    public function GetPublicKey($secretKey, $curve){
        [$x, $y] = $curve->generator();

        $generator = new PointJacobi(new ECpoint($x, $y, 1), $curve);

        $publicPoint = $generator->_mul($secretKey);

        return $publicPoint->to_affine();
    }
}