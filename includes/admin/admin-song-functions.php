<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Output Song form
 *
 * @param int $playlist_id
 * @param int $sing_id
 */
function wolf_song_form( $playlist_id = null , $song_id = null ) {
	
	global $wpdb;
	$wolf_jplayer_playlists_table = $wpdb->prefix . 'wolf_jplayer_playlists';
	$wolf_jplayer_table           = $wpdb->prefix . 'wolf_jplayer';
	$no_flash                     = wolf_get_jplayer_option( 'disable_flash' );
	$playlist                     = $wpdb->get_row( "SELECT * FROM $wolf_jplayer_playlists_table WHERE id = '$playlist_id'" );
	$song_name                    = null;
	$artist                       = null;
	$poster                       = null;
	$itunes                       = null;
	$amazon                       = null;
	$buy                          = null;
	$free                         = null;
	$submit_value                 = __( 'Submit', 'wolf' );
	$ogg                          = null;
	$mp3                          = null;

	if ( $song_id ) {
		$song_id      = intval($song_id);
		$song         = $wpdb->get_row( "SELECT * FROM $wolf_jplayer_table WHERE id = '$song_id' AND playlist_id = '$playlist_id'" );
		$song_name    = $song->name;
		$ogg          = $song->ogg;
		$mp3          = $song->mp3;
		$artist       = $song->artist;
		$poster       = $song->poster;
		$itunes       = $song->itunes;
		$amazon       = $song->amazon;
		$buy          = $song->buy;
		$free         = $song->free;
		$submit_value = __( 'Save changes', 'wolf' );
		// debug($song);
	}
?>
<?php if ( $song_id == null ) : ?>
	<h3><?php printf( __( 'Upload a new song in "%s" playlist', 'wolf' ), $playlist->name ); ?></h3>
	<p><em><?php _e( 'Fields marked with * are required', 'wolf' ); ?></em></p>
<?php else: ?>
	<h3><?php _e( 'Update your song', 'wolf' ); ?></h3>
<?php endif; ?>
	<form style="margin:0 20px 20px 0" id="jp-form" action="<?php echo esc_url( admin_url( 'admin.php?page=wolf-jplayer-panel&amp;playlist_id=' . $playlist_id ) ); ?>" method="post">
			<p>
				<label for="song_name"><?php _e( 'Song title', 'wolf' ); ?> *</label>
				<input type="text" name="song_name" value="<?php echo stripslashes( $song_name ); ?>">
			</p>
			<p>
				<label for="artist"><?php _e( 'Artist', 'wolf' ); ?> : </label>
				<input type="text" name="artist" value="<?php echo $artist; ?>">
			</p>
			<p>
				<label for="mp3"><?php _e( 'Mp3 URL', 'wolf' ); ?> *<br>
				<em><?php _e( '(Enter an URL or choose a song from the media library)', 'wolf' ); ?> </em>
				</label>
				<input type="text" name="mp3" value="<?php echo $mp3; ?>">
				<a href="#" class="wolf_jplayer_upload_button button"><?php _e( 'Choose an mp3 file', 'wolf' ); ?></a>
			</p>
			<?php if ( $no_flash ) : ?>
			<p>
				<label for="ogg"><?php _e( 'Ogg URL', 'wolf' ); ?> *<br>
				<em><?php _e( '(Optional ogg file for firefox and opera)', 'wolf' ); ?> </em>
				</label>
				<input type="text" name="ogg" value="<?php echo $ogg; ?>">
				<a href="#" class="wolf_jplayer_upload_button button"><?php _e( 'Choose an ogg file', 'wolf' ); ?></a>
			</p>
			<?php endif; ?>
			<p>
				<label for="poster"><?php _e( 'Artwork (jpg or png)', 'wolf' ); ?> (80px X 80px)</label>
				<input type="hidden" name="poster" value="<?php echo $poster; ?>">
				<img <?php if ( ! $poster ) echo 'style="display:none;"'; ?> class="wolf_jplayer_img_preview" src="<?php echo esc_url( $poster ); ?>" alt="poster">
				<a href="#" class="wolf_jplayer_upload_img_button button"><?php _e( 'Choose an image', 'wolf' ); ?></a>
				<a href="#" class="button wolf_jplayer_reset"><?php _e( 'Clear', 'wolf' ); ?></a>
			</p>
			<p>
				<label for="itunes"><?php _e( 'Itunes URL', 'wolf' ); ?></label>
				<input type="text" name="itunes" value="<?php echo $itunes; ?>">
			</p>
			<p>
				<label for="amazon"><?php _e( 'Amazon URL', 'wolf' ); ?></label>
				<input type="text" name="amazon" value="<?php echo $amazon; ?>">
			</p>
			<p>
				<label for="buy"><?php _e( 'Other "buy" URL', 'wolf' ); ?></label>
				<input type="text" name="buy" value="<?php echo $buy; ?>">
			</p>
			<p>
				<label for="free"><?php _e( 'Free download', 'wolf' ); ?></label>
				<input type="checkbox" name="free" <?php echo ( $free ) ? 'checked="checked"' : '' ?>>
				<em><?php _e( 'Will overwrite the "buy" URLs above', 'wolf' ) ?></em>
			</p>
			<input type="hidden" name="playlist_id" value="<?php echo $playlist_id; ?>">
			<input type="hidden" name="song_id" value="<?php echo $song_id; ?>">
			<p>
				<input type="submit" class="button-primary" name="manage_song" value="<?php echo $submit_value; ?>">
			</p>
			<?php wp_nonce_field( 'save_song', 'save_song_nonce' ); ?>
			<div class="clear"></div>
	</form>
	<div class="clear"></div>

<?php if ( $song_id ) : ?>
	<p><a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=wolf-jplayer-panel&amp;playlist_id=' . $playlist_id ) ); ?>"><?php _e( 'back to the "add a song" form', 'wolf' ); ?></a></p>
<?php else: ?>
	<p><a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=wolf-jplayer-panel' ) ); ?>"><?php _e( 'back to the playlist manager', 'wolf' ); ?></a></p>
<?php endif; ?>
<script type="text/javascript">
	function Show( section ) {
		document.getElementById( section ).style.display = 'block';
	}
</script>
<?php
}

