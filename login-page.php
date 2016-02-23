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
	public $plugin_prefix;

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
		$this->plugin_prefix = "{$this->main_prefix}{$this->plugin_slug}-";

		$this->init();
	}

	/**
	 * Hooking methods into WordPress actions and filters
	 *
	 * @return void
	 */
	private function init(){
		add_action( 'admin_menu',            array( $this, 'add_submenu' ), 30 ); // Make sure the priority is higher than Divi 100's add_menu()
		add_action( 'admin_enqueue_scripts', array( $this, 'add_submenu_scripts' ) );
		add_action( 'login_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ) );
		add_filter( 'login_body_class',      array( $this, 'body_class' ) );
		add_action( 'login_footer',          array( $this, 'print_background_image' ) );
	}

	/**
	 * Add submenu
	 * @return void
	 */
	function add_submenu() {
		add_submenu_page(
			$this->main_prefix . 'options',
			__( 'Custom Login Page' ),
			__( 'Custom Login Page' ),
			'switch_themes',
			$this->plugin_prefix . 'options',
			array( $this, 'render_options_page' )
		);
	}

	/**
	 * Add dashboard scripts
	 * @return void
	 */
	function add_submenu_scripts() {
		if ( isset( $_GET['page'] ) && $this->plugin_prefix . 'options' === $_GET['page'] ) {
			wp_enqueue_media();
			wp_enqueue_script( $this->plugin_prefix . 'dashboard-scripts', plugin_dir_url( __FILE__ ) . 'js/dashboard-scripts.js', array( 'jquery' ), '0.0.1', true );
			wp_localize_script( $this->plugin_prefix . 'dashboard-scripts', $this->main_prefix . 'js_params', array(
				'preview_dir_url'                 => plugin_dir_url( __FILE__ ) . 'preview/',
				'upload_background_inactive_text' => __( 'Select Background' ),
				'upload_background_active_text'   => __( 'Change Background' ),
				'media_uploader_title'            => __( 'Select Image for login page background' ),
				'media_uploader_button_text'      => __( 'Select this image' ),
			));
		}
	}

	/**
	 * Render options page
	 * @return void
	 */
	function render_options_page() {
		$is_option_updated         = false;
		$is_option_updated_success = false;
		$is_option_updated_message = '';
		$login_page_style          = 'login-page-style';
		$login_page_background_src = 'login-page-background-media-src';
		$login_page_background_id  = 'login-page-background-media-id';
		$nonce_action              = $this->plugin_prefix . 'options';
		$nonce                     = $this->plugin_prefix . 'options_nonce';

		// Verify whether an update has been occured
		if ( isset( $_POST[ $login_page_style ] ) && isset( $_POST[ $nonce ] ) ) {
			$is_option_updated = true;

			// Verify nonce. Thou shalt use correct nonce
			if ( wp_verify_nonce( $_POST[ $nonce ], $nonce_action ) ) {

				// Verify input
				if ( in_array( $_POST[ $login_page_style ], array_keys( $this->get_styles() ) ) ) {
					// Update option
					update_option( $this->plugin_prefix . 'styles', sanitize_text_field( $_POST[ $login_page_style ] ) );

					if ( isset( $_POST[ $login_page_background_src ] ) && '' != $_POST[ $login_page_background_src ] ) {
						update_option( $this->plugin_prefix . 'background_src', esc_url( $_POST[ $login_page_background_src ] ) );
					} else {
						delete_option( $this->plugin_prefix . 'background_src' );
					}

					if ( isset( $_POST[ $login_page_background_id ] ) && '' != $_POST[ $login_page_background_id ] ) {
						update_option( $this->plugin_prefix . 'background_id', intval( $_POST[ $login_page_background_id ] ) );
					} else {
						delete_option( $this->plugin_prefix . 'background_id' );
					}

					// Update submission status & message
					$is_option_updated_message = __( 'Your setting has been updated.' );
					$is_option_updated_success = true;
				} else {
					$is_option_updated_message = __( 'Invalid submission. Please try again.' );
				}
			} else {
				$is_option_updated_message = __( 'Error authenticating request. Please try again.' );
			}
		}

		// Get preview image
		$background_src = get_option( $this->plugin_prefix . 'background_src' );
		$background_id = get_option( $this->plugin_prefix . 'background_id' );

		?>
		<div class="wrap">
			<h1><?php _e( 'Custom Login Page' ); ?></h1>

			<?php if ( $is_option_updated ) { ?>
				<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible <?php echo $is_option_updated_success ? '' : 'error' ?>">
					<p>
						<strong><?php echo esc_html( $is_option_updated_message ); ?></strong>
					</p>
					<button type="button" class="notice-dismiss">
						<span class="screen-reader-text"><?php _e( 'Dismiss this notice.' ); ?></span>
					</button>
				</div>
			<?php } ?>

			<form action="" method="POST">
				<p><?php _e( 'Proper description goes here Nullam id dolor id nibh ultricies vehicula ut id elit. Vestibulum id ligula porta felis euismod semper. Nullam id dolor id nibh ultricies vehicula ut id elit. Vestibulum id ligula porta felis euismod semper.' ); ?></p>

				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<label for="login-page-style"><?php _e( 'Select Style' ); ?></label>
							</th>
							<td>
								<select name="login-page-style" id="login-page-style" data-preview-prefix="style-">
									<?php
									// Get saved style
									$style = $this->get_selected_style();

									// Render options
									foreach ( $this->get_styles() as $style_id => $style_label ) {
										printf(
											'<option value="%1$s" %3$s>%2$s</option>',
											esc_attr( $style_id ),
											esc_html( $style_label ),
											"{$style}" === "{$style_id}" ? 'selected="selected"' : ''
										);
									}
									?>
								</select>
								<p class="description"><?php _e( 'Proper description goes here' ); ?></p>
								<div class="option-preview" style="margin-top: 20px; <?php echo ( '' !== $style ) ? 'min-height: 182px; ' : ''; ?>">
								<?php if ( '' !== $style ) { ?>
									<img src="<?php echo plugin_dir_url( __FILE__ ) . 'preview/style-' . $style . '.gif'; ?>">
								<?php } ?>
								</div>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="login-page-background"><?php _e( 'Select Background Image' ); ?></label>
							</th>
							<td>
								<input name="login-page-background-media-src" id="login-page-background-media-src" type="hidden" value="<?php echo esc_attr( $background_src ); ?>">

								<input name="login-page-background-media-id" id="login-page-background-media-id" type="hidden" value="<?php echo esc_attr( $background_id ); ?>">

								<p>
									<button id="login-page-background-upload" class="button"><?php _e( 'Select Background' ); ?></button>
									<a href="#" id="login-page-background-remove" style="margin-left: 10px; display: none;"><?php _e( 'Remove Background' ); ?></a>
								</p>

								<div class="option-preview" id="login-page-background-preview" style="width: 100%; margin-top: 20px;">
									<?php
										if ( $background_src && '' !== $background_src ) {
											printf( '<img src="%s" style="%s" />', esc_attr( $background_src ), esc_attr( 'max-width: 100%;' ) );
										}
									?>
								</div><!-- .option-preview -->
							</td>
						</tr>
					</tbody>
				</table>
				<!-- /.form-table -->

				<?php wp_nonce_field( $nonce_action, $nonce ); ?>

				<p class="submit">
					<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes' ); ?>">
				</p>
				<!-- /.submit -->

			</form>
		</div>
		<!-- /.wrap -->
		<?php
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
		$style = get_option( $this->plugin_prefix . 'styles', '' );

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
	 * Print background image on login page
	 * @return void
	 */
	function print_background_image() {
		$background_image_src = get_option( $this->plugin_prefix . 'background_src' );

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
	}
}
ET_Divi_100_Custom_Login_Page::instance();