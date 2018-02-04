<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class GFireMSelectImageManager {

	public function __construct() {

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'fs_is_submenu_visible_' . GFireMSelectImage::getSlug(), array( $this, 'handle_sub_menu' ), 10, 2 );

		require_once 'class-gfirem-select-image-logs.php';
		new GFireMSelectImageLogs();

		try {
			//Check formidable pro
			if ( class_exists( 'FrmAppHelper' ) && method_exists( 'FrmAppHelper', 'pro_is_installed' )
			     && FrmAppHelper::pro_is_installed() ) {
				if ( GFireMSelectImage::getFreemius()->is_paying() ) {
					//Implements here
				}
			} else {
				add_action( 'admin_notices', array( $this, 'required_formidable_pro' ) );
			}
		} catch ( Exception $ex ) {
			GFireMSelectImageLogs::log( array(
				'action'         => get_class( $this ),
				'object_type'    => GFireMSelectImage::getSlug(),
				'object_subtype' => 'loading_dependency',
				'object_name'    => $ex->getMessage(),
			) );
		}
	}

	public function required_formidable_pro() {
		require GFireMSelectImage::$view . 'formidable_notice.php';
	}

	public static function load_plugins_dependency() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}

	public static function is_formidable_active() {
		self::load_plugins_dependency();

		return is_plugin_active( 'formidable/formidable.php' );
	}

	/**
	 * Handle freemius menus visibility
	 *
	 * @param $is_visible
	 * @param $menu_id
	 *
	 * @return bool
	 */
	public function handle_sub_menu( $is_visible, $menu_id ) {
		if ( $menu_id == 'account' ) {
			$is_visible = false;
		}

		return $is_visible;
	}

	/**
	 * Adding the Admin Page
	 */
	public function admin_menu() {
		add_menu_page( __( "Select Image", "gfirem_select_image-locale" ), __( "Select Image", "gfirem_select_image-locale" ), 'manage_options', GFireMSelectImage::getSlug(), array( $this, 'screen' ), 'dashicons-format-image' );
	}

	/**
	 * Screen to admin page
	 */
	public function screen() {
		GFireMSelectImage::getFreemius()->get_logger()->entrance();
		GFireMSelectImage::getFreemius()->_account_page_load();
		GFireMSelectImage::getFreemius()->_account_page_render();
	}
}