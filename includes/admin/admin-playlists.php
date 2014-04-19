<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( isset( $_POST['manage_playlist'] ) && wp_verify_nonce( $_POST['save_playlist_nonce'], 'save_playlist' ) ) {

	if ( empty( $_POST['playlist_name_id'] ) )
		
		wolf_manage_playlist();
	else
		wolf_manage_playlist( $_POST['playlist_name_id'] );		
}

if ( isset( $_GET['delete_playlist'] ) ) {

	wolf_delete_playlist( $_GET['delete_playlist'] );
	unset( $_GET );

}

if ( ! isset( $_GET['playlist_name_id'] ) )

	wolf_playlist_form();

else
	wolf_playlist_form( $_GET['playlist_name_id'] );


$wolf_jplayer_playlists_table = $wpdb->prefix.'wolf_jplayer_playlists';
$wolf_jplayer_table = $wpdb->prefix.'wolf_jplayer';
$playlists = $wpdb->get_results( "SELECT * FROM $wolf_jplayer_playlists_table" );
if ( $playlists ) :
?>
	<h2>Playlists</h2>
	<table class="wpw-custom-table">
		<thead>
			<th>Shortcode</th>
			<th><?php _e( 'Songs count', 'wolf' ); ?></th>
			<th><?php _e( 'Name', 'wolf' ); ?></th>
			<th><?php _e( 'Logo', 'wolf' ); ?></th>
			<th><?php _e( 'Default Artwork', 'wolf' ); ?></th>
			<th><?php _e( 'Autoplay', 'wolf' ); ?></th>
			<th><?php _e( 'Actions', 'wolf' ); ?></th>
		</thead>
		<tbody>
			<?php 
			foreach ($playlists as $p) : 
				$songs = $wpdb->get_row( "SELECT COUNT(*) AS count FROM $wolf_jplayer_table WHERE playlist_id = '$p->id'" );
				if ( $songs )
					$count = $songs->count;
				else
					$count = 0;
				
			
				$edit_link = esc_url( admin_url( 'admin.php?page=wolf-jplayer-panel&amp;playlist_name_id='.$p->id ) );
			?>
			<tr>
				<td><code>[wolf_jplayer_playlist id="<?php echo $p->id; ?>"]</code></td>
				<td><?php echo $count; ?></td>
				<td><a class="wolf-jp-hastip" title="<?php _e( 'Edit playlist name', 'wolf' ); ?>" href="<?php echo $edit_link; ?>"><?php echo $p->name; ?></a></td>
				<td><?php echo ( $p->logo ) ? '<img style="vertical-align:middle; margin:5px 0" width="50" src="' . $p->logo . '">' : '<a class="wolf-jp-hastip" title="' . __( 'Upload a custom logo for your playlist', 'wolf' ) . '" href="' . $edit_link . '">' . __( 'Add a logo', 'wolf' ) . '</a>'; ?></td>

				<td><?php echo ( $p->poster ) ? '<img style="vertical-align:middle; margin:5px 0" width="50" src="' . $p->poster . '">' : '<a class="wolf-jp-hastip" title="' . __( 'Upload a default artwork for your playlist songs', 'wolf' ) . '" href="' . $edit_link . '">' . __( 'Add a default artwork', 'wolf' ) . '</a>'; ?></td>
				
				<td><?php echo ( $p->autoplay ) ? __( 'Yes', 'wolf' ) : __( 'No', 'wolf' ); ?></td>

				<td>
					<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=wolf-jplayer-panel&amp;playlist_id='.$p->id ) ); ?>">
						<?php _e('Manage songs', 'wolf' ); ?></a> 
					<a class="wolf-jp-hastip" title="<?php _e( 'Edit playlist', 'wolf' ); ?>" href="<?php echo esc_url( $edit_link ); ?>"><img style="vertical-align:middle; margin-left:10px;" src="<?php echo WOLF_JPLAYER_PLUGIN_URL . '/assets/images/admin/edit.png'; ?>" alt="edit"></a> 
					
					<a class="wolf-jp-hastip" title="<?php _e( 'Delete playlist', 'wolf' ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=wolf-jplayer-panel&amp;delete_playlist=' . $p->id ) ); ?>" onclick="if ( window.confirm( '<?php _e( 'Are you sure to want to delete this playlist and all songs in it ?', 'wolf' ); ?>' ) ) { location.href='default.htm';return true; } else { return false; }"><img style="vertical-align:middle" src="<?php echo esc_url( WOLF_JPLAYER_PLUGIN_URL . '/assets/images/admin/delete.png' ); ?>" alt="delete"></a>

				</td>
			</tr>
		             <?php endforeach; ?>
		</tbody>

	</table>
<?php else: ?>
	<p><?php _e( 'No playlist yet, create you first playlist using the form above!', 'wolf' ); ?></p>
<?php endif; // end if playlists ?>