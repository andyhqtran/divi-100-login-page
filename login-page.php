<?php

/**
* @package Custom_Login_Page
* @version 0.0.1
*/

/*
* Plugin Name: Custom Login Page
* Plugin URI: https://elegantthemes.com/
* Description: This plugin gives you the option to customize your login screen
* Author: Elegant Themes
* Version: 0.0.1
* Author URI: http://elegantthemes.com
* License: GPL3
*/

/**
 * Load Divi 100 Setup
 */
require_once( plugin_dir_path( __FILE__ ) . '/divi-100-setup/divi-100-setup.php' );

/**
 * Load Custom Login Page
 */
class ET_Divi_100_Custom_Login_Page {
	/**
	 * Unique instance of plugin
	 */
	public static $instance;
	public $main_prefix;
	public $plugin_slug;
	public $plugin_id;
	public $plugin_prefix;
	protected $settings;
	protected $utils;

	/**
	 * Gets the instance of the plugin
	 */
	public static function instance(){
		if ( null === self::$instance ){
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct(){
		$this->main_prefix   = 'et_divi_100_';
		$this->plugin_slug   = 'custom_login_page';
		$this->plugin_id     = "{$this->main_prefix}{$this->plugin_slug}";
		$this->plugin_prefix = "{$this->plugin_id}-";
		$this->settings      = maybe_unserialize( get_option( $this->plugin_id ) );
		$this->utils         = new Divi_100_Utils( $this->settings );

		// Initialize if Divi is active
		if ( et_divi_100_is_active() ) {
			$this->init();
		}
	}

	/**
	 * Hooking methods into WordPress actions and filters
	 *
	 * @return void
	 */
	private function init(){
		add_filter( 'login_body_class',      array( $this, 'body_class' ) );
		add_action( 'login_footer',          array( $this, 'print_background_image' ) );
		add_filter( 'login_headerurl',       array( $this, 'modify_login_logo_url' ) );
		add_action( 'login_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ) );

		if ( is_admin() ) {
			$settings_args = array(
				'plugin_id'   => $this->plugin_id,
				'title'       => __( 'Custom Login Page' ),
				'description' => __( 'Nullam quis risus eget urna mollis ornare vel eu leo.' ),
				'fields'      => array(
					array(
						'type'                 => 'select',
						'preview_prefix'       => 'style-',
						'preview_height'       => 182,
						'id'                   => 'style',
						'label'                => __( 'Select Style' ),
						'description'          => __( 'Proper description goes here' ),
						'options'              => $this->get_styles(),
						'sanitize_callback'    => 'sanitize_text_field',
					),
					array(
						'type'                 => 'color',
						'id'                   => 'login-color',
						'label'                => __( 'Select Login Color' ),
						'sanitize_callback'    => 'et_divi_100_sanitize_alpha_color',
					),
					array(
						'type'                 => 'upload',
						'id'                   => 'logo-image',
						'label'                => __( 'Select Logo Image' ),
						'description'          => __( 'Proper description goes here' ),
						'button_active_text'   => __( 'Change Logo' ),
						'button_inactive_text' => __( 'Select Logo' ),
						'button_remove_text'   => __( 'Remove Logo' ),
						'sanitize_callback'    => 'esc_url',
					),
					array(
						'type'                 => 'url',
						'id'                   => 'logo-url',
						'label'                => __( 'Logo URL' ),
						'placeholder'          => 'http://wordpress.org',
						'description'          => __( 'Proper description goes here' ),
						'sanitize_callback'    => 'esc_url',
					),
					array(
						'type'                 => 'upload',
						'id'                   => 'background-image',
						'label'                => __( 'Select Background Image' ),
						'description'          => __( 'Proper description goes here' ),
						'button_active_text'   => __( 'Change Background' ),
						'button_inactive_text' => __( 'Select Background' ),
						'button_remove_text'   => __( 'Remove Background' ),
						'sanitize_callback'    => 'esc_url',
					)
				),
				'button_save_text' => __( 'Save Changes' ),
			);

			new Divi_100_Settings( $settings_args );
		}
	}

	/**
	 * List of valid styles
	 * @return void
	 */
	function get_styles() {
		return apply_filters( $this->plugin_prefix . 'styles', array(
			''  => __( 'Default' ),
			'1' => __( 'One' ),
			'2' => __( 'Two' ),
			'3' => __( 'Three' ),
			'4' => __( 'Four' ),
			'5' => __( 'Five' ),
			'6' => __( 'Six' ),
			'7' => __( 'Seven' ),
		) );
	}

	/**
	 * Get selected style
	 * @return string
	 */
	function get_selected_style() {
		$style = $this->utils->get_value( 'style', '' );

		return apply_filters( $this->plugin_prefix . 'get_selected_style', $style );
	}

	/**
	 * Add specific class to <body>
	 * @return array
	 */
	function body_class( $classes ) {
		// Get selected style
		$selected_style = $this->get_selected_style();

		// Assign specific class to <body> if needed
		if ( '' !== $selected_style ) {
			$classes[] = esc_attr(  $this->plugin_prefix . '-style-' . $selected_style . ' et_divi_100_custom_login_page');
		}

		return $classes;
	}

	/**
	 * Load front end scripts
	 * @return void
	 */
	function enqueue_frontend_scripts() {
		wp_enqueue_style( 'custom-login-pages', plugin_dir_url( __FILE__ ) . 'css/style.css' );
		wp_enqueue_style( 'custom-login-pages-icon-font', plugin_dir_url( __FILE__ ) . 'css/ionicons.min.css' );
		wp_enqueue_script( 'custom-login-pages-scripts', plugin_dir_url( __FILE__ ) . 'js/scripts.js', array( 'jquery'), '0.0.1', true );
	}

	/**
	 * Modify login logo url
	 */
	function modify_login_logo_url( $url ) {
		$custom_url = $this->utils->get_value( 'logo-url', false );

		if ( $custom_url && '' !== $custom_url ) {
			$url = esc_url( $custom_url );
		}

		return $url;
	}

	/**
	 * Print background image on login page
	 * @return void
	 */
	function print_background_image() {
		$background_image_src = $this->utils->get_value( 'background-image', '' );
		$logo_image_src       = $this->utils->get_value( 'logo-image', '' );
		$login_color          = $this->utils->get_value( 'login-color', '' );

		if ( $background_image_src && '' !== $background_image_src ) {
			printf(
				'<style type="text/css">
					body {
						background: url( "%s" ) center center no-repeat;
						background-size: cover;
					}
				</style>',
				esc_url( $background_image_src )
			);
		}

		if ( $logo_image_src && '' !== $logo_image_src ) {
			printf(
				'<style type="text/css">
					#login h1 a {
						background: url( "%s" ) center center no-repeat;
						background-size: cover;
						background-position: center center;
					}
				</style>',
				esc_url( $logo_image_src )
			);
		}

		if ( $login_color && '' !== $login_color ) {
			printf(
				'<style type="text/css">
					.wp-core-ui .button-primary{
						background-color: %1$s;
						border-color: %1$s;
						box-shadow: none;
						text-shadow: none;
					}
				</style>',
				et_divi_100_sanitize_alpha_color( $login_color )
			);
		}
	}
}
ET_Divi_100_Custom_Login_Page::instance();