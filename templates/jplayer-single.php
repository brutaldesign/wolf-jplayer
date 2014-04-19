<?php 
include_once( WOLF_JPLAYER_PLUGIN_DIR . '/includes/wp.php' );
$playlist_id = null;

if ( isset( $_GET['playlist_id'] ) ){

	$playlist_id = intval( $_GET['playlist_id'] );
}

if ( ! function_exists( 'wolf_jplayer_playlist_wp_title' ) ) {
	/**
	 * Display Playlist name in browser tab
	 *
	 * @param string $title
	 */
	function wolf_jplayer_playlist_wp_title( $title ) {
		global $wpdb, $playlist_id;
		$wolf_jplayer_playlists_table = $wpdb->prefix.'wolf_jplayer_playlists';
		$playlist = $wpdb->get_row( "SELECT * FROM $wolf_jplayer_playlists_table WHERE id = '$playlist_id'" );

		if ( $playlist )
			return $playlist->name .' | '. get_bloginfo( 'name' );
		else
			return get_bloginfo( 'name' );

	}

	add_filter( 'wp_title', 'wolf_jplayer_playlist_wp_title' );
}


if ( ! function_exists( 'wolf_jplayer_get_wp_title' ) ) {
	/**
	 * Get WP title
	 */
	function wolf_jplayer_get_wp_title() {
		ob_start();
		wp_title();
		$wp_title = ob_get_contents();
		ob_end_clean();
		$wp_title = preg_replace( '/&#?[a-z0-9]{2,8};/i', '', $wp_title );
		$wp_title = preg_replace( '/\s+/', ' ', $wp_title );
		echo sanitize_text_field( $wp_title );

	}
}

if ( ! function_exists( 'wolf_jplayer_get_default_playlist_poster' ) ) {
	/**
	 * Get default poster
	 *
	 * @param int $playlist_id
	 */
	function wolf_jplayer_get_default_playlist_poster( $playlist_id ) {
		global $wpdb;
		$wolf_jplayer_playlists_table = $wpdb->prefix.'wolf_jplayer_playlists';
		$row = $wpdb->get_row( "SELECT poster FROM $wolf_jplayer_playlists_table WHERE id = '$playlist_id'" );
		return $row->poster ? esc_url( $row->poster ) : esc_url( WOLF_JPLAYER_PLUGIN_URL . '/assets/images/default_poster.png' );
	}
}

if ( ! function_exists( 'wolf_jplayer_meta' ) ) {
	function wolf_jplayer_meta() {
		global $options, $playlist_id;
		$playlist_url   = WOLF_JPLAYER_PLUGIN_URL . '/includes/jplayer-single.php?playlist_id=' . $playlist_id;
		$site_name      = get_bloginfo( 'name' );
		$player_options = get_option( 'wolf_jplayer_settings' );
		if ( isset( $player_options['social_meta'] ) ) {
			?>

			<!-- playlist facebook meta -->
			<meta property="og:site_name" content="<?php echo sanitize_text_field( $site_name ); ?>" />
			<meta property="og:title" content="<?php wolf_jplayer_get_wp_title(); ?>" />
			<meta property="og:url" content="<?php echo esc_url( $playlist_url ); ?>" />
			<?php if ( wolf_jplayer_get_default_playlist_poster( $playlist_id ) ) : ?>
				<meta property="og:image" content="<?php echo esc_url( wolf_jplayer_get_default_playlist_poster( $playlist_id ) ); ?>" />
			<?php endif; ?>

			<!-- playlist google plus meta -->
			<meta itemprop="name" content="<?php echo sanitize_text_field( $site_name ); ?>" />
			<?php if ( wolf_jplayer_get_default_playlist_poster( $playlist_id ) ) : ?>
				<meta itemprop="image" content="<?php echo esc_url( wolf_jplayer_get_default_playlist_poster( $playlist_id ) ); ?>" />
			<?php endif; ?>

		<?php
		}
	}
	add_action( 'wp_head', 'wolf_jplayer_meta' );
}

// Wordpress Header
get_header( 'jplayer' ); ?>
	
	<div id="wolf-jplayer-single-page">
		<?php
		if ( $playlist_id ) echo wolf_jplayer_show_playlist( $playlist_id, false, true );
		?>
	</div>

<?php get_footer( 'jplayer' ); ?>