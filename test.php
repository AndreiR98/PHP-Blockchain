<?php

require(__DIR__.'/vendor/autoload.php');


use Blockchain\Blocks;
use Blockchain\Chains;
use Tuupola\Base58;
use ECDSA\Math;
use \FurqanSiddiqui\BIP39\BIP39;
use ECDSA\ECDSA;
use ECDSA\Curves;

$mnemonic = BIP39::Generate(12);

$phrase = join(" ", $mnemonic->words);

//echo $phrase."<br>";

$base58 = new Base58([
	"characters"=>Base58::BITCOIN,
	"check"=>true,
	"version"=>0x00
]);

$string = '5J4xMQADyktRPmPTkHYLxkAwJAj4PQuDAYHtNxj3sPXepZdXfaw';
$key = '0c28fca386c7a227600b2fe50b7cae11ec86d3bf1fbe471be89827e19d72aa1d';

$curve = Curves::SECP256k1();

$public_key = ECDSA::GetPublicKey(Math::hex2int($key), $curve);

[$x, $y] = [$public_key['x'], $public_key['y']];

$x = Math::int2hex($x);

$x = "03".$x;

$x_sha = hash('sha256', Math::unhexlify($x));

$ecrypted_pk = hash('ripemd160', Math::unhexlify($x_sha));

echo "Public WIF KEY:".$base58->encode(Math::unhexlify($ecrypted_pk))."<br>";



//ENCODE PRIVATE KEY
$base58_private = new Base58([
	"characters"=>Base58::BITCOIN,
	"check"=>true,
	"version"=>0x80
]);

echo "Private WIF key:".$base58_private->encode(Math::unhexlify($key));

$image = imagecreate(9, 9);

for($i = 1; $i <= 9; $i++){
	for($j = 1; $j <= 9; $j++){
		$color = imagecolorallocate($image, rand(0, 255), rand(0,255), rand(0,255));
		imagesetpixel($image, $i, $j, $color);
	}
}


header("Content-Type: image/png");
  
imagepng($image);
imagedestroy($image);



/*echo $base58->encode(Math::unhexlify($ripemd))."<br>";

$key = "80".$key;

$key_hash = hash('sha256', hash('sha256', $key));

$suf =  substr($key_hash, 0, 8)."<br>";


//echo $key."<br>";
$to_encode =  $key.$suf;

echo $base58->encode(Math::unhexlify('9169ca3e0daee5f3dbebc34f6f1a3cdfc4ca27bdda4e58e548475a5bedac96fe'));

//echo Math::hex2int(Math::hexlify($base58->decode($string)))."<br>";
//echo $base58->encode($key)."<br>";
//echo Math::hex2int('9169ca3e0daee5f3dbebc34f6f1a3cdfc4ca27bdda4e58e548475a5bedac96fe');



$chain = new Chains();
//$block = Blocks::test_block($chain);

//echo $block;*/