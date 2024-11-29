<?php
session_start();

// Generate a random CAPTCHA code
$captcha_code = '';
$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
$characters_length = strlen($characters);
for ($i = 0; $i < 5; $i++) {
    $captcha_code .= $characters[rand(0, $characters_length - 1)];
}
$_SESSION['captcha_code'] = $captcha_code;

// Create a larger CAPTCHA image (wider and taller)
$im = imagecreatetruecolor(200, 70);

// Check if the GD library is working
if (!$im) {
    die("Error: Could not initialize image creation. Make sure the GD library is installed.");
}

// Set background and text colors
$bg = imagecolorallocate($im, 0, 128, 0); // Background color (Green)
$fg = imagecolorallocate($im, 255, 255, 255); // Text color (White)
$line_color = imagecolorallocate($im, 64, 64, 64); // Line color (Gray)
$dot_color = imagecolorallocate($im, 255, 255, 255); // Dot color (White)

imagefill($im, 0, 0, $bg);

// Add random lines
for ($i = 0; $i < 10; $i++) {
    imageline($im, rand(0, 200), rand(0, 70), rand(0, 200), rand(0, 70), $line_color);
}

// Add random dots
for ($i = 0; $i < 1000; $i++) {
    imagesetpixel($im, rand(0, 200), rand(0, 70), $dot_color);
}

// Use a TrueType font for larger text
$font_file = 'captcha.ttf'; // Path to your TrueType font file
$font_size = 20; // Font size

// Adjust the positioning (X and Y) for each character
$spacing = 20; // Space between characters
$start_x = 10; // Starting X position for the first character
$start_y = 50; // Y position for the text

// Loop through each character and display it with spacing
for ($i = 0; $i < strlen($captcha_code); $i++) {
    $x = $start_x + ($i * ($font_size + $spacing)); // Calculate X position with spacing
    imagettftext($im, $font_size, rand(-10, 10), $x, $start_y, $fg, $font_file, $captcha_code[$i]);
}

// Output the image as a PNG
header('Content-type: image/png');
imagepng($im);

// Free memory
imagedestroy($im);
?>
