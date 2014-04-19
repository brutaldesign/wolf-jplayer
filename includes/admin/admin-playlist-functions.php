<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Output Playlist form
 *
 * @param int $id
 */
function wolf_playlist_form( $id = null ) {
	global $wpdb;
	$wolf_jplayer_playlists_table = $wpdb->prefix . 'wolf_jplayer_playlists';

	$name         = null;
	$logo         = null;
	$poster       = null;
	$submit_value = __( 'Create', 'wolf' );
	$autoplay     = null;

	if ( $id != null ) {
		$id           = intval( $id );
		$playlist     = $wpdb->get_row( "SELECT * FROM $wolf_jplayer_playlists_table WHERE id = '$id'" );
		$name         = sanitize_text_field( $playlist->name );
		$autoplay     = $playlist->autoplay;
		$logo         = esc_url( $playlist->logo );
		$poster       = esc_url( $playlist->poster );
		$submit_value = __( 'Save changes', 'wolf' );
	}
?>
	<?php if ( $id == null ) : ?>
		<h3><?php _e( 'Create a new playlist', 'wolf' ); ?></h3>
		<p><em><?php _e( 'Fields marked with * are required', 'wolf' ); ?></em></p>
	<?php else : ?>
		<h3><?php _e( 'Update your playlist name', 'wolf' ); ?></h3>
	<?php endif; ?>
		<form id="jp-form" action="<?php echo esc_url(admin_url( 'admin.php?page=wolf-jplayer-panel' )); ?>" method="post" enctype="multipart/form-data">
			
			<p>
				<label for="playlist_name"><?php _e( 'Playlist name', 'wolf' ); ?>*</label>
				<input type="text" name="playlist_name" value="<?php echo $name; ?>">
			</p>
			<p>
				<label for="logo"><?php _e( 'Logo', 'wolf' ); ?> (120px X 50px)</label>
				<input type="hidden" name="logo" value="<?php echo $logo; ?>">
				<img <?php if ( ! $logo ) echo 'style="display:none;"'; ?> class="wolf_jplayer_img_preview" src="<?php echo esc_url( $logo ); ?>" alt="logo">
				<a href="#" class="wolf_jplayer_upload_img_button button"><?php _e( 'Choose an image', 'wolf' ); ?></a>
				<a href="#" class="button wolf_jplayer_reset"><?php _e( 'Clear', 'wolf' ); ?></a>
			</p>
			<p>
				<label for="poster"><?php _e( 'Defaut Artwork', 'wolf' ); ?> (80px X 80px)</label>
				<input type="hidden" name="poster" value="<?php echo $poster; ?>">
				<img <?php if ( ! $poster ) echo 'style="display:none;"'; ?> class="wolf_jplayer_img_preview" src="<?php echo esc_url( $poster ); ?>" alt="poster">
				<a href="#" class="wolf_jplayer_upload_img_button button"><?php _e( 'Choose an image', 'wolf' ); ?></a>
				<a href="#" class="button wolf_jplayer_reset"><?php _e( 'Clear', 'wolf' ); ?></a>
			</p>
			<p>
				<label for="playlist_name"><?php _e( 'Autoplay (not recommended)', 'wolf' ); ?></label>
				<input type="checkbox" name="autoplay" <?php echo $autoplay ? 'checked="checked"' : '' ?>>
			</p>
				<input type="hidden" name="playlist_name_id" value="<?php echo $id; ?>">
			<p>
				<input onClick="javascript:Show( 'loader' );" type="submit" class="button-primary" name="manage_playlist" value="<?php echo $submit_value; ?>">
			</p>
			<?php wp_nonce_field( 'save_playlist', 'save_playlist_nonce' ); ?>
			<div class="clear"></div>
		</form>

		<div class="clear"></div>
	<?php
}

/**
 * Add or Edit playlist to the database
 *
 * @param int $id
 */
function wolf_manage_playlist( $id = null ) {
	
	global $wpdb;
	$wolf_jplayer_playlists_table = $wpdb->prefix.'wolf_jplayer_playlists';

	$playlist = null;
	$errors   = array();
	$error    = false;
	$autoplay = null;
	$logo     = esc_url( $_POST['logo'] );
	$poster   = esc_url( $_POST['poster'] );
	
	if ( $id ) {
		$id = intval($id);
		$playlist = $wpdb->get_row( "SELECT * FROM $wolf_jplayer_playlists_table WHERE id = '$id'" );
	}

	if ( isset( $_POST['autoplay'] ) )
		$autoplay = sanitize_title( $_POST['autoplay'] );

	if ( empty( $_POST['playlist_name'] ) ) {

		$errors[] = __( 'The playlist name can not be empty.', 'wolf' );
		$error = true;

	} else {

		$name = sanitize_text_field( $_POST['playlist_name'] );
	}

	
	if ( $error && $errors != array() ) {

		foreach ( $errors as $e) {
			wolf_jpayer_admin_notices( $e, 'error' );
		}

		/* If no errors
		----------------------------------------------*/
	} else {

		if ( $errors == array() ) {

			if ( ! $id ) {
				$data = array(
					'name' => $name,
					'logo' => $logo,
					'poster' => $poster,
					'autoplay' => $autoplay
				);
				$format = array( '%s' );
				$wpdb->insert( $wolf_jplayer_playlists_table, $data, $format );
				$confirm = __( 'Your playlist has been created succesfully.', 'wolf' );
			}

			elseif ( $id ) {
				
				$data = array(
					'name' => $name,
					'logo' => $logo,
					'poster' => $poster,
					'autoplay' => $autoplay
				);
				$format     = array( '%s' );
				$conditions = array( 'id' => $id );
				$wpdb->update( $wolf_jplayer_playlists_table, $data, $conditions, $format, array( '%d' ) ); 
				$confirm    = __( 'Your playlist has been updated.', 'wolf' );
			}

			wolf_jpayer_admin_notices( $confirm, 'updated' );
		
		} else {

			wolf_jpayer_admin_notices( __( 'Unknow error occured, please try again later', 'wolf' ), 'error' );

		}
	}

}

/**
 * Delete a Playlist from the database
 *
 * @param int $id
 */
function wolf_delete_playlist( $id = null ) {
	
	$songs = array();

	if ( $id ) {
		$id = intval( $id );
		
		global $wpdb;
		$wolf_jplayer_playlists_table = $wpdb->prefix.'wolf_jplayer_playlists';
		$wolf_jplayer_table           = $wpdb->prefix.'wolf_jplayer';
		$playlist                     = $wpdb->get_row( "SELECT * FROM $wolf_jplayer_playlists_table WHERE id = $id" );
		$songs                        = $wpdb->get_results( "SELECT * FROM $wolf_jplayer_table WHERE playlist_id = $id" );

		if ( $playlist ) {

			$wpdb->query( "DELETE FROM $wolf_jplayer_table WHERE playlist_id = $id" );
			$wpdb->query( "DELETE FROM $wolf_jplayer_playlists_table WHERE id = $id" );

			wolf_jpayer_admin_notices( __( 'Playlist deleted', 'wolf' ), 'updated' );

		} else {

			wolf_jpayer_admin_notices( __('The playlist you are trying to delete does not exist.', 'wolf' ), 'error' );

		}
	} // end if id
}