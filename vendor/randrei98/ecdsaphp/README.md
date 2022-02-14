# ECDSA-PHP
 Elliptic Curve Cryptography for PHP
 
 ##Usage
 
 ```php
 use \ECDSA\Key;
use \ECDSA\Curves;
use \ECDSA\Algorithms;
use \ECDSA\ECDSA;

$pem = 'EC PRIVATE KEY PEM FORMAT';

$curve = Curves::NIST256p();
$algorithm = Algorithms::ES256();

$key = new Key($pem, '', $curve, $algorithm);

$message = 'HELLO';

$Signature = ECDSA::Sign($message, $key);

$verif = ECDSA::Verify($message, $Signature, $key);

var_dump($verif);
 ```
