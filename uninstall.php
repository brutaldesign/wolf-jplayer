<?php
/**
 * WolfJplayer Uninstall
 *
 * Uninstalling WolfJplayer deletes tables
 *
 * @author WpWolf
 * @package WolfJplayer/Uninstaller
 * @since 2.1.2
 */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) 
	exit();

global $wpdb;

// Tables
$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'wolf_jplayer_playlists' );
$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'wolf_jplayer' );