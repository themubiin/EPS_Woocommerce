<?php
/**
 * Class EPS
 *
 * @package MCoder\EPS\Gateway
 */

namespace MCoder\EPS\Gateway;

if ( ! defined( ABSPATH ) ) {
    exit;
}


class EPS extends \WC_Payment_Gateway {

    /**
     * EPS constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {
        $this->id                 = 'eps';
        $this->has_fields         = false;
        $this->method_title       = __('eps', 'eps');
        $this->method_description = __('Payment Automation is Easier, Faster and more Secured via EPS.', 'eps');
        $this->title              = __('Visa/Mastercard/MFS', 'eps');
        $this->icon               = defined('EPS_ASSETS') ? EPS_ASSETS . '/images/EPS_logo.png' : 'https://eps.com.bd/images/logo.png';
        $banner_url               = defined('EPS_ASSETS') ? EPS_ASSETS . '/images/eps-Group95.png' : 'https://eps.com.bd/images/banner.png';
        $this->description        = '<img src="' . esc_url( $banner_url ) . '" alt="EPS Banner" style="max-width:480px; width:100%; height:auto; display:block; margin-top:8px;">';

        $this->init_form_fields();
        $this->init_settings();

        $this->enabled = $this->get_option('enabled', 'yes');

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, [ $this, 'process_admin_options' ]);
        add_action('woocommerce_thankyou_' . $this->id, [ $this, 'thank_you_page' ]);
    }

    /**
     * Initialise Gateway Settings Form Fields
     */
    public function init_form_fields() {
        $banner_url = defined('EPS_ASSETS') ? EPS_ASSETS . '/images/eps-Group95.png' : 'https://eps.com.bd/images/banner.png';
        $this->form_fields = [
            'enabled' => [
                'title'   => __('Enable/Disable', 'eps'),
                'type'    => 'checkbox',
                'label'   => __('Enable EPS Payment Gateway', 'eps'),
                'default' => 'yes',
            ],
            'title' => [
                'title'       => __('Title', 'eps'),
                'type'        => 'text',
                'description' => __('This controls the title which the user sees during checkout.', 'eps'),
                'default'     => __('Visa/Mastercard/MFS', 'eps'),
                'desc_tip'    => true,
            ],
            'description' => [
                'title'       => __('Description', 'eps'),
                'type'        => 'textarea',
                'description' => __('Payment method description that the customer will see on checkout.', 'eps'),
                'default'     => '<img src="' . esc_url( $banner_url ) . '" alt="EPS Banner" style="max-width:480px; width:100%; height:auto; display:block; margin-top:8px;">',
            ],
        ];
    }

    /**
     * Show EPS logo and label on checkout page under payment method
     */
    public function payment_fields() {
        $logo_url = defined('EPS_ASSETS') ? EPS_ASSETS . '/images/EPS_logo.png' : 'https://eps.com.bd/images/logo.png';
        echo '<div style="margin-bottom:10px; display:flex; align-items:center;">
            <img src="' . esc_url( $logo_url ) . '" style="height: 24px; margin-right: 10px;" alt="EPS Logo" />
            <strong style="color: black;">Visa/Mastercard/MFS</strong>
        </div>';
        if ( !empty($this->description) ) {
            echo wp_kses_post( wpautop( wptexturize( $this->description ) ) );
        }
    }

    /**
     * Admin options with settings link
     */
    public function admin_options() {
        parent::admin_options();

        $eps_settings_url = admin_url('admin.php?page=eps-settings');

        printf(
            // translators: %1$s: opening p tag, %2$s: payment method title, %3$s: opening link tag, %4$s: closing link tag, %5$s: closing p tag.
            esc_html__( '%1$sYou will get %2$s setting options in %3$s here %4$s.%5$s', 'eps' ),
            '<p>',
            esc_html($this->method_title),
            wp_kses_post(sprintf('<a href="%s">', $eps_settings_url)),
            '</a>',
            '</p>'
        );
    }