/**
 * Delete Songs
 */
function wolf_delete_selected_songs() {
	
	if ( isset( $_POST['box'] ) ) {
		$boxes = $_POST['box'];
		global $wpdb;
		$wolf_jplayer_table = $wpdb->prefix . 'wolf_jplayer';
		foreach( $boxes as $id ) {
			$s = $wpdb->get_row( "SELECT * FROM $wolf_jplayer_table WHERE id = $id" );
	
			if ( $s ) {
				$wpdb->query( "DELETE FROM $wolf_jplayer_table WHERE id = $id" );
			}
		}

		wolf_jpayer_admin_notices( __( 'Songs deleted', 'wolf' ), 'updated' );

	} else {
		wolf_jpayer_admin_notices( __( 'No songs selected', 'wolf' ), 'error' );
	}
}

/**
 * Delete a song
 *
 * @param int $id
 */
function wolf_delete_song( $id = null ) {
	
	if ( $id ) {
		$id = intval( $id );
		global $wpdb;
		$wolf_jplayer_table = $wpdb->prefix . 'wolf_jplayer';

		$s = $wpdb->get_row( "SELECT * FROM $wolf_jplayer_table WHERE id = $id" );

		$wpdb->query( "DELETE FROM $wolf_jplayer_table WHERE id = $id" );
		wolf_jpayer_admin_notices( __( 'Song deleted', 'wolf' ), 'updated' );
	}
}

/**
 * Output error
 *
 * @param int $song_id
 */
function wolf_error( $song_id = null ) {
	
	$error = false;
	$errors = array();

	/*  Title empty */
	if ( empty( $_POST['song_name'] ) ) {
		$errors[] =  __( 'Song title can not be empty.', 'wolf' );
		$error = true;
	}

	/*  Mp3 URL empty */
	if ( empty( $_POST['mp3'] ) ) {

		$errors[] = __( 'Please fill an mp3 URL.', 'wolf' );
		$error = true;

	}

	if ( $error && $errors != array() ) {

		foreach( $errors as $e ) {
			wolf_jpayer_admin_notices( $e, 'error' );
		}
	}
	return $error;
}

/**
 * Manage song
 *
 * @param int $playlist_id
 * @param int $sing_id
 */
function wolf_manage_song( $playlist_id = 0, $song_id = null  ) {
	global $wpdb;
	$wolf_jplayer_table = $wpdb->prefix.'wolf_jplayer';


	/* If no errors
	----------------------------------------------*/
	if ( ! wolf_error( $song_id ) ) {

		/* All good, proceed  */
		$name    = sanitize_text_field( $_POST['song_name'] );
		$artist  = sanitize_text_field( $_POST['artist'] );
		$itunes  = esc_url( $_POST['itunes'] );
		$amazon  = esc_url( $_POST['amazon'] );
		$buy     = esc_url( $_POST['buy'] );
		$free    = ! empty( $_POST['free'] ) ? sanitize_title( $_POST['free'] ) : null;
		$mp3_url = esc_url( $_POST['mp3'] );
		$ogg_url = isset( $_POST['ogg'] ) ? esc_url( $_POST['ogg'] ) : null;
		$poster  = esc_url( $_POST['poster'] );
			
		if ( ! $song_id ) {
			/* Insertion */
			$data = array(
				'name' => $name,
				'mp3' => $mp3_url, 
				'ogg' => $ogg_url,
				'poster' => $poster,
				'artist' => $artist,
				'itunes' => $itunes,
				'amazon' => $amazon,
				'buy' => $buy,
				'free' => $free,
				'position' => 0,
				'playlist_id' => $playlist_id
			);
			$format = array( '%s', '%s', '%s','%s','%s','%s', '%s', '%s', '%s', '%d', '%d' );
			
			if ( $wpdb->insert( $wolf_jplayer_table, $data, $format ) ) {
				$notice_type = 'updated';
				$confirm = __( 'Your song has been added to your playlist.', 'wolf' );
			} else {
				$notice_type = 'error';
				$confirm = __( 'Your song could not be added to your database.', 'wolf' );
			}

			wolf_jpayer_admin_notices( $confirm, $notice_type );
			

		} else { // if update
			
			/* Update */
			$data = array(
				'name' => $name,
				'ogg' => $ogg_url,
				'mp3' => $mp3_url,
				'poster' => $poster,
				'artist' => $artist,
				'itunes' => $itunes,
				'amazon' => $amazon,
				'buy' => $buy,
				'free' => $free,
			);
			$format = array( '%s' );
			$conditions = array( 'id' => $song_id );
			
			$wpdb->update( $wolf_jplayer_table, $data, $conditions, $format, array( '%d' ) );
			$notice_type = 'updated';
			$confirm = __( 'Your song has been updated.', 'wolf' );

			wolf_jpayer_admin_notices( $confirm, $notice_type );

		} // end if song id

	} // end if error
}