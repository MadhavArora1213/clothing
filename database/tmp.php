<?php
$dir = dirname(__DIR__) . '/images/';
foreach (['hero1.png', 'hero2.png', 'hero3.png', 'desktophero3.png', 'tablethero3.png'] as $f) {
    $path = $dir . $f;
    $img = @getimagesize($path);
    if ($img) {
        echo $f . ': ' . $img[0] . 'x' . $img[1] . ' (' . round(filesize($path) / 1024) . 'KB)' . PHP_EOL;
    } else {
        echo $f . ': NOT FOUND' . PHP_EOL;
    }
}
