<?php
namespace MCoder\EPS;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * Assets handlers class
 */
class Assets {

    /**
     * Class constructor
     */
    function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'register_assets' ] );
    }

    /**
     * All available scripts
     *
     * @return array
     */
    public function get_scripts() {
        return [
            'jquery-datatables-js' => [
                'src'     => EPS_ASSETS . '/js/jquery.dataTables.min.js',
                'version' => '1.10.22',
                'deps'    => [ 'jquery' ]
            ],
            'eps-script' => [
                'src'     => EPS_ASSETS . '/js/frontend.js',
                'version' => filemtime( EPS_PATH . '/assets/js/frontend.js' ),
                'deps'    => [ 'jquery', 'jquery-datatables-js' ]
            ],
            'eps-checkout-animation' => [
                'src'     => EPS_ASSETS . '/js/eps-checkout.js',
                'version' => file_exists( EPS_PATH . '/assets/js/eps-checkout.js' ) ? filemtime( EPS_PATH . '/assets/js/eps-checkout.js' ) : EPS_VERSION,
                'deps'    => [ 'jquery' ]
            ]
        ];
    }

    /**
     * All available styles
     *
     * @return array
     */
    public function get_styles() {
        return [
            'jquery-datatables-css' => [
                'src'     => EPS_ASSETS . '/css/jquery.dataTables.min.css',
                'version' => '1.11.3',
                'deps'    => []
            ],
            'eps-style' => [
                'src'     => EPS_ASSETS . '/css/frontend.css',
                'version' => filemtime( EPS_PATH . '/assets/css/frontend.css' )
            ],
            'eps-admin-style' => [
                'src'     => EPS_ASSETS . '/css/admin.css',
                'version' => filemtime( EPS_PATH . '/assets/css/admin.css' )
            ]
        ];
    }

    /**
     * Register scripts and styles
     *
     * @return void
     */
    public function register_assets() {
        $scripts = $this->get_scripts();
        $styles  = $this->get_styles();

        foreach ( $scripts as $handle => $script ) {
            $deps = isset( $script['deps'] ) ? $script['deps'] : false;

            wp_register_script( $handle, $script['src'], $deps, $script['version'], true );
        }

        foreach ( $styles as $handle => $style ) {
            $deps = isset( $style['deps'] ) ? $style['deps'] : false;

            wp_register_style( $handle, $style['src'], $deps, $style['version'] );
        }

        wp_localize_script( 'eps-script', 'datatablesajax', array('transection_url' => admin_url('admin-ajax.php')));
        wp_localize_script(
            'eps-script',
            'eps_ajax',
            [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('eps_update_product_status_nonce')
            ]
        );

        $logo_url = defined('EPS_ASSETS') ? EPS_ASSETS . '/images/EPS_logo.png' : 'https://eps.com.bd/images/logo.png';
        wp_localize_script(
            'eps-checkout-animation',
            'eps_checkout_params',
            [
                'logo_url' => $logo_url
            ]
        );

        // Auto-enqueue checkout animation on checkout / pay pages
        if ( function_exists('is_checkout') && is_checkout() ) {
            wp_enqueue_style( 'eps-style' );
            wp_enqueue_script( 'eps-checkout-animation' );
        }
    }
}
