<?php
/**
 * jPlayer Widget
 *
 * Displays jPlayer widget
 *
 * @author WpWolf
 * @category Widgets
 * @package WolfJplayer/Widgets
 * @since 1.0.0
 * @extends WP_Widget
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WJ_Playlist_Widget extends WP_Widget {

	/**
	 * constructor
	 *
	 */
	function WJ_Playlist_Widget() {

		// Widget settings
		$ops = array( 'classname' => 'wolf_jplayer_widget', 'description' => __( 'Display a playlist', 'wolf' ) );

		// Create the widget
		$this->WP_Widget( 'wolf_jplayer_widget', 'jPlayer', $ops );
		
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @param array $args
	 * @param array $instance
	 */
	function widget( $args, $instance ) {
		
		extract( $args );
		
		$title = apply_filters( 'widget_title', $instance['title'] );
		$desc  = '<p>' . wp_kses_post( $instance['desc'] ) . '</p>';
		echo wp_kses_post( $before_widget );
		
		if ( ! empty( $title ) ) echo wp_kses_post( $before_title ) . sanitize_text_field( $title ) . wp_kses_post( $after_title );
		
		if ( $instance['desc'] ) echo wp_kses_post( $desc );
		
		echo wolf_jplayer_show_playlist( $instance['playlist_id'], false, true );
		echo wp_kses_post( $after_widget );
	
	}

	/**
	 * update function.
	 *
	 * @see WP_Widget->update
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	function update( $new_instance, $old_instance ) {
		
		$instance                = $old_instance;
		$instance['title']       = $new_instance['title'];
		$instance['desc']        = $new_instance['desc'];
		$instance['playlist_id'] = $new_instance['playlist_id'];

		return $instance;
	}

	/**
	 * form function.
	 *
	 * @see WP_Widget->form
	 * @param array $instance
	 */
	function form( $instance ) {

		global $wpdb;
		$wolf_jplayer_playlists_table = $wpdb->prefix . 'wolf_jplayer_playlists';
		$playlists = $wpdb->get_results( "SELECT * FROM $wolf_jplayer_playlists_table" );
		$default_playlist_id = 0;
		
		if ( $playlists )
			$default_playlist_id = $playlists[0]->id;

		// Set up some default widget settings
		$defaults = array(
			'title' => '', 
			'playlist_id' => $default_playlist_id, 
			'desc' => '', 
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		?>
		<?php if ( $playlists ) : ?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'wolf' ); ?>:</label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo sanitize_text_field( $this->get_field_name( 'title' ) ); ?>" value="<?php echo sanitize_text_field( $instance['title'] ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'desc' ) ); ?>"><?php _e( 'Optional Text', 'wolf' ); ?>:</label>
			<textarea class="widefat"  id="<?php echo esc_attr( $this->get_field_id( 'desc' ) ); ?>" name="<?php echo sanitize_text_field( $this->get_field_name( 'desc' ) ); ?>" ><?php echo wp_kses_post( $instance['desc'] ); ?></textarea>
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'playlist_id' ) ); ?>"><?php _e( 'Playlist', 'wolf' ); ?>:</label>
			<select name="<?php echo sanitize_text_field( $this->get_field_name( 'playlist_id' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'playlist_id' ) ); ?>">
				<?php foreach ( $playlists as $p ) : ?>
					<option value="<?php echo absint( $p->id ); ?>" <?php if ( $instance['playlist_id'] == $p->id ) echo wp_kses_post( 'selected="selected"' ); ?>><?php echo sanitize_text_field( $p->name ); ?></option>
				<?php endforeach; ?>
			</select>
			
		</p>
		<?php else : ?>
			<p><?php _e( 'No playlist yet.', 'wolf' ); ?></p>
		<?php endif; ?>
		<?php
	}

}