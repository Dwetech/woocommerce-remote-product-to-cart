<?php
/*
Plugin Name: WooCommerce Remote Product to Cart
Plugin URI:
Description: Add product to WooCommerce cart from remote site
Version: 0.0.1
Author: Abdullah Al Jahid
Author URI: http://Dwetech.com/
*/


class WooRemoteProductCart_Plugin {

    private static $notices = array();

    public static function init(){

        register_activation_hook( WOO_ROLE_PRICING_LIGHT_FILE, array( __CLASS__, 'activate' ) );
        register_deactivation_hook( WOO_ROLE_PRICING_LIGHT_FILE, array( __CLASS__, 'deactivate' ) );
        register_uninstall_hook( WOO_ROLE_PRICING_LIGHT_FILE, array( __CLASS__, 'uninstall' ) );


        add_action( 'init', array( __CLASS__, 'wp_init' ) );
        add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
        add_action( 'admin_menu', array( __CLASS__, 'add_plugin_page' ) );
        add_action( 'admin_init', array( __CLASS__, 'page_init' ) );

    }


    /**
     * Things that needs to be run on wp_init
     */
    public static function wp_init(){

        if( self::checkWooCommerceInstall() ) {

            self::woo_add_product_to_cart();

        }

    }

    /**
     * Check if WooCommerce is installed or not
     *
     * @return bool
     */
    function checkWooCommerceInstall() {

        if ( is_multisite() ) {
            $active_plugins = get_site_option( 'active_sitewide_plugins', array() );
            $active_plugins = array_keys( $active_plugins );
        } else {
            $active_plugins = get_option( 'active_plugins', array() );
        }

        $woo_is_active = in_array( 'woocommerce/woocommerce.php', $active_plugins );
        if ( !$woo_is_active ) {
            self::$notices[] = '<div class="error"><p><b>Important: </b>The <strong>Woocommerce Remote Product to Cart</strong> plugin requires the <a href="http://wordpress.org/extend/plugins/woocommerce" target="_blank">Woocommerce</a> plugin to be activated</p></div>';
        }

        return $woo_is_active;

    }


    public static function admin_notices() {
        if ( !empty( self::$notices ) ) {
            foreach ( self::$notices as $notice ) {
                echo $notice;
            }
        }
    }


    function woo_add_product_to_cart() {

        if( isset($_GET['woo_remote_product']) && isset($_GET['product_id']) && isset($_GET['product_price']) && isset($_GET['user_name']) ) {

            global $woocommerce;
            $woocommerce->cart->add_to_cart($product_id);

        }

    }



    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin',
            'My Settings',
            'manage_options',
            'my-setting-admin',
            array( __CLASS__, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property

        ?>
        <div class="wrap">
            <h2>My Settings</h2>
            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields( 'my_option_group' );
                do_settings_sections( 'my-setting-admin' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {

        $options = get_option( 'my_option_name' );

        register_setting(
            'my_option_group', // Option group
            'my_option_name'  // Option name
        );

        add_settings_section(
            'setting_section_id', // ID
            'My Custom Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'my-setting-admin' // Page
        );

        add_settings_field(
            'id_number', // ID
            'ID Number', // Title
            array( $this, 'id_number_callback' ), // Callback
            'my-setting-admin', // Page
            'setting_section_id' // Section
        );

    }




    /**
     * Plugin activation work.
     *
     */
    public static function activate() {

    }

    /**
     * Plugin deactivation.
     *
     */
    public static function deactivate() {

    }

    /**
     * Plugin uninstall. Delete database table.
     *
     */
    public static function uninstall() {

    }

}

WooRemoteProductCart_Plugin::init();