    /**
     * Handle payment process
     */
    public function process_payment($order_id) {
        $order = wc_get_order($order_id);
        if (!$order) {
            throw new \Exception( esc_html__( 'Invalid order.', 'eps' ) );
        }

        $processor = Processor::get_instance();

        $return_url = $this->get_return_url($order);
        $fail_url   = $order->get_cancel_order_url_raw();
        $invoice_id = 'INV_' . $order->get_id() . '_' . bin2hex(random_bytes(6));

        $order->update_meta_data('_eps_merchant_transaction_id', $invoice_id);
        $order->save();

        $create_payment_data = $processor->create_payment(
            (float) $order->get_total(),
            $invoice_id,
            $fail_url,
            $return_url,
            $order
        );

        if (!$create_payment_data) {
            $err_msg = __( 'Payment error: Unable to initiate payment with EPS. Please verify EPS credentials in settings.', 'eps' );
            wc_add_notice($err_msg, 'error');
            throw new \Exception( esc_html( $err_msg ) );
        }

        if (!isset($create_payment_data['RedirectURL']) || $create_payment_data['RedirectURL'] == null) {
            $err_msg = $create_payment_data['FailedReason'] ?? __( 'Payment error: Could not obtain gateway redirect URL.', 'eps' );
            wc_add_notice($err_msg, 'error');
            throw new \Exception( esc_html( $err_msg ) );
        }
        
        return [
            'result'              => 'success',
            'order_number'        => $order_id,
            'amount'              => (float) $order->get_total(),
            'checkout_order_pay'  => $order->get_checkout_payment_url(),
            'redirect'            => $create_payment_data['RedirectURL'],
            'create_payment_data' => $create_payment_data,
        ];
    }

    /**
     * Handle thank you page actions
     */
    public function thank_you_page($order_id) {
        $order = wc_get_order($order_id);

        if (!$order || 'eps' !== $order->get_payment_method()) {
            return;
        }

        $processor = Processor::get_instance();
        global $wp;
        $raw_query = isset( $_SERVER['QUERY_STRING'] ) ? sanitize_text_field( wp_unslash( $_SERVER['QUERY_STRING'] ) ) : '';
        $current_url = add_query_arg($raw_query, '', home_url($wp->request));
        $url_components = wp_parse_url($current_url);
        parse_str($url_components['query'] ?? '', $params);

        $merchant_txn_id = sanitize_text_field($params['MerchantTransactionId'] ?? '');
        $expected_txn_id = $order->get_meta('_eps_merchant_transaction_id');
        $status = 'Fail';
        $transection_id = sanitize_text_field($params['EPSTransactionId'] ?? '');

        // Security check: verify merchant transaction ID belongs to this order
        if ( !empty($expected_txn_id) && !empty($merchant_txn_id) && !hash_equals((string)$expected_txn_id, (string)$merchant_txn_id) ) {
            $order->update_status( 'failed', __( 'EPS payment verification failed: Transaction ID mismatch.', 'eps' ) );
            return;
        }

        $lookup_txn_id = !empty($merchant_txn_id) ? $merchant_txn_id : $expected_txn_id;

        if ( !empty($lookup_txn_id) ) {
            $check_payment_data = $processor->check_payment($lookup_txn_id);

            if (!$check_payment_data) {
                $status = 'Payment Status Check Fail';
            } else {
                $status = $check_payment_data['Status'] ?? ($check_payment_data['ErrorMessage'] ?? 'Fail');
                $transection_id = $check_payment_data['FinancialEntity'] ?? $transection_id;
            }
        }

        $args = [
            'order_id'             => $order_id,
            'amount'               => (float) $order->get_total(),
            'transection_id'       => $transection_id,
            'response_description' => $status,
            'customer_account'     => $lookup_txn_id,
        ];

        eps_insert_transaction_info($args);

        // Only update order status if not already completed/paid
        if ( !$order->is_paid() ) {
            if ( strcasecmp($status, 'Success') === 0 ) {
                $order->payment_complete( $transection_id );
                /* translators: %s: EPS transaction ID */
                $order->add_order_note( sprintf( __( 'EPS payment successful. Transaction ID: %s', 'eps' ), $transection_id ) );
            } elseif ( strcasecmp($status, 'Cancel') === 0 || strcasecmp($status, 'Aborted') === 0 ) {
                $order->update_status( 'cancelled', __( 'Payment was cancelled by the customer.', 'eps' ) );
            } else {
                /* translators: %s: EPS payment status response */
                $order->update_status( 'failed', sprintf( __( 'EPS payment failed. Status: %s', 'eps' ), $status ) );
            }
        }
    }
}
