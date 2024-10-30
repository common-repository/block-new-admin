<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

class Bna_Options
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;



    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );

    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
    // This page will be under "Settings"
        add_options_page(
            __('Block new admin','block-new-admin'), 
            __('Block new admin','block-new-admin'), 
            'manage_options', 
            'bna-settings-admin', 
            array( $this, 'create_admin_page' )
        );

    }
/**
     * Options page callback
     */
    public function create_admin_page()   {

        // Set class property
        $this->options = get_option( 'bna_options' );
        $dir = plugin_dir_path( __DIR__ );
        wp_enqueue_script( 'tooltip', plugins_url( 'js/tooltip.js', dirname( __FILE__ ) ) ,array('jquery-ui-tooltip'),null,true );
        wp_enqueue_style('bnaadmin-css', plugins_url('block-new-admin/css/bna-admin-style.css'), false, null);

        ?>
        <div class="wrap" style="width: 310px;">
            <h2><?php _e( 'Block New Admin Settings', 'block-new-admin' ) ?></h2>
           <?php if ( ! isset( $_REQUEST['settings-updated'] ) )
              $_REQUEST['settings-updated'] = false; ?>
 
  
           
            <div id="poststuff">
                <div id="post-body">
                    <div id="post-body-content">

                            <?php
     $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'bna-settings';
        ?>
         
        <h2 class="nav-tab-wrapper">
            <a href="?page=bna-settings-admin&tab=bna-settings" class="nav-tab <?php echo $active_tab == 'bna-settings' ? 'nav-tab-active' : ''; ?>"><?php _e('General','block-new-admin') ?></a>
            <a href="?page=bna-settings-admin&tab=HELP" class="nav-tab <?php echo $active_tab == 'HELP' ? 'nav-tab-active' : ''; ?>"><?php _e('HELP','block-new-admin') ?></a>
        </h2>

                         <form method="post" action="options.php">
                            <?php
                                // This prints out all hidden setting fields
                                
                                 if( $active_tab == 'bna-settings' ) {
                                    settings_fields( 'bna_options' );
                                 do_settings_sections( 'bna-setting-admin' );
                                  $butlabel=!isset( $this->options['bnapsw'] ) ? __('Activate Block', 'block-new-admin' ): __('Deactivate Block', 'block-new-admin' );
                                   submit_button($butlabel);
                                   load_template( $dir.'admin-parts/tab-footer.php');
                              

                             } else {
                                 settings_fields( 'bna_help' );
                                do_settings_sections( 'bna-help-admin' );
                            }
              
                            ?>
                        </form>
                    </div>
                </div> 
            </div>
          </div><?php load_template( $dir.'admin-parts/sidebar.php'); ?>
        <?php

    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting('bna_options', 'bna_options', array( $this, 'verify' ));

        add_settings_section(
            'settings_section', // ID
            __('Block new admin settings','block-new-admin'), // Title
            array( $this, 'bna_info' ), // Callback
            'bna-setting-admin' // Page
        ); 

        add_settings_section(
            'HELP_section', // ID
            __('Block new admin settings','block-new-admin'), // Title
            array( $this, 'bna_help' ), // Callback
            'bna-help-admin' // Page
        ); 

        $this->options = get_option( 'bna_options' );
        $fldlabel=!isset( $this->options['bnapsw'] ) ? __('Set your deactivation password ', 'block-new-admin' ): __('Insert Password to Deactivate', 'block-new-admin' );
        add_settings_field(
            'bnapsw', // ID
             $fldlabel , // Title 
            array( $this, 'bnapsw_callback' ), // Callback
            'bna-setting-admin', // Page
            'settings_section' // Section           
        );             
    }

    /**
     * Verify and sanitize setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function verify( $input )  {

       if( strlen($input['bnapsw'])==32  )  return $input;


        global $wpdb;
        $prefix= $wpdb->prefix;

        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
         if ($mysqli->connect_errno) {
             wp_die( "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
        }
        $this->options = get_option( 'bna_options' );
        if( isset( $this->options['bnapsw'] ) ) {
            // BLOCK ACTIVATED
            // Check if password is ok
            if (md5($input['bnapsw'])==$this->options['bnapsw']) {
                //REMOVE BLOCK
                  $dropsql="DROP TRIGGER IF EXISTS C".substr(md5($input['bnapsw']),0,4).substr(sha1($input['bnapsw']),0,4).";DROP TRIGGER IF EXISTS D".substr(md5($input['bnapsw']),0,4).substr(sha1($input['bnapsw']),0,4).";";
                   $result=mysqli_multi_query($mysqli,$dropsql);
                   delete_option('bna_options' );

            } else {

                return $this->options;
            }
        } elseif ( isset( $input['bnapsw'] ) )  {
           // BLOCK NOT ACTIVATED
          // add Block

            $sql = "CREATE TRIGGER C".substr(md5($input['bnapsw']),0,4).substr(sha1($input['bnapsw']),0,4)." BEFORE INSERT ON ".$prefix."usermeta
                            FOR EACH ROW
                            BEGIN
                            IF (new.meta_key = '".$prefix."capabilities' AND new.meta_value LIKE '%administrator%') then
                            set new.meta_value = replace(NEW.meta_value, 'administrator', 'HACKERATTEMPT');
                            END IF;
              END;
              CREATE TRIGGER D".substr(md5($input['bnapsw']),0,4).substr(sha1($input['bnapsw']),0,4)." BEFORE UPDATE ON ".$prefix."usermeta
                            FOR EACH ROW
                            BEGIN
                            IF (new.meta_key = '".$prefix."capabilities' AND new.meta_value LIKE '%administrator%') then
                            set new.meta_value = replace(NEW.meta_value, 'administrator', 'HACKERATTEMPT');
                            END IF;
              END;
            ";
            $result=mysqli_multi_query($mysqli,$sql);
            if (!$result) wp_die(var_dump($mysqli->error));

            $new_input = array();
            $new_input['bnapsw'] = md5($input['bnapsw']);
             
            return $new_input;
        }

        //return $input;

    }

    /** 
     * Print the Section text
     */
    public function bna_info()
    {
        printf('<h2>%s</h2>%s', __('PLEASE READ CAREFULLY BEFORE ACTIVATION', 'block-new-admin'), __('This plugin is a security block for creation of new administrators. PLUGIN DEACTIVATION OR FOLDER DELETION MANTAIN THE BLOCK ACTIVE. The only way for deactivating the block is to use the password you chose. SO PLEASE PRESERVE YOUR PASSWORD WITH CARE !','block-new-admin'));
    }

    public function bna_help()
    {

        $dir = plugin_dir_path( __DIR__ );
         load_template( $dir.'admin-parts/help-bna.php'); 
    }
    /** 
     * Get the settings option array and print one of its values
     */
    public function bnapsw_callback()
    {
        printf(
            '<input type="password" id="bnapsw" name="bna_options[bnapsw]" value="" /> '
        );
        $this->options = get_option( 'bna_options' );
        $fldtip=!isset( $this->options['bnapsw'] ) ? __('Choose a password to protect the deactivation ', 'block-new-admin' ): __('Insert Your Password to Deactivate the Block. If you forgot it, please see the Help Tab', 'block-new-admin' );
        $this->tooltip($fldtip);
    }


    private function tooltip($text)
    {
        printf(
            '<span class="setting-tooltip dashicons dashicons-editor-help sizeup" title="%s"></span>', $text
        );

    }
}