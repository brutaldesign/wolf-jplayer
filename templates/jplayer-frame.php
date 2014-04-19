<?php 
include_once( WOLF_JPLAYER_PLUGIN_DIR . '/includes/wp.php' );

$id = null;

if ( isset( $_GET['playlist_id'] ) ) {

	$id = intval( $_GET['playlist_id'] );
}

global $wpdb, $options;
$wolf_jplayer_playlists_table = $wpdb->prefix.'wolf_jplayer_playlists';
$playlist = $wpdb->get_row( "SELECT * FROM $wolf_jplayer_playlists_table WHERE id = '$id'" );

$embed    = false;
$in_popup = true;


if ( $playlist )
	$page_title = $playlist->name.' | '.get_bloginfo( 'name' );
else
	$page_title = get_bloginfo( 'name' );

if ( isset( $_GET['iframe'] ) && $_GET['iframe'] == 'true' ) {
	$embed    = true;
	$in_popup = false;
}

$bg_color_hex  = wolf_get_jplayer_option( 'bg_color' ) ? wolf_get_jplayer_option( 'bg_color' ) : '#353535';
$bg_color_rgba = wolf_get_jplayer_option( 'bg_color' ) ? wolf_jplayer_hex_to_rgb( wolf_get_jplayer_option( 'bg_color' ) ) : wolf_jplayer_hex_to_rgb( '#353535' );
$opacity       = wolf_get_jplayer_option( 'bg_opacity' ) ? intval( wolf_get_jplayer_option( 'bg_opacity' ) ) / 100 : 1;
$font_color    = wolf_get_jplayer_option( 'font_color' ) ? wolf_get_jplayer_option( 'font_color' ) : '#ffffff';
$max_song_count  = wolf_get_jplayer_option( 'song_count_before_scroll' );
$playlist_height = ( wolf_get_jplayer_option( 'scrollbar' ) && $max_song_count ) ? $max_song_count * 37 : '';
?>
<!DOCTYPE html> 
<html <?php language_attributes(); ?>>
<head>
	<title><?php echo sanitize_text_field( $page_title ); ?></title>
	<link rel="stylesheet" href="<?php echo esc_url( WOLF_JPLAYER_PLUGIN_URL . '/assets/css/min/normalize.min.css' ); ?>">
	<link rel="stylesheet" href="<?php echo esc_url( WOLF_JPLAYER_PLUGIN_URL . '/assets/css/min/jplayer.min.css' ); ?>">
	<style type="text/css">
		<?php if ( $embed ): ?>
			body {background:none}
		<?php endif; ?>
		<?php if ( $in_popup ) : ?>
		html { background: <?php echo sanitize_text_field( $bg_color_hex ); ?>;}
		body{
			background: <?php echo sanitize_text_field( $bg_color_hex ); ?>;
			height:auto!important;
		      	overflow-x:hidden!important;overflow-y:hidden!important; 
		}
		.jp-repeat, .jp-repeat-off{
			right:34px!important;
		}

		.jp-shuffle, .jp-shuffle-off{
			right:10px!important;

		}
		a.wolf-jp-popup { display:none!important }
		<?php endif; ?>

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
		<?php endif; ?>
	</style>
	<script type='text/javascript' src="<?php echo esc_url( includes_url( 'js/jquery/jquery.js' ) ); ?>"></script>
	<script type="text/javascript">
            		jQuery( function( $ ) {

            			$( '.wolf-jplayer-playlist' ).find( 'span.close-wolf-jp-share' ).click( function() {
            				$( this ).parent().parent().parent().fadeOut();
            			} );

            			$( '.wolf-jp-share-icon').click( function() {
            				var container = $( this ).parent().parent().parent();
            				container.find('.wolf-jp-overlay').fadeIn();
            			} );

            			jQuery('.wolf-share-jp-popup').click(function() {
		 		var url = jQuery( this ).attr('href');
				var popup = window.open( url, 'null', 'height=350,width=570, top=150, left=150');
				if ( window.focus ) {
					popup.focus();
				}
				return false; 
			} );
            		} );
	</script>
	<!-- HTML5 and media queries Fix for IE --> 
	<!--[if IE]>
		<script src="<?php echo esc_url( WOLF_JPLAYER_PLUGIN_URL . '/assets/js/html5.js' ); ?>"></script>
	<![endif]-->
	<!-- End Fix for IE --> 
</head>
<body>
	<section id="main">
		<?php echo wolf_jplayer_show_playlist( $id, $in_popup, $embed, true ); ?>
	</section>
	<?php
	$buy_itunes = __( 'Buy on iTunes', 'wolf' );
	$buy_amazon = __( 'Buy on amazon', 'wolf' );
	$buy_now    = __( 'Buy now', 'wolf' );
	$download   = __( 'Right click and save link to download the mp3', 'wolf' );
	?>
	<script type="text/javascript">
		/* <![CDATA[ */
		var WolfjPlayerParams = { 
			"iTunesText": "<?php echo sanitize_text_field( $buy_itunes ); ?>", 
			"amazonText": "<?php echo sanitize_text_field( $buy_amazon ); ?>", 
			"buyNowText": "<?php echo sanitize_text_field( $buy_now ); ?>", 
			"downloadText": "<?php echo sanitize_text_field( $download ); ?>",
			"scrollBar": ""
		};
		/* ]]> */
	</script>
            	<script type='text/javascript' src="<?php echo esc_url( WOLF_JPLAYER_PLUGIN_URL . '/assets/js/min/jquery.jplayer.concat.min.js' ); ?>"></script>
</body>
</html>