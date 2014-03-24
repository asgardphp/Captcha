<?php
namespace Asgard\Captcha\Libs;

class Captcha {
	protected static $font = 'monofont.ttf';
 
	protected static function generateCode($characters=10) {
		/* list all possible characters, similar looking characters and vowels have been removed */
		$possible = '23456789bcdfghjkmnpqrstvwxyz';
		// $possible = '123456789abcdefghijklmnopqrstuvwxyz';
		$code = '';
		$i = 0;
		while ($i < $characters) { 
			$code .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
			$i++;
		}
		return $code;
	}
 
	public static function image($width='120',$height='40',$characters='6') {
		$font = dirname(__FILE__).'/../'.static::$font;

		$code = static::generateCode($characters);
		\Asgard\Core\App::get('session')->set('captcha', $code);
		/* font size will be 75% of the image height */
		$font_size = $height * 0.75;
		if(!$image = @imagecreate($width, $height))
			throw new Exception('Cannot initialize new GD image stream');
		/* set the colours */
		$background_color = imagecolorallocate($image, 255, 255, 255);
		$text_color = imagecolorallocate($image, 20, 40, 100);
		$noise_color = imagecolorallocate($image, 100, 120, 180);
		/* generate random dots in background */
		for( $i=0; $i<($width*$height)/3; $i++ ) {
			imagefilledellipse($image, mt_rand(0,$width), mt_rand(0,$height), 1, 1, $noise_color);
		}
		/* generate random lines in background */
		for( $i=0; $i<($width*$height)/150; $i++ ) {
			imageline($image, mt_rand(0,$width), mt_rand(0,$height), mt_rand(0,$width), mt_rand(0,$height), $noise_color);
		}
		/* create textbox and add text */
		if(!$textbox = imagettfbbox($font_size, 0, $font, $code))
			throw new Exception('Error in imagettfbbox function');
		$x = ($width - $textbox[4])/2;
		$y = ($height - $textbox[5])/2;
		if(!imagettftext($image, $font_size, 0, $x, $y, $text_color, $font , $code))
			throw new Exception('Error in imagettftext function');
		return $image;
	}

	public static function test($val) {
		return \Asgard\Core\App::get('session')->get('captcha') == $val;
	}
}