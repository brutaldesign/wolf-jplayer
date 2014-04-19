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

if ( ! function_exists( 'wolf_jpayer_admin_notices' ) ) {
	/**
	 * Custom admin notice
	 *
	 * @param string $message
	 * @param string $type
	 * @param bool $dismiss
	 * @param int $id
	 */
	function wolf_jpayer_admin_notices( $message = null, $type = null, $dismiss = false, $id = null ) {
		
		if ( $dismiss ){

			$dismiss = __( 'Hide permanently', 'wolf' );

			if ( $id ){
				if ( ! isset( $_COOKIE[ $id ] ) )
					echo '<div class="'.$type.'"><p>' . $message . '<span class="wolf-close-admin-notice">&times;</span><span id="' . $id . '" class="wolf-dismiss-admin-notice">' . $dismiss . '</span></p></div>';
			} else {
				echo '<div class="'.$type.'"><p>' . $message . '<span class="wolf-close-admin-notice">&times;</span><span class="wolf-dismiss-admin-notice">' . $dismiss . '</span></p></div>';
			}
		} else {
			echo '<div class="'.$type.'"><p>' . $message . '</p></div>';
		}

		return false;
	}
	add_action( 'admin_notices', 'wolf_jpayer_admin_notices'  );
}