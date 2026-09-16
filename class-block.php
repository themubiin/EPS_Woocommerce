<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

final class Eps_Gateway_Blocks extends AbstractPaymentMethodType {

    protected $gateway;
    protected $name = 'eps';

    public function initialize() {
        $this->settings = get_option( 'woocommerce_eps_settings', [] );
        $gateways       = function_exists('WC') && WC()->payment_gateways ? WC()->payment_gateways->payment_gateways() : [];
        $this->gateway  = $gateways['eps'] ?? new MCoder\EPS\Gateway\EPS();
    }

    public function is_active() {
        return !empty($this->gateway) && $this->gateway->is_available();
    }

    public function get_payment_method_script_handles() {

        $script_path = plugin_dir_path(__FILE__) . 'checkout.js';
        $version     = file_exists($script_path) ? filemtime($script_path) : '1.0.0';

        wp_register_script(
            'eps-blocks-integration',
            plugin_dir_url(__FILE__) . 'checkout.js',
            [
                'wc-blocks-registry',
                'wc-settings',
                'wp-element',
                'wp-html-entities',
                'wp-i18n',
            ],
            $version,
            true
        );
        if ( function_exists( 'wp_set_script_translations' ) ) {            
            wp_set_script_translations( 'eps-blocks-integration' );
        }
        return [ 'eps-blocks-integration' ];
    }

    public function get_payment_method_data() {
        return [
            'title'       => $this->gateway ? $this->gateway->title : __('Visa/Mastercard/MFS', 'eps'),
            'icon'        => $this->gateway ? esc_url( $this->gateway->icon ) : '',
            'description' => $this->gateway ? wp_kses_post( $this->gateway->description ) : '',
            'supports'    => $this->get_supported_features(),
        ];
    }

    public function get_supported_features() {
        return [ 'products' ];
    }
}