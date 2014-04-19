<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpdb; 

$wolf_jplayer_table = $wpdb->prefix.'wolf_jplayer';

include_once( WOLF_JPLAYER_PLUGIN_DIR . '/includes/admin/admin-functions.php' );
include_once( WOLF_JPLAYER_PLUGIN_DIR . '/includes/admin/admin-playlist-functions.php' );
include_once( WOLF_JPLAYER_PLUGIN_DIR . '/includes/admin/admin-song-functions.php' );
?>
<div class="wrap">
	<div id="icon-options-general" class="icon32"></div>
	<h2>jPlayer</h2>
<?php 
if ( ! isset( $_GET['playlist_id'] ) ) {
	/**
	 * Playlists
	 */
	include_once( WOLF_JPLAYER_PLUGIN_DIR . '/includes/admin/admin-playlists.php' );

} elseif ( isset( $_GET['playlist_id'] ) ) {

	/**
	 * Songs
	 */
	include_once( WOLF_JPLAYER_PLUGIN_DIR . '/includes/admin/admin-songs.php' );

}
?></div><!--  end .wrap -->
