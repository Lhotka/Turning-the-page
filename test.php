<?php
if (extension_loaded('gd') && function_exists('gd_info')) {
    echo "GD is enabled";
} else {
    echo "GD is not enabled";
}
$functions = ['imagecreatefromjpeg', 'imagecreatetruecolor', 'imagecopyresampled', 'imagejpeg'];

foreach ($functions as $function) {
    echo $function . ': ' . (function_exists($function) ? 'available' : 'not available') . '<br>';
}
?>
