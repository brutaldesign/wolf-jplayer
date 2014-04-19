<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// debug( $_POST);
$playlist_id = intval( $_GET['playlist_id'] );
$no_flash = wolf_get_jplayer_option( 'disable_flash' );

if ( isset( $_POST['manage_song'] ) && wp_verify_nonce( $_POST['save_song_nonce'], 'save_song' ) ) {
	
	if ( empty( $_POST['song_id'] ) )
		wolf_manage_song( $playlist_id );
	else
		wolf_manage_song( $playlist_id, $_POST['song_id'] );

}

if ( isset( $_POST['wolf-songs-action'] ) ) {

	wolf_delete_selected_songs();
}

if ( isset( $_GET['delete_song'] ) ) {

	wolf_delete_song( $_GET['delete_song'] );
	unset( $_GET );

}

if ( ! isset( $_GET['song_id'] ) )

	wolf_song_form( $playlist_id );

else

	wolf_song_form( $playlist_id, $_GET['song_id'] );



$wolf_jplayer_table = $wpdb->prefix.'wolf_jplayer';
$songs = $wpdb->get_results( "SELECT * FROM $wolf_jplayer_table WHERE playlist_id = '$playlist_id' ORDER BY position" );
if ( $songs ):
	//debug( $songs);
// Re-order songs by drag & drop (jquery-ui sortable)
if ( isset( $_POST['sortable'] ) ) {
	$sortlist = $_POST['sortable'];
	/*
	* $k = position
	* $v = id
	*/
	foreach ( $sortlist as $k => $v) {
		//echo $k.' = '. $v;
		$wpdb->query( "UPDATE $wolf_jplayer_table SET position=$k WHERE id = $v AND playlist_id = $playlist_id" );

	}
}
?>
<h2><?php _e( 'Songs', 'wolf' ); ?></h2>
<form action="<?php echo esc_url( admin_url( 'admin.php?page=wolf-jplayer-panel&playlist_id=' . $playlist_id ) ); ?>" method="post" id="wolf-selected-form">
<table class="wpw-custom-table">
	<thead>
		<th><input type="checkbox" id="wolf-check-all-songs"></th>
		<th>#ID</th>
		<th><?php _e( 'Title', 'wolf' ); ?></th>
		<th><?php _e( 'Artist', 'wolf' ); ?></th>
		<th><?php _e( 'Song', 'wolf' ); ?></th>
		<th><?php _e( 'Artwork', 'wolf' ); ?></th>
		<th><?php _e( 'Free Download', 'wolf' ); ?></th>
		<th><?php _e( 'Buy URL', 'wolf' ); ?></th>
		<th><?php _e( 'Actions', 'wolf' ); ?></th>
	</thead>
	<tbody id="sortable">
		<?php 
		foreach ( $songs as $s ): 
			//debug( $s);
			$edit_link = esc_url( admin_url( 'admin.php?page=wolf-jplayer-panel&amp;playlist_id=' . $playlist_id . '&amp;song_id=' . $s->id ) );
			$delete_link = esc_url( admin_url( 'admin.php?page=wolf-jplayer-panel&amp;playlist_id=' . $playlist_id . '&amp;delete_song=' . $s->id ) );
		?>
		<tr class="state-default" id="sortable_<?php echo $s->id; ?>">
			<td><input name="box[]" type="checkbox" value="<?php echo $s->id; ?>"></td>
			<td><?php echo $s->id; ?></td>
			<td><a class="wolf-jp-hastip" title="<?php _e( 'Edit the title', 'wolf' ); ?>" href="<?php echo $edit_link; ?>"><?php echo stripslashes( $s->name); ?></a></td>
			<td><?php echo ( $s->artist ) ? $s->artist : 'No artist'; ?></td>
			<td>
				<?php echo $s->mp3; ?>
				<?php if ( $no_flash ) : ?>
				<?php echo ( $s->ogg ) ? '<br>' . $s->ogg : ''; ?>
				<?php endif; ?>
			</td>
			<td><?php echo ( $s->poster ) ? '<img style="vertical-align:middle; margin:5px 0" width="50" src="' . $s->poster . '">' : '<a class="wolf-jp-hastip" title="' . __( 'Upload an artwork for your song', 'wolf' ) . '" href="' . $edit_link . '">' . __( 'Add an artwork', 'wolf' ) . '</a>'; ?></td>
			
			<td><?php echo ( $s->free ) ? __( 'Yes', 'wolf' ) : __( 'No', 'wolf' ); ?></td>

			<td><?php echo ( $s->buy) ? $s->buy : '<a class="wolf-jp-hastip" title="' . __( 'Add links where visitors can purchase your song', 'wolf' ) . '" href="' . $edit_link . '">' . __( 'Add buy links', 'wolf' ) . '</a>';  ?></td>

			<!-- Actions -->
			<td>
				<a href="<?php echo $edit_link; ?>"><img title="<?php _e( 'Edit', 'wolf' ); ?>" class="wolf-jp-hastip" src="<?php echo WOLF_JPLAYER_PLUGIN_URL . '/assets/images/admin/edit.png'; ?>" alt="edit"></a>
				<a onclick="if (window.confirm( '<?php _e( 'Are you sure to want to delete this  song ?', 'wolf' ); ?>' ) ) {location.href='default.htm';return true;} else {return false;}" href="<?php echo $delete_link; ?>"><img title="<?php _e( 'Delete', 'wolf' ); ?>" class="wolf-jp-hastip" src="<?php echo WOLF_JPLAYER_PLUGIN_URL . '/assets/images/admin/delete.png'; ?>" alt="delete"></a>
				<img style="cursor: move" src="<?php echo WOLF_JPLAYER_PLUGIN_URL . '/assets/images/admin/move.png'; ?>" alt="move" title="move">
			</td>
		</tr>
	             <?php endforeach; ?>
	</tbody>
