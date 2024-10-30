<?php
/*
Plugin Name: Block New Admin
Description: Block the creation of a new administrator
Author: Roberto Bruno
Version: 1.1.0
Text Domain: block-new-admin
Domain Path: /languages/
Author URI: http://www.roberto-bruno.me
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Block_New_Admin.
 *
 * Main Block_New_Admin class initializes the plugin.
 *
 * @class		Block_New_Admin
 * @version		1.0.0
 * @author		Roberto Bruno
 */
class Block_New_Admin {

	/**
	 * Plugin version.
	 *
	 * @since 1.0.0
	 * @var string $version Plugin version number.
	 */
	public $version = '1.1.0';

	/**
	 * Plugin file.
	 *
	 * @since 1.0.0
	 * @var string $file Plugin file path.
	 */
	public $file = __FILE__;



	/**
	 * Instance of Block_New_Admin.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var object $instance The instance of Block_New_Admin.
	 */
	private static $instance;


	/**
	 * Construct.
	 *
	 * Initialize the class and plugin.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

				// Initialize plugin parts
		$this->init();
		register_activation_hook( __FILE__,  array( $this,'add_role' ));
		register_deactivation_hook( __FILE__,  array( $this,'remove_role' ));
	}

		/**
	 * init.
	 *
	 * Initialize plugin parts.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		// Load textdomain
		load_plugin_textdomain('block-new-admin',false,dirname( plugin_basename( __FILE__ ) ) . '/languages/');

	}

		/**
	 * Instance.
	 *
	 * An global instance of the class. Used to retrieve the instance
	 * to use on other files/plugins/themes.
	 *
	 * @since 1.0.0
	 * @return object Instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) :
			self::$instance = new self();
		endif;

		return self::$instance;

	}


	// @since 1.1.0
	public function add_role() { 

		if ($this->role_exists("HACKERATTEMPT")) return;

		add_role("HACKERATTEMPT","HACKER ATTEMPT",array(null));
	}

	// @since 1.1.0
	public function remove_role() { 
		//check if role exist before removing it
		$role=get_role("HACKERATTEMPT");
		if( $role) {
		      remove_role( "HACKERATTEMPT" );
		}
	}

	public function role_exists( $role ) {

	  if( ! empty( $role ) ) {
	    return $GLOBALS['wp_roles']->is_role( $role );
	  }
	  
	  return false;
	}

}


/**
 * The main function responsible for returning the Block_New_Admin object.
 *
 * Use this function like you would a global variable, except without needing to declare the global.
 *
 * Example: <?php Block_New_Admin()->method_name(); ?>
 *
 * @since 1.0.0
 *
 * @return object Block_New_Admin class object.
 */
if ( ! function_exists( 'Block_New_Admin' ) ) :

 	function Block_New_Admin() {

		if( is_admin() ) {
			require_once ("classes/Bna_Options.php");
			$wdpopt = new Bna_Options();
		}

    	return Block_New_Admin::instance();
	}

endif;

Block_New_Admin();

?>
