<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! function_exists( 'wolf_get_jplayer_option' ) ) {
	/**
	 * Get jPlayer options 
	 *
	 * @param string $value
	 * @return string
	 */
	function wolf_get_jplayer_option( $value ) {
		global $wolf_jplayer;
		return $wolf_jplayer->get_option( $value );

	}
}

if ( ! function_exists( 'wolf_jplayer_hex_to_rgb' ) ) {
	/**
	 * Convert Hex color value to rgba
	 *
	 * @access public
	 * @return string
	 */
	function wolf_jplayer_hex_to_rgb( $hex ) {
		$hex = str_replace( '#', '', $hex );

		if ( strlen( $hex ) == 3 ) {
			$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex,0,1 ) );
			$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex,1,1 ) );
			$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex,2,1 ) );
		} else {
			$r = hexdec( substr( $hex, 0, 2 ) );
			$g = hexdec( substr( $hex, 2, 2 ) );
			$b = hexdec( substr( $hex, 4, 2 ) );
		}
		$rgb = array( $r, $g, $b );
		return implode( ',', $rgb ); // returns the rgb values separated by commas
		//return $rgb; // returns an array with the rgb values
	}
}