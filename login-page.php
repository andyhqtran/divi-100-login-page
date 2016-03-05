<?php

/**
* @package Custom_Login_Page
* @version 0.0.1
*/

/*
* Plugin Name: Divi 100 Login Page
* Plugin URI: https://elegantthemes.com/
* Description: This plugin gives you the option to customize your login screen
* Author: Elegant Themes
* Version: 0.0.1
* Author URI: http://elegantthemes.com
* License: GPL3
*/

/**
 * Register plugin to Divi 100 list
 */
class ET_Divi_100_Custom_Login_Page_Config {
	public static $instance;

	/**
	 * Hook the plugin info into Divi 100 list
	 */
	function __construct() {
		add_filter( 'et_divi_100_settings', array( $this, 'register' ) );
		add_action( 'plugins_loaded',       array( $this, 'init' ) );
	}

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
	 * Define plugin info
	 *
	 * @return array plugin info
	 */
	public static function info() {
		$main_prefix = 'et_divi_100_';
		$plugin_slug = 'custom_login_page';

		return array(
			'main_prefix'        => $main_prefix,
			'plugin_name'        => __( 'Custom Login Page' ),
			'plugin_description' => __( 'Nullam quis risus eget urna mollis ornare vel eu leo.' ),
			'plugin_slug'        => $plugin_slug,
			'plugin_id'          => "{$main_prefix}{$plugin_slug}",
			'plugin_prefix'      => "{$main_prefix}{$plugin_slug}-",
			'plugin_version'     => 20160301,
			'plugin_dir_path'    => plugin_dir_path( __FILE__ ),
		);
	}

	/**
	 * et_divi_100_settings callback
	 *
	 * @param array  settings
	 * @return array settings
	 */
	function register( $settings ) {
		$info = self::info();

		$settings[ $info['plugin_slug'] ] = $info;

		return $settings;
	}

	/**
	 * Init plugin after all plugins has been loaded
	 */
	function init() {
		// Load Divi 100 Setup
		require_once( plugin_dir_path( __FILE__ ) . 'divi-100-setup/divi-100-setup.php' );

		// Load Login Page
		ET_Divi_100_Custom_Login_Page::instance();
	}
}
ET_Divi_100_Custom_Login_Page_Config::instance();

/**
 * Load Custom Login Page
 */
class ET_Divi_100_Custom_Login_Page {
	/**
	 * Unique instance of plugin
	 */
	public static $instance;
	public $config;
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
		$this->config   = ET_Divi_100_Custom_Login_Page_Config::info();
		$this->settings = maybe_unserialize( get_option( $this->config['plugin_id'] ) );
		$this->utils    = new Divi_100_Utils( $this->settings );

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
				'plugin_id'   => $this->config['plugin_id'],
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
						'type'                 => 'color',
						'id'                   => 'background-color',
						'label'                => __( 'Select Background Color' ),
						'sanitize_callback'    => 'et_divi_100_sanitize_alpha_color',
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
					),
					array(
						'type'                 => 'color',
						'id'                   => 'button-text-color',
						'label'                => __( 'Select Button Text Color' ),
						'sanitize_callback'    => 'et_divi_100_sanitize_alpha_color',
					),
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
		return apply_filters( $this->config['plugin_prefix'] . 'styles', array(
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

		return apply_filters( $this->config['plugin_prefix'] . 'get_selected_style', $style );
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
			$classes[] = esc_attr(  $this->config['plugin_prefix'] . '-style-' . $selected_style . ' et_divi_100_custom_login_page');
		}

		return $classes;
	}

	/**
	 * Load front end scripts
	 * @return void
	 */
	function enqueue_frontend_scripts() {
		wp_enqueue_style( 'custom-login-pages', plugin_dir_url( __FILE__ ) . 'assets/css/style.css', array(), $this->config['plugin_version'] );
		wp_enqueue_style( 'custom-login-pages-icon-font', plugin_dir_url( __FILE__ ) . 'assets/css/ionicons.min.css', array(), $this->config['plugin_version'] );
		wp_enqueue_script( 'custom-login-pages-scripts', plugin_dir_url( __FILE__ ) . 'assets/js/scripts.js', array( 'jquery'), $this->config['plugin_version'], true );
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
		$background_color     = $this->utils->get_value( 'background-color', '' );
		$logo_image_src       = $this->utils->get_value( 'logo-image', '' );
		$login_color          = $this->utils->get_value( 'login-color', '' );
		$button_text_color    = $this->utils->get_value( 'button-text-color', '' );
		$print_css_status     = false;
		$css                  = '<style type="text/css">';

		if ( $background_color && '' !== $background_color ) {
			$print_css_status = true;
			$css .= sprintf(
				'body {
					background-color: %s;
				}',
				esc_url( $background_color )
			);
		}

		if ( $background_image_src && '' !== $background_image_src ) {
			$print_css_status = true;
			$css .= sprintf(
				'body {
					background: url( "%s" ) center center no-repeat;
					background-size: cover;
				}',
				esc_url( $background_image_src )
			);
		}

		if ( $logo_image_src && '' !== $logo_image_src ) {
			$print_css_status = true;
			$css .= sprintf(
				'#login h1 a {
					background: url( "%s" ) center center no-repeat;
					background-size: cover;
					background-position: center center;
				}',
				esc_url( $logo_image_src )
			);
		}

		if ( $login_color && '' !== $login_color ) {
			$print_css_status = true;
			$css .= sprintf(
				'.wp-core-ui .button-primary,
				.wp-core-ui .button-primary:hover{
					background-color: %1$s;
					border-color: %1$s;
					box-shadow: none;
					text-shadow: none;
				}',
				et_divi_100_sanitize_alpha_color( $login_color )
			);
		}

		if ( $button_text_color && '' !== $button_text_color ) {
			$print_css_status = true;
			$css .= sprintf(
				'.wp-core-ui .button-primary{
					color: %1$s;
				}',
				et_divi_100_sanitize_alpha_color( $button_text_color )
			);
		}

		$css .= '</style>';

		if ( $print_css_status ) {
			echo $css;
		}
	}
}