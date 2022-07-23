<?php
    $width = 1024;
    $height = 1024;
    $image = imagecreatetruecolor($width, $height);

    for ($i = 0; $i <= $width; ++$i) {
        for ($j = 0; $j <= $height; ++$j) {
            $col = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
            imagesetpixel($image, $i, $j, $col);
        }
    }

    header("Content-type: image/png");
    imagepng($image);
    imagedestroy($image);
?>