</table>
<select style="position:relative; top:-20px" name="wolf-songs-action" id="wolf-songs-action">
	<option value=""><?php _e( 'Action', 'wolf' ); ?></option>
	<option value="1"><?php _e( 'Delete selected songs', 'wolf' ); ?></option>
</select>
</form>
<?php else: ?>
<hr>
	<h4><?php _e( 'No songs uploaded in this  playlist yet.', 'wolf' ); ?></h4>

<?php endif; // end if songs ?>

<hr>
<h4><?php _e( 'Your server infos', 'wolf' ); ?>:</h4>

<p>Post max size: <?php echo ini_get( 'post_max_size' ); ?><br>
Upload max filesize: <?php echo ini_get( 'upload_max_filesize' ); ?></p>
<em><?php printf( __( '<a href="%s" target="_blank">How to Increase your Upload Size Limit</a>', 'wolf' ), 'http://help.wpwolf.com/2013/11/how-to-increase-your-upload-size-limit/' ); ?></em>
<?php if ( ini_get( 'upload_max_filesize' ) < 20 ) : ?>
<p><?php _e( 'Your upload size limit seems a but low. Here is a tip to increase your upload limit', 'wolf' ); ?></p>
<a target="_blank" href="http://help.wpwolf.com/2013/11/how-to-increase-your-upload-size-limit/">http://help.wpwolf.com/2013/11/how-to-increase-your-upload-size-limit/</a>
<?php endif; ?>

<script type="text/javascript">
jQuery( function( $ ) {

	$( '#wolf-check-all-songs' ).click( function () {

		var boxes = $( this ).parents( 'table:eq(0)' ).find( ':checkbox' );

		if ( $( this ).is(':checked') ) {
			
			boxes.prop( 'checked', true );
		} else {

			boxes.prop( 'checked', false );
		}
		
	} );

	var select = $( '#wolf-songs-action' );
	select.on( 'change', function() {
		var val = $( this ).val();

		if ( val == '1' ) {

			if ( confirm( "<?php _e( 'Are you sure to want to delete these songs?', 'wolf' ); ?>" ) ) {
				$( "#wolf-selected-form").submit();
			}
				
			
		}

	} );

	var fixHelper = function( e, ui ) {
		ui.children().each( function() {
			$( this ).width( $( this ).width() );
		} );
		return ui;
	};
	$( '#sortable').sortable( {
		helper: fixHelper,
		placeholder: 'state-highlight',
		opacity : 0.6,
		accept : 'state-default',
		update: function() {             
			serial = $( '#sortable').sortable('serialize');
			$.ajax( {
				url: '',
				type: 'post',
				data: serial,
				complete: function(data) { 
					//console.log( data ); 
				}
			} );
		}
	} );
	
	$(  '#sortable' ).disableSelection();

} );
</script>