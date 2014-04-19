<?php
/**
 * Plugin Name: Wolf jPlayer
 * Plugin URI: http://wpwolf.com/plugin/wolf-jplayer
 * Description: A WordPress plugin based on the jPlayer jQuery plugin. Allows multiple playlists and supports multiple uploads.
 * Version: 2.1.7.2
 * Author: WpWolf
 * Author URI: http://wpwolf.com/
 * Requires at least: 3.5
 * Tested up to: 3.9
 *
 * Text Domain: wolf
 * Domain Path: /lang/
 *
 * @package WolfjPlayer
 * @author WpWolf
 *
 * Being a free product, this plugin is distributed as-is without official support. 
 * Verified customers however, who have purchased a premium theme
 * at http://themeforest.net/user/BrutalDesign/portfolio?ref=BrutalDesign
 * will have access to support for this plugin in the forums
 * http://help.wpwolf.com/
 *
 * Copyright (C) 2013 Constantin Saguin
 * This WordPress Plugin is a free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * It is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * See http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Wolf_Jplayer' ) ) {
	/**
	 * Main Wolf_Jplayer Class
	 *
	 * Contains the main functions for Wolf_Jplayer
	 *
	 * @class Wolf_Jplayer
	 * @since 1.0.0
	 * @package WolfjPlayer
	 * @author WpWolf
	 */
	class Wolf_Jplayer {

		/**
		 * @var string
		 */
		public $version = '2.1.7.2';

		/**
		 * @var string
		 */
		private $update_url = 'http://plugins.wpwolf.com/update';

		/**
		 * @var string
		 */
		public $plugin_url;

		/**
		 * @var string
		 */
		public $plugin_path;

		/**
		 * WolfjPlayer Constructor.
		 *
		 */
		public function __construct() {

			define( 'WOLF_JPLAYER_PLUGIN_VERSION', $this->version );
			define( 'WOLF_JPLAYER_PLUGIN_URL', $this->plugin_url() );
			define( 'WOLF_JPLAYER_PLUGIN_DIR', dirname( __FILE__ ) );

			// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
			register_activation_hook( __FILE__, array( $this, 'activate' ) );
			
			// plugin update notification
			add_action( 'admin_init', array( $this, 'update' ), 5 );
		
			// admin hooks
			add_action( 'admin_menu', array( $this, 'add_menu' ) );
			add_action( 'admin_init', array( $this, 'jplayer_options' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_script' ) );
			add_action( 'after_setup_theme', array( $this, 'options_init' ) );

			// Include required files
			$this->includes();

			add_action( 'init', array( $this, 'init' ), 0 );

			// Widget
			add_action( 'widgets_init', array( $this, 'register_widgets' ) );

		}

		/**
		 * Activation function
		 *
		 * Create jPlayer tables
		 */
		public function activate( $network_wide ) {
			
			$this->create_jplayer_tables();
		}

		/**
		 * Plugin update
		 *
		 * @return string
		 */
		public function update() {
			
			$plugin_data     = get_plugin_data( __FILE__ );
			$current_version = $plugin_data['Version'];
			$plugin_slug     = plugin_basename( dirname( __FILE__ ) );
			$plugin_path     = plugin_basename( __FILE__ );
			$remote_path     = $this->update_url . '/' . $plugin_slug;
			
			if ( ! class_exists( 'Wolf_WP_Update' ) )
				include_once('classes/class-wp-update.php' );
			
			$wolf_plugin_update = new Wolf_WP_Update( $current_version, $remote_path, $plugin_path );
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 */
		public function includes() {

			if ( ! is_admin() || defined( 'DOING_AJAX' ) )
				$this->frontend_includes();

			// Core functions
			include_once( 'includes/functions.php' );

		}

		/**
		 * Include required frontend files.
		 */
		public function frontend_includes() {
			
			// Show player
			include_once( 'includes/jplayer-show.php' );
			
		}

		/**
		 * register_widgets function.
		 */
		public function register_widgets() {
			
			// Include
			include_once( 'includes/jplayer-widget.php' );

			// Register widgets
			register_widget( 'WJ_Playlist_Widget' );
			
		}

		/**
		 * Init WolfJplayer when WordPress Initialises.
		 */
		public function init() {

			// Set up localisation
			$this->load_plugin_textdomain();

			// Hooks
			add_filter( 'template_include', array( $this, 'template_redirect' ) );
		}

		/**
		 * Load Localisation files.
		 */
		public function load_plugin_textdomain() {

			$domain = 'wolf';
			$locale = apply_filters( 'wolf', get_locale(), $domain );
			load_textdomain( $domain, WP_LANG_DIR.'/'.$domain.'/'.$domain.'-'.$locale.'.mo' );
			load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

		}

		/**
		 * Create tables
		 */
		public function create_jplayer_tables() {
			
			global $wpdb;

			$jplayer_playlists_table = "CREATE  TABLE IF NOT EXISTS `{$wpdb->prefix}wolf_jplayer_playlists` (
				`id` INT NOT NULL AUTO_INCREMENT ,
				`name` VARCHAR(255) NOT NULL ,
				`logo` VARCHAR(255) NULL ,
				`autoplay` VARCHAR(20) NULL ,
				PRIMARY KEY (`id`) );";

			$jplayer_table = "CREATE  TABLE IF NOT EXISTS `{$wpdb->prefix}wolf_jplayer` (
				`id` INT NOT NULL AUTO_INCREMENT ,
				`mp3` VARCHAR(255) NULL ,
				`ogg` VARCHAR(255) NULL ,
				`name` VARCHAR(255) NULL ,
				`artist` VARCHAR(255) NULL ,
				`poster` VARCHAR(255) NULL ,
				`free` VARCHAR(45) NULL ,
				`external` INT NOT NULL DEFAULT 0 ,
				`itunes` VARCHAR(255) NULL ,
				`amazon` VARCHAR(255) NULL ,
				`buy` VARCHAR(255) NULL ,
				`position` INT NOT NULL DEFAULT 0 ,
				`playlist_id` INT NULL ,
				PRIMARY KEY (`id`) );";

			$wpdb->query( $jplayer_playlists_table );
			$wpdb->query( $jplayer_table );

			/* Check playlist default artwork row for version < 2.1 */
			$req = "SELECT * FROM `{$wpdb->prefix}wolf_jplayer_playlists` LIMIT 1";
			$check_col = $wpdb->get_row( $req );
			if ( ! isset( $check_col->poster ) ) {
				$add_col = "ALTER TABLE `{$wpdb->prefix}wolf_jplayer_playlists` ADD COLUMN `poster` VARCHAR(255) NULL";
				$wpdb->query( $add_col );
			}

		}

		/**
		 * Custom template for single player page
		 *
		 * @param string $template
		 * @return string $template
		 */
		public function template_redirect( $template ) {

			if ( isset( $_GET['playlist_id'] ) ) {

				$new_template = WOLF_JPLAYER_PLUGIN_DIR . '/templates/jplayer-single.php';
				
				if ( '' != $new_template ) {
					$template = $new_template ;
				}
			}

			if ( isset( $_GET['playlist_id'] ) && isset( $_GET['iframe'] ) ) {

				$new_template = WOLF_JPLAYER_PLUGIN_DIR . '/templates/jplayer-frame.php';
				
				if ( '' != $new_template ) {
					$template = $new_template;
				}
			}

			return $template;
		}

		/**
		 * Enqueue admin CSS
		 */
		public function admin_styles() {
			
			if ( isset( $_GET['page'] ) ) {
				
				if ( $_GET['page'] == 'wolf-jplayer-panel' ) {
					wp_enqueue_style( 'wolf-jplayer-admin', $this->plugin_url() . '/assets/css/min/jplayer-admin.min.css', array(), $this->version, 'all' );
				}
					
				
				if ( $_GET['page'] == 'wolf-jplayer-options' ) {
					wp_enqueue_style( 'wp-color-picker' );
				}
					
			}
		}

		/**
		 * Enqueue admin scripts
		 */
		public function admin_script() {
			if ( isset( $_GET['page'] ) ) {
			
				if ( 
					$_GET['page'] == 'wolf-jplayer-panel' 
					|| $_GET['page'] == 'wolf-jplayer-options'
				) {
					wp_enqueue_media();
					wp_enqueue_script( 'jquery-ui-sortable' );
					wp_enqueue_script( 'tipsy', $this->plugin_url() . ' /assets/js/min/tipsy.min.js', 'jquery', '1.0.0a', true );
					wp_enqueue_script( 'wolf-jplayer-admin-upload', $this->plugin_url() . ' /assets/js/min/upload.min.js', 'jquery', $this->version, true );
					wp_enqueue_script( 'wolf-jplayer-admin-colorpicker', $this->plugin_url() . ' /assets/js/min/colorpicker.min.js', array( 'wp-color-picker' ), $this->version, true );
				}
			}
			
		}

		/**
		 * Add admin menu
		 */
		public function add_menu() {

			add_menu_page( 'jPlayer', 'jPlayer', 'activate_plugins', 'wolf-jplayer-panel', array( $this, 'jplayer_panel' ) , 'dashicons-format-audio' );
			add_submenu_page( 'wolf-jplayer-panel',  __( 'Manage playlists', 'wolf' ), __( 'Manage playlists', 'wolf' ), 'activate_plugins', 'wolf-jplayer-panel', array( $this, 'jplayer_panel' ) );
			add_submenu_page( 'wolf-jplayer-panel',  __( 'Options', 'wolf' ), __( 'Options', 'wolf' ), 'activate_plugins', 'wolf-jplayer-options', array( $this, 'wolf_jplayer_settings' ) );
		}

		/**
		 * Get player options
		 *
		 * @param string $value
		 * @return string
		 */
		public function get_option( $value ) {
			global $options;
			$settings = get_option( 'wolf_jplayer_settings' );
			
			if ( isset( $settings[$value] ) )
				return $settings[$value];

		}

		/**
		 * Set default options
		 */
		public function options_init() {
			global $options;

			if ( false === get_option( 'wolf_jplayer_settings' )  ) {

				$default = array(
					'bg_color' => '#353535',
					'bg_opacity' => 100,
					'font_color' => '#ffffff',
					'social_meta' => 1,
					'scrollbar' => 0,
					'disable_flash' => 0,
					'song_count_before_scroll' => 6
				);

				add_option( 'wolf_jplayer_settings', $default );
			}
		}

		/**
		 * Register options fields
		 */
		public function jplayer_options() {
			
			register_setting( 'wolf-jplayer-options', 'wolf_jplayer_settings', array( $this, 'settings_validate' ) );
			add_settings_section( 'wolf-jplayer-options', '', array( $this, 'section_intro' ), 'wolf-jplayer-options' );
			add_settings_field( 'color', __( 'Background Color', 'wolf' ), array( $this, 'section_color' ), 'wolf-jplayer-options', 'wolf-jplayer-options' );
			add_settings_field( 'bg_opacity', __( 'Background Opacity in percent', 'wolf' ), array( $this, 'section_bg_opacity' ), 'wolf-jplayer-options', 'wolf-jplayer-options' );
			add_settings_field( 'font_color', __( 'Text and Icons Color', 'wolf' ), array( $this, 'section_font_color' ), 'wolf-jplayer-options', 'wolf-jplayer-options' );
			add_settings_field( 'scrollbar', __( 'Enable scrollbar when the playlist has more than 5 songs (beta)', 'wolf' ), array( $this, 'section_scrollbar' ), 'wolf-jplayer-options', 'wolf-jplayer-options' );
			add_settings_field( 'song_count_before_scroll', __( 'Number of songs to display before showing a scrollbar', 'wolf' ), array( $this, 'section_song_count_before_scroll' ), 'wolf-jplayer-options', 'wolf-jplayer-options' );
			add_settings_field( 'social_meta', __( 'Generate  facebook and google plus metadata on playlist single page', 'wolf' ), array( $this, 'section_social_meta' ), 'wolf-jplayer-options', 'wolf-jplayer-options' );
			add_settings_field( 'disable_flash', __( 'Disable flash fallback', 'wolf' ), array( $this, 'section_disable_flash' ), 'wolf-jplayer-options', 'wolf-jplayer-options' );
			add_settings_field( 'infos', __( 'Infos', 'wolf' ), array( $this, 'section_infos' ), 'wolf-jplayer-options', 'wolf-jplayer-options' );
		
		}

		/**
		 * Validate options
		 *
		 * @return string
		 */
		public function settings_validate( $input ) {
		
			$input['bg_color']         = sanitize_text_field( $input['bg_color'] );
			$input['font_color']    = sanitize_text_field( $input['font_color'] );
			$input['bg_opacity']    = absint( $input['bg_opacity'] );
			$input['scrollbar']     = absint( $input['scrollbar'] );
			$input['song_count_before_scroll'] = ( 2 > absint( $input['song_count_before_scroll'] ) ) ? 2 : absint( $input['song_count_before_scroll'] );
			$input['social_meta']   = absint( $input['social_meta'] );
			$input['disable_flash'] = absint( $input['disable_flash'] );

			return $input;
		
		}

		/**
		 * Intro used for debug
		 */
		public function section_intro() {
			//global $options;
			//echo "<pre>";
			//print_r(get_option( 'wolf_jplayer_settings' ));
			//echo "</pre>";
		}

		/**
		 * Background Color option
		 *
		 * @return string
		 */
		public function section_color() {
			?>
			<input type="text" value="<?php echo sanitize_text_field( $this->get_option( 'bg_color' ) ); ?>" class="wolf-jplayer-color" name="wolf_jplayer_settings[bg_color]">
			<?php
		}

		/**
		 * Background Color opacity
		 *
		 * @return string
		 */
		public function section_bg_opacity() {
			?>
			<input type="text" value="<?php echo sanitize_text_field( $this->get_option( 'bg_opacity' ) ); ?>" name="wolf_jplayer_settings[bg_opacity]">
			<?php
		}


		/**
		 * Font Color option
		 *
		 * @return string
		 */
		public function section_font_color() {
			?>
			<input type="text" value="<?php echo sanitize_text_field( $this->get_option( 'font_color' ) ); ?>" class="wolf-jplayer-color" name="wolf_jplayer_settings[font_color]">
			<?php
		}

		/**
		 * Enable scrollbar
		 *
		 * @return string
		 */
		public function section_scrollbar() {
			$checked = ( $this->get_option( 'scrollbar' ) == 1 ) ? 'checked="checked"' : '';
			?>
			<input type="hidden" name="wolf_jplayer_settings[scrollbar]" value="0">
			<label for="wolf_jplayer_settings[scrollbar]">
			<input type="checkbox" value="1" <?php echo wp_kses_post( $checked ); ?> name="wolf_jplayer_settings[scrollbar]">
			</label>
			<?php
		}

		/**
		 * Number of songs to display before showing a scrollbar
		 *
		 * @return string
		 */
		public function section_song_count_before_scroll() {
			?>
			<input type="text" value="<?php echo sanitize_text_field( $this->get_option( 'song_count_before_scroll' ) ); ?>" name="wolf_jplayer_settings[song_count_before_scroll]">
			<?php
		}

		/**
		 * Disable flash option
		 *
		 * @return string
		 */
		public function section_disable_flash() {
			$checked = ( $this->get_option( 'disable_flash' ) == 1 ) ? 'checked="checked"' : '';
			?>
			<input type="hidden" name="wolf_jplayer_settings[disable_flash]" value="0">
			<label for="wolf_jplayer_settings[disable_flash]">
			<input type="checkbox" value="1"  <?php echo wp_kses_post( $checked ); ?> name="wolf_jplayer_settings[disable_flash]">
			</label>
			<?php
		}

		/**
		 * Enable social meta option
		 *
		 * @return string
		 */
		public function section_social_meta() {
			$checked = ( $this->get_option( 'social_meta' ) ) ? 'checked="checked"' : '';
			?>
			<input type="hidden" name="wolf_jplayer_settings[social_meta]" value="0">
			<label for="wolf_jplayer_settings[social_meta]">
			<input type="checkbox" value="1"  <?php echo wp_kses_post( $checked ); ?> name="wolf_jplayer_settings[social_meta]">
			</label>
			<?php
		}

		/**
		 * Info help
		 *
		 * @return string
		 */
		public function section_infos() {
			?>
			<p><?php _e( 'If you choose to disable the flash fallback, you will have to upload an ogg file for each song. This is useful only if you care about the compatibility with firefox on IOS.' ) ?></p>
			<?php
		}

		/**
		 * Print options form
		 *
		 * @return string
		 */
		public function wolf_jplayer_settings() {
			?>
			<div class="wrap">
				<div id="icon-options-general" class="icon32"></div>
				<h2><?php _e( 'Player Color Settings', 'wolf' ); ?></h2>
				<?php if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ) { ?>
				<div id="setting-error-settings_updated" class="updated settings-error"> 
					<p><strong><?php _e( 'Settings saved.', 'wolf' ); ?></strong></p>
				</div>
				<?php } ?>
				<form action="options.php" method="post">
					<?php settings_fields( 'wolf-jplayer-options' ); ?>
					<?php do_settings_sections( 'wolf-jplayer-options' ); ?>
					<p class="submit">
						<input name="save" type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'wolf' ); ?>">
					</p>
				</form>
			</div>
			<?php
		}

		/**
		 * jplayer panel - playlist manager
		 */
		public function jplayer_panel() {
			require_once( 'includes/admin/admin-panel.php' );
		}

		/**
		 * Get the plugin url.
		 *
		 * @return string
		 */
		public function plugin_url() {
			if ( $this->plugin_url ) return $this->plugin_url;
			return $this->plugin_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		/**
		 * Get the plugin path.
		 *
		 * @return string
		 */
		public function plugin_path() {
			if ( $this->plugin_path ) return $this->plugin_path;
			return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

	} // end class

	/**
	 * Init Wolf_Jplayer class
	 */
	$GLOBALS['wolf_jplayer'] = new Wolf_Jplayer();

} // end class exists check