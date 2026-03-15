<?php
$source = "logo-wq.png";
$img = imagecreatefrompng($source);
$w = imagesx($img);
$h = imagesy($img);

function resizeAndSave($img, $w, $h, $newSize, $dest) {
    if (!imageistruecolor($img)) {
        imagepalettetotruecolor($img);
    }
    $newImg = imagecreatetruecolor($newSize, $newSize);
    imagealphablending($newImg, false);
    imagesavealpha($newImg, true);
    $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
    imagefilledrectangle($newImg, 0, 0, $newSize, $newSize, $transparent);
    imagecopyresampled($newImg, $img, 0, 0, 0, 0, $newSize, $newSize, $w, $h);
    imagepng($newImg, $dest);
    return $newImg;
}

// Generate 192 and 512 for manifest
resizeAndSave($img, $w, $h, 192, "logo-192.png");
resizeAndSave($img, $w, $h, 512, "logo-512.png");

// Save 16x16 as ico header (simple uncompressed ICO containing PNG, supported by modern Windows)
$iconImg = resizeAndSave($img, $w, $h, 64, "logo-64.png");

// We can just use a simple favicon generation logic by wrapping the PNG in an ICO header.
// Windows technically accepts PNG based icons natively if wrapped in ICO format.
$pngData = file_get_contents("logo-64.png");
$icoHeader = pack("vvv", 0, 1, 1);
$icoDir = pack("C C C C v v V V", 
    64, // width
    64, // height
    0, // color count
    0, // reserved
    1, // planes
    32, // bpp
    strlen($pngData), // size
    22 // offset
);
file_put_contents("favicon.ico", $icoHeader . $icoDir . $pngData);

echo "Done generating icons!";
?>
