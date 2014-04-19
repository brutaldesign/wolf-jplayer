<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Wolf_Jplayer_Show' ) ) {
	/**
	 * WolfJplayer output class
	 *
	 * @package WolfJplayer
	 * @since WolfJplayer 1.0.0
	 */
	class Wolf_Jplayer_Show {

		/**
		* @var string
		*/
		public $version = WOLF_JPLAYER_PLUGIN_VERSION;

		/**
		 * Wolf_jPlayer_Show Constructor.
		 */
		public function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'print_styles' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'register_script' ) );
			add_shortcode( 'wolf_jplayer_playlist', array( $this, 'jplayer_playlist_shortcode' ) );
			add_action( 'wp_head', array( $this, 'jplayer_custom_styles' ) );
		}

		/**
		 * Register CSS styles
		 */
		public function print_styles() {

			wp_register_style( 'jplayer-playlist', WOLF_JPLAYER_PLUGIN_URL . '/assets/css/min/jplayer.min.css', array(), $this->version, 'all' );
			wp_register_style( 'mCustomScrollbar', WOLF_JPLAYER_PLUGIN_URL . '/assets/css/min/mCustomScrollbar.min.css', array(), '2.8.3', 'all' );

			wp_enqueue_style( 'jplayer-playlist' );

			if ( wolf_get_jplayer_option( 'scrollbar' ) )
			wp_enqueue_style( 'mCustomScrollbar' );
		}

		/**
		 * Register JS scripts
		 */
		public function register_script() {

			if ( ! wp_script_is( 'jquery' ) )
				wp_enqueue_script( 'jquery' );

			wp_register_script( 'mCustomScrollbar', WOLF_JPLAYER_PLUGIN_URL . '/assets/js/min/jquery.mCustomScrollbar.concat.min.js', 'jquery', '2.8.3', false );

			wp_register_script( 'wolf-jplayer', WOLF_JPLAYER_PLUGIN_URL . '/assets/js/min/jquery.jplayer.concat.min.js', 'jquery', $this->version, false );
			wp_localize_script(
				'wolf-jplayer',
				'WolfjPlayerParams',
				array(
					'iTunesText' => __( 'Buy on iTunes', 'wolf' ), 
					'amazonText' => __( 'Buy on amazon', 'wolf' ),
					'buyNowText' => __( 'Buy now', 'wolf' ),
					'downloadText' => __( 'Right click and save link to download the mp3', 'wolf' ),
					'scrollBar' => wolf_get_jplayer_option( 'scrollbar' ),
				) 
			);
		}

		/**
		 * Enqueue inline CSS
		 */
		public function jplayer_custom_styles() {
			
			$bg_color_hex  = wolf_get_jplayer_option( 'bg_color' ) ? wolf_get_jplayer_option( 'bg_color' ) : '#353535';
			$bg_color_rgba = wolf_get_jplayer_option( 'bg_color' ) ? wolf_jplayer_hex_to_rgb( wolf_get_jplayer_option( 'bg_color' ) ) : wolf_jplayer_hex_to_rgb( '#353535' );
			$opacity       = wolf_get_jplayer_option( 'bg_opacity' ) ? intval( wolf_get_jplayer_option( 'bg_opacity' ) ) / 100 : 1;
			$font_color    = wolf_get_jplayer_option( 'font_color' ) ? wolf_get_jplayer_option( 'font_color' ) : '#ffffff';
			$max_song_count  = wolf_get_jplayer_option( 'song_count_before_scroll' );
			$playlist_height = ( wolf_get_jplayer_option( 'scrollbar' ) && $max_song_count ) ? $max_song_count * 37 : '';
			
			$inline_css = '<style type="text/css">';
			ob_start();
			?>
			
			.wolf-jplayer-playlist-container, .wolf-jplayer-playlist a{
				color: <?php echo sanitize_text_field( $font_color ); ?>!important;
			}
			.wolf-jplayer-playlist .jp-play-bar, .wolf-jplayer-playlist .jp-volume-bar-value{
				background-color: <?php echo sanitize_text_field( $font_color ); ?>;
			}
			.wolf-jplayer-loader-overlay{
				background-color: <?php echo sanitize_text_field( $bg_color_hex ); ?>;
			}
			.wolf-jplayer-playlist-container{
				background-color:rgba(<?php echo sanitize_text_field( $bg_color_rgba ); ?>, <?php echo sanitize_text_field( $opacity ); ?> );
			}

			.wolf-jplayer-playlist-container .mCSB_scrollTools .mCSB_dragger_bar{
				background-color: <?php echo sanitize_text_field( $font_color ); ?>;
			}

			<?php if ( $playlist_height ) : ?>
			.wolf-jplayer-playlist-container.wolf-jplayer-scrollbar .jp-playlist{
				max-height : <?php echo $playlist_height; ?>px;
			}
			<?php endif;
			$inline_css .= ob_get_clean();
			$inline_css .= '</style>';

			echo preg_replace( '/\s+/', ' ', $inline_css );
		}

		/**
		 * Output popup JS in wp_head
		 *
		 * @return string
		 */
		public function popup() {

			$popup = 'jQuery(".wolf-jp-popup").click(function() {
					Player = $(this).parent().prev();
					Player.jPlayer("stop");
			 		var url = jQuery(this).attr("href");
			 		var popupHeight = jQuery(this).parents(".wolf-jplayer-playlist-container").height();
					var popup = window.open(url,"null", "height=" + popupHeight + ",width=570, top=150, left=150");
					if (window.focus) {
						popup.focus();
					}
					return false; 
			});';

			return  $popup;
		}

		/**
		 * Force flash callback
		 *
		 * @return bool
		 */
		public function force_flash() {

			global $options;
			$settings = get_option( 'wolf_jplayer_settings' );
			$no_flash_option = isset( $settings['disable_flash'] );

			$user_agent = $_SERVER['HTTP_USER_AGENT'];
			$is_firefox_on_ios = preg_match( '/Macintosh/i', $user_agent ) && preg_match( '/Firefox/i', $user_agent );

			if ( $no_flash_option && $is_firefox_on_ios ) {
				//debug('disable flash' );
				return false;
			} else {
				//debug('enable flash' );
				return true;
			}
		
		}

		/**
		 * Check if a default playlist poster is set
		 *
		 * Otherwise, return the default image from the images folder : default_poster.png
		 *
		 * @param int $playlist_id
		 * @return string
		 */
		public function get_default_playlist_poster( $playlist_id ) {
			global $wpdb;
			$wolf_jplayer_playlists_table = $wpdb->prefix.'wolf_jplayer_playlists';
			$row = $wpdb->get_row( "SELECT poster FROM $wolf_jplayer_playlists_table WHERE id = '$playlist_id'" );

			return $row->poster ? esc_url( $row->poster ) : esc_url( WOLF_JPLAYER_PLUGIN_URL . '/assets/images/default_poster.png' );
		}

		/**
	              * Output inline jplayer javascript
	              *
	              * @param int $id
	              * @param int $playlist_id
	              * @param array $songs
	              * @param bool $popup
	              * @param bool $autoplay
	              * @return string
	              */
		public function head_script( $id, $playlist_id, $songs, $in_popup, $autoplay = false ) {
			$output   = '';
			$playlist = '';
			$artist   = '';
			$free     = null;
			$external = 0;

			if ( $songs ) {
				
				$ogg = '';
				
				foreach ( $songs as $song ) {

					$free = $song->free;

					if ( $song->poster ) {
						$poster = esc_url( $song->poster );
					} else {
						$poster = $this->get_default_playlist_poster( $playlist_id );
					}
	
					$playlist .= '{  title : "' . $song->name . '", mp3:"'. esc_url( $song->mp3 ) .'"';

					if ( $song->ogg )
						$playlist .= ', oga : "' . esc_url( $song->ogg ) . '" ';

					if ( $song->artist )
						$playlist .= ', artist : "' . $song->artist . '" ';


					if ( $free != 'on' ) {

					if ( $song->itunes )
						$playlist .= ', itunes : "' . esc_url( $song->itunes ) . '" ';

						if ( $song->amazon )
							$playlist .= ', amazon : "' . esc_url( $song->amazon ) . '" ';

						if ( $song->buy )
							$playlist .= ', buy : "' . esc_url( $song->buy ) . '" ';

					}

					else {
						$playlist .= ',download : "' . esc_url( $song->mp3 ) . '" '; // is free
					}

					$playlist .= ',poster : "' . $poster . '" ';


					$playlist .= ' },';
				}

				$playlist = substr( $playlist, 0, -1 );

				$output .= '<script type="text/javascript">//<![CDATA[';

				$output .= "\n";
				$output .= 'jQuery(document).ready(function($) {
						new jPlayerPlaylist( {
							jPlayer: "#jquery_jplayer_' . $id . '",
							cssSelectorAncestor: "#jp_container_' . $id . '" }, 
							['.$playlist.'], {
							swfPath: "' . WOLF_JPLAYER_PLUGIN_URL . '/assets/js/src",
							wmode: "window", ';

				if ( $this->force_flash() ) {
					// from previous version : works except for firefox on IOS
					$output .= 'supplied: "mp3"';
					$output .= ', solution:"flash, html"';
				} else {
					$output .= 'supplied: "oga, mp3"';		
					//$output .= ', solution:"html, flash"';
				}
				
				if (  $autoplay && $autoplay == 'on' ) {
					$output .= ', 
					playlistOptions: { autoPlay : true }';
				}

				$output .= '});'; // end playlist

				if ( ! $in_popup )
					$output .= $this->popup();

				$output .= '});'; // end document ready playlist

				$output .= '//]]></script>';
			}

			echo $output;
		}

		/**
	              * Output jplayer HTML
	              *
	              * @param int $playlist_id
	              * @param bool $in_popup
	              * @param bool $embed
	              * @return string
	              */
		public function jplayer_show_playlist( $playlist_id, $in_popup, $embed = false ) {

			global $wpdb, $options;

			if ( wolf_get_jplayer_option( 'scrollbar' ) )
				wp_enqueue_script( 'mCustomScrollbar' );

			wp_enqueue_script( 'wolf-jplayer' );

			$wolf_jplayer_playlists_table = $wpdb->prefix.'wolf_jplayer_playlists';
			$wolf_jplayer_table           = $wpdb->prefix.'wolf_jplayer';
			$playlist                     = $wpdb->get_row( "SELECT * FROM $wolf_jplayer_playlists_table WHERE id = '$playlist_id'" );
			$songs                        = $wpdb->get_results( "SELECT * FROM $wolf_jplayer_table WHERE playlist_id = '$playlist_id' ORDER BY position" );
			$autoplay                     = null;

			if ( $playlist)
				$share_title = $playlist->name.' | '.get_bloginfo( 'name' );
			else
				$share_title = get_bloginfo( 'name' );

			$id = $playlist_id.rand( 1,999 );

			if ( $playlist && $songs ) :

				$autoplay      = $playlist->autoplay;
				$max_song_count  = wolf_get_jplayer_option( 'song_count_before_scroll' );
				$count_songs   = ( wolf_get_jplayer_option( 'scrollbar' ) && $max_song_count ) ? $max_song_count : count( $songs );
				$player_height = 170 + 35 * $count_songs;
				$logo          = null;
				$html          = $this->head_script( $id, $playlist_id, $songs, $in_popup, $autoplay );
				
				if ( $playlist->logo ) {
					$logo = "background-image : url( '" . $playlist->logo . "' );";
				}

				$mobile_class    = wp_is_mobile() ?' wolf-jplayer-is-mobile' : '';
				$scrollbar_class = wolf_get_jplayer_option( 'scrollbar' ) ? '  wolf-jplayer-scrollbar' : '';
				
				$html .= '<!-- jPlayer -->
				<div class="wolf-jplayer-playlist-container' . $mobile_class . $scrollbar_class . '">
					<div class="wolf-jplayer-loader-overlay"><div class="wolf-jplayer-loader"></div></div>
					<div class="wolf-jplayer-playlist">
					<div class="wolf-jp-overlay">
						<div class="wolf-jp-share-container">
							<div class="wolf-jp-share">
							<div>
								<p><strong>Share</strong></p>
							</div>
							<div class="wolf-share-input">
								<label>url : </label>
								<div>
									<input onclick="this.focus();this.select()" type="text" value="' . esc_url( home_url( '/' ) ) . 'player/?playlist_id=' . $playlist_id . '">
								</div>
							</div>
							<div class="wolf-share-input">
								<label>embed : </label>
								<div>
								<input onclick="this.focus();this.select()" type="text" value="&lt;iframe width=&quot;100%&quot; height=&quot;' . $player_height . '&quot; scrolling=&quot;no&quot; frameborder=&quot;no&quot; src=&quot;'.  esc_url( home_url( '/' ) ) . 'player/?playlist_id=' . $playlist_id . '&amp;iframe=true&amp;wmode=transparent&quot;&gt;&lt;/iframe&gt;">
								</div>
							</div>
							<div class="clear"></div>
							<div class="wolf-jp-share-socials">
								<a class="wolf-share-jp-popup" href="http://www.facebook.com/sharer.php?u=' .  esc_url( home_url( '/' ) ) . 'player/?playlist_id=' . $playlist_id .'&t='.urlencode( $share_title ).'" title="'.__( 'Share on facebook', 'wolf' ).'" target="_blank">
								<span id="wolf-jplayer-facebook-button"></span>
								</a>
								<a class="wolf-share-jp-popup" href="http://twitter.com/home?status='. urlencode( $share_title.' - ' ) .  esc_url( home_url( '/' ) ) . 'player/?playlist_id=' . $playlist_id .'" title="'.__( 'Share on twitter', 'wolf' ).'" target="_blank">
								<span id="wolf-jplayer-twitter-button"></span>
								</a>
							</div>
							<span class="close-wolf-jp-share" title="'. __( 'close', 'wolf' ) .'">&times;</span>
						</div>
					</div>
				</div>
				<div id="jplayer_container_' . $id . '" class="jplayer_container">
				<div id="jquery_jplayer_' . $id . '" class="jp-jplayer"></div>
					<div id="jp_container_' . $id . '" class="jp-audio">
					<div class="jp-logo" style="' . $logo . '"></div><span title="'. __( 'share', 'wolf' ) .'" class="wolf-jp-share-icon"></span>';

			if ( ! $in_popup )
				$html .= '<a href="' . esc_url( home_url( '/' ) ) . '/player/?playlist_id=' . $playlist_id . '&amp;iframe=false" class="wolf-jp-popup" title="popup window"></a>';
				$html .= '<div class="jp-type-playlist">
					<div class="jp-gui jp-interface">
						<ul class="jp-controls">
							<li><a href="javascript:;" class="jp-previous" tabindex="1"></a></li>
							<li><a href="javascript:;" class="jp-play" tabindex="1"></a></li>
							<li><a href="javascript:;" class="jp-pause" tabindex="1"></a></li>
							<li><a href="javascript:;" class="jp-next" tabindex="1"></a></li>
							<li><a href="javascript:;" class="jp-stop" tabindex="1"></a></li>
							<li class="wolf-volume">
								<a href="javascript:;" class="jp-mute" tabindex="1" title="mute"></a>
								<a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute"></a>
							</li>
							<li><a href="javascript:;" class="jp-volume-max wolf-volume" tabindex="1" title="max volume"></a></li>
						</ul>
						<div class="jp-progress">
							<div class="jp-seek-bar">
								<div class="jp-play-bar"></div>
							</div>
						</div>
						<div class="jp-volume-bar wolf-volume">
							<div class="jp-volume-bar-value"></div>
						</div>
						<div class="jp-current-time"></div>
						<div class="jp-duration"></div>
						<ul class="jp-toggles">
							<li><a href="javascript:;" class="jp-shuffle" tabindex="1" title="shuffle"></a></li>
							<li><a href="javascript:;" class="jp-shuffle-off" tabindex="1" title="shuffle off"></a></li>
							<li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat"></a></li>
							<li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off"></a></li>
						</ul>
					</div>

					<div class="jp-playlist">
						<ul>
							<li></li>
						</ul>
					</div>

					<div class="jp-no-solution">
						<span>Update Required</span>
						To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
					</div>

					</div>
				</div>
			</div>
			</div>
			</div>
			<!-- End jPlayer -->';

			else :
				if ( is_user_logged_in() )
					$html = '<p style="text-shadow:none!important"><em>' . __( 'This playlist does not exist or is empty. Please double check the playlist ID and be sure you have uploaded songs.', 'wolf' ) . '</em></p>';
				else
					$html = '<p style="text-shadow:none!important"><em>' . __( 'This playlist does not exist or is empty.', 'wolf' ) . '</em></p>';
			endif;

				return $html;
		}

		/**
		 * Playlist Shortcode
		 * 
		 * @param array $atts
		 * @return string
		 */
		public function jplayer_playlist_shortcode( $atts ) {
				
			extract(
				shortcode_atts(
					array(
						'id' => '1',
					), $atts
				) 
			);
			
			return $this->jplayer_show_playlist( $id, false );
		}


	} //end class

	/**
	 * Init Wolf_Jplayer_Show class
	 */
	$GLOBALS['wolf_jplayer_show'] = new Wolf_Jplayer_Show;

	if ( ! function_exists( 'wolf_jplayer_show_playlist' ) ) {
		/**
		 *  jPlayer Show Function
		 *
		 * @param int $id
		 * @param bool $in_popup
		 * @param bool $echo
		 */
		function wolf_jplayer_show_playlist( $id = 1, $in_popup = false ) {
			global $wolf_jplayer_show;
			return $wolf_jplayer_show->jplayer_show_playlist( $id, $in_popup );
		}
	}
} // end class check