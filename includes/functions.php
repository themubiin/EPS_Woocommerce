<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Insert a new Token
 *
 * @param  array  $args
 *
 * @return int|WP_Error
 */
function eps_insert_token( $args = [] ) {

    global $wpdb;

    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
    $inserted = $wpdb->insert( $wpdb->prefix . 'eps_token', $args );

    if ( ! $inserted ) {
        return 0;
    }

    return $wpdb->insert_id;
}

/**
 * Fetch a single info from the DB
 *
 * @param  int $order_id
 *
 * @return object
 */
function eps_get_token( $order_id ) {

    global $wpdb;

    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}eps_token WHERE order_id = %d ORDER BY id DESC", intval( $order_id ) ) );
}

/**
 * Delete a token
 *
 * @param  int $order_id
 *
 * @return int|boolean
 */
function eps_delete_token( $order_id ) {
    global $wpdb;

    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    return $wpdb->delete(
        $wpdb->prefix . 'eps_token',
        [ 'order_id' => $order_id ]
    );
}


/**
 * Safe helper to get EPS settings
 *
 * @return array
 */
function eps_get_settings() {
    $settings = get_option('mc_eps_settings');
    if ( is_string($settings) ) {
        $decoded = json_decode($settings, true);
        $settings = is_array($decoded) ? $decoded : maybe_unserialize($settings);
    }
    $defaults = [
        'api_base_url'  => '29e86e70-0ac6-45eb-ba04-9fcb0aaed12a', // Merchant ID
        'redirect_url'  => 'd44e705f-9e3a-41de-98b1-1674631637da', // Store ID
        'module_val'    => 'Epsdemo@gmail.com',                     // User Name
        'plugin_key'    => 'Epsdemo258@',                           // Password
        'merchent_code' => 'FHZxyzeps56789gfhg678ygu876o=',         // Hash Key
        'mode'          => 'sandbox'
    ];

    if ( !is_array($settings) || empty($settings) ) {
        return $defaults;
    }

    return wp_parse_args( $settings, $defaults );
}

function eps_insert_transaction_info( $args = [] ) {

    global $wpdb;
    $type = eps_get_settings();
    $mode = $type['mode'] ?? 'sandbox';
    
    $customer_account = sanitize_text_field( $args['customer_account'] ?? '' );
    if ( empty($customer_account) ) {
        return 0;
    }

    if ( $mode === 'sandbox' ) {
        $table_name = $wpdb->prefix . 'eps_sandbox_transections';
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $data = $wpdb->get_results(
            $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}eps_sandbox_transections WHERE customer_account = %s ORDER BY id DESC LIMIT 1", $customer_account )
        );
    } else {
        $table_name = $wpdb->prefix . 'eps_transections';  
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $data = $wpdb->get_results(
            $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}eps_transections WHERE customer_account = %s ORDER BY id DESC LIMIT 1", $customer_account )
        );
    }

    if ( count($data) > 0 ) {
        if ( !empty($data[0]->response_description) ) {
            $select_data_id = $data[0]->id;
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            $wpdb->update( $table_name, $args, array( 'id' => $select_data_id ) );
        }
    } else {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $inserted = $wpdb->insert( $table_name, $args );
        if ( ! $inserted ) {
            return 0;
        }
        return $wpdb->insert_id;
    }

    return 0;
}

// Backwards compatibility wrapper
if ( ! function_exists( 'insertTransectionInfo' ) ) {
    function insertTransectionInfo( $args = [] ) {
        return eps_insert_transaction_info( $args );
    }
}

add_action('wp_ajax_eps_transection_endpoint', 'eps_ajax_get_transactions'); //logged in only

function eps_ajax_get_transactions(){

    if ( ! current_user_can('manage_options') ) {
        wp_send_json_error( ['message' => 'Unauthorized'], 403 );
    }

    check_ajax_referer( 'eps_update_product_status_nonce', '_ajax_nonce', false );

    $response = []; 
    $posts = eps_get_transaction_info();
    
    $response['data'] = !empty($posts) ? $posts : [];        
    $response['recordsTotal'] = !empty($posts) ? count($posts) : 0;
    
    wp_send_json($response);
}

add_action('wp_ajax_eps_update_product_status', 'eps_update_product_status');

function eps_update_product_status() {
    global $wpdb;

    if ( ! current_user_can('manage_options') ) {
        wp_send_json_error(['message' => 'Unauthorized'], 403);
    }

    // Nonce check
    $nonce = isset($_POST['_ajax_nonce']) ? sanitize_text_field(wp_unslash($_POST['_ajax_nonce'])) : '';
    if ( ! wp_verify_nonce($nonce, 'eps_update_product_status_nonce') ) {
        wp_send_json_error(['message' => 'Invalid nonce'], 400);
    }

    // Validate POST
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $status = isset($_POST['status']) ? sanitize_text_field(wp_unslash($_POST['status'])) : '';

    if ($id <= 0 || empty($status)) {
        wp_send_json_error(['message' => 'Missing parameters'], 400);
    }
    $type = eps_get_settings();
    $mode = $type['mode'] ?? 'sandbox';
    if($mode=="sandbox"){
        $table_name = $wpdb->prefix . 'eps_sandbox_transections';
    }else{
        $table_name = $wpdb->prefix . 'eps_transections';  
    }

    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    $updated = $wpdb->update(
        $table_name,
        ['product_status' => $status],
        ['id' => $id]
    );

    if ($updated === false) {
        wp_send_json_error(['message' => 'DB update failed'], 500);
    }

    wp_send_json_success(['message' => 'Status updated']);
}

add_action('wp_ajax_eps_sync_gateway', 'eps_sync_gateway');

function eps_sync_gateway() {
    global $wpdb;

    if ( ! current_user_can('manage_options') ) {
        wp_send_json_error(['message' => 'Unauthorized'], 403);
    }

    // Check nonce
    $nonce = isset($_POST['_ajax_nonce']) ? sanitize_text_field(wp_unslash($_POST['_ajax_nonce'])) : '';
    if (!wp_verify_nonce($nonce, 'eps_update_product_status_nonce')) {
        wp_send_json_error(['message' => 'Invalid nonce'], 400);
    }
    
    // Current UTC time = end date
    $range = isset($_POST['range']) ? sanitize_text_field(wp_unslash($_POST['range'])) : '12_months';

    $endDate = (new DateTime('now', new DateTimeZone('UTC')))
        ->format('Y-m-d\TH:i:s.v\Z');

    if ($range === '7_days') {
        $startDate = (new DateTime('now', new DateTimeZone('UTC')))
            ->modify('-7 days')
            ->format('Y-m-d\TH:i:s.v\Z');
    } else {
        // default = 12 months
        $startDate = (new DateTime('now', new DateTimeZone('UTC')))
            ->modify('-12 months')
            ->format('Y-m-d\TH:i:s.v\Z');
    }
    $processor = new \MCoder\EPS\Gateway\Processor();
    // Call your EPS gateway API here
    $gateway_data = $processor->eps_sync_gateway($startDate, $endDate);

    if(!$gateway_data){
        wp_send_json_success(['data' => []]);
    }
    $type = eps_get_settings();
    $mode = $type['mode'] ?? 'sandbox';
    $is_sandbox = ($mode === 'sandbox');

        foreach ($gateway_data as $tran) {
        
            $tran_id = $tran['EPSTransactionId'];
        
            // Check if record exists
            if ($is_sandbox) {
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
                $existing = $wpdb->get_row(
                    $wpdb->prepare(
                        "SELECT id, is_sync FROM {$wpdb->prefix}eps_sandbox_transections WHERE customer_account = %s",
                        $tran_id
                    )
                );
                $table_name = $wpdb->prefix . 'eps_sandbox_transections';
            } else {
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
                $existing = $wpdb->get_row(
                    $wpdb->prepare(
                        "SELECT id, is_sync FROM {$wpdb->prefix}eps_transections WHERE customer_account = %s",
                        $tran_id
                    )
                );
                $table_name = $wpdb->prefix . 'eps_transections';
            }
        
            // If exists and not synced → UPDATE
            if ($existing) {
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
                $wpdb->update(
                    $table_name,
                    [
                        'order_id'             => $tran['OrderId'],
                        'amount'               => $tran['TotalAmount'],
                        'customer_account'     => $tran['EPSTransactionId'],
                        'response_description' => $tran['Status'],
                        'created_at'           => $tran['TransactionDate'],
                        'is_sync'              => 1,
                    ],
                    [
                        'id' => $existing->id
                    ],
                    ['%s','%s','%s','%s','%s','%d'],
                    ['%d']
                );
            }
            // If not exists → INSERT
            else {
               
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
                $wpdb->insert(
                    $table_name,
                    [
                        'order_id'             => $tran['OrderId'],
                        'amount'               => $tran['TotalAmount'],
                        'customer_account'     => $tran['EPSTransactionId'],
                        'transection_id'       => $tran['FinancialEntity'],
                        'response_description' => $tran['Status'],
                        'created_at'           => $tran['TransactionDate'],
                        'product_status'       => 'Pending',
                        'is_sync'              => 1
                    ],
                    ['%s','%s','%s','%s','%s','%s','%s','%d']
                );
            
            }
        }

    wp_send_json_success(['message' => 'Data synced successfully!']);
}

function eps_get_transaction_info( $args = [] ) {

    global $wpdb;

    $defaults = [
		'number'  => 100,
		'offset'  => 0,
		'orderby' => 'id',
		'order'   => 'DESC',
	];

	$args = wp_parse_args( $args, $defaults );
	$type = eps_get_settings();
    $mode = $type['mode'] ?? 'sandbox';

    $offset = intval( $args['offset'] );
    $number = intval( $args['number'] );

    if($mode=="sandbox"){
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $items = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}eps_sandbox_transections ORDER BY id DESC LIMIT %d, %d",
                $offset,
                $number
            )
        );
    }else{
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $items = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}eps_transections ORDER BY id DESC LIMIT %d, %d",
                $offset,
                $number
            )
        );
    }

	return $items;
}

// Backwards compatibility wrapper
if ( ! function_exists( 'getTransectionInfo' ) ) {
    function getTransectionInfo( $args = [] ) {
        return eps_get_transaction_info( $args );
    }
}

add_action( 'template_redirect', 'eps_handle_cancel_payment' );

function eps_handle_cancel_payment() {
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- External EPS gateway redirect return callback without nonce.
    if ( isset( $_GET['Status'] ) ) {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- External EPS gateway redirect return callback.
        $status_raw = sanitize_text_field( wp_unslash( $_GET['Status'] ) );
        if ( $status_raw === 'Failed' ) {
            $status  = $status_raw;
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $message = isset( $_GET['Message'] ) ? sanitize_text_field( wp_unslash( $_GET['Message'] ) ) : '';
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $eps_txn = isset( $_GET['MerchantTransactionId'] ) ? sanitize_text_field( wp_unslash( $_GET['MerchantTransactionId'] ) ) : '';
            if ( $eps_txn ) {
                $args = [
                    'response_description' => 'Failure',
                    'customer_account'     => $eps_txn,
                ];
                eps_insert_transaction_info( $args );
            }
            if ( function_exists( 'wc_add_notice' ) ) {
                /* translators: %s: failure message */
                $err_text = ! empty( $message ) ? sprintf( esc_html__( 'Payment failed: %s', 'eps' ), $message ) : esc_html__( 'Payment failed. Please try again.', 'eps' );
                wc_add_notice( $err_text, 'error' );
            }
        }
    }
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- External cancel URL redirect parameter.
    if ( ! isset( $_GET['cancel_order'] ) ) {
        return;
    }

    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $order_id = isset( $_GET['order_id'] ) ? absint( $_GET['order_id'] ) : 0;
   
    if ( ! $order_id ) {
        return;
    }

    $order = wc_get_order( $order_id );
    if ( ! $order ) {
        return;
    }

    // Optional: only your gateway
    if ( $order->get_payment_method() !== 'eps' ) {
        return;
    }
    
    // Read gateway response
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $status  = isset( $_GET['Status'] ) ? sanitize_text_field( wp_unslash( $_GET['Status'] ) ) : '';
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $eps_txn = isset( $_GET['MerchantTransactionId'] ) ? sanitize_text_field( wp_unslash( $_GET['MerchantTransactionId'] ) ) : '';
    $args = [
        'response_description' => $status === 'Aborted' ? 'Cancel' : 'Cancel',
        'customer_account'     => $eps_txn,
    ];

    if ( $order && ! $order->has_status( 'cancelled' ) ) {
        $order->update_status( 'cancelled', 'Payment was cancelled by the user.' );
    }           
    eps_insert_transaction_info( $args );
}

/**
 * Handle WooCommerce payment cancel via custom cancel_order query
 */
function eps_handle_payment_cancel_redirect() {
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- External cancel URL redirect parameter.
    if ( ! is_admin() && isset( $_GET['cancel_order'] ) && 'true' === $_GET['cancel_order'] ) {

        // sanitize
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $order_id = isset( $_GET['order_id'] ) ? intval( $_GET['order_id'] ) : 0;

        // Cancel order if exists
        if ( $order_id ) {
            $order = wc_get_order( $order_id );
            if ( $order && ! $order->has_status( 'cancelled' ) ) {
                $order->update_status( 'cancelled', 'Payment was cancelled by gateway/redirect.' );
            }
        }

        // Clear customer cart
        if ( WC()->cart ) {
            WC()->cart->empty_cart();
        }

        // Optional: remove query args and redirect (better UX to avoid re-trigger)
        $redirect = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/' );

        wp_safe_redirect( $redirect );
        exit;
    }
}
add_action( 'template_redirect', 'eps_handle_payment_cancel_redirect', 20 );

add_action( 'woocommerce_order_status_cancelled', 'eps_order_cancelled', 10, 1 );

function eps_order_cancelled( $order_id ) {
    // Action hook placeholder for order cancelled
}
add_action( 'woocommerce_order_status_failed', 'eps_order_failed', 10, 1 );

function eps_order_failed( $order_id ) {
    // Action hook placeholder for order failed
}

