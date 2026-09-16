<?php
/**
 * Class Settings
 *
 * @since 0.0.1
 *
 * @package MCoder\EPS\Admin
 *
 * @author Md Mokbul Hossain
 */

namespace MCoder\EPS\Admin;

if ( ! defined( ABSPATH ) ) {
    exit;
}



/**
 * Class Settings
 */
class Settings {

    public $errors = [];
    public $successes = [];
	/**
	 * Option key to hold the settings in database
	 */
	const OPTION_KEY = 'mc_eps_settings';

	/**
	 * Get settings field
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */

    public function plugin_page() {
       
        $data = function_exists('eps_get_settings') ? eps_get_settings() : get_option(self::OPTION_KEY); 
        if ( is_string($data) ) {
            $data = maybe_unserialize($data);
        }
        $data = is_array($data) ? $data : [];

        $values = array(
            'api_base_url'  => !empty($data['api_base_url']) ? $data['api_base_url'] : '29e86e70-0ac6-45eb-ba04-9fcb0aaed12a',
            'module_val'    => !empty($data['module_val']) ? $data['module_val'] : 'Epsdemo@gmail.com',
            'merchent_code' => !empty($data['merchent_code']) ? $data['merchent_code'] : 'FHZxyzeps56789gfhg678ygu876o=',
            'password'      => "",
            'plugin_key'    => !empty($data['plugin_key']) ? $data['plugin_key'] : 'Epsdemo258@',
            'redirect_url'  => !empty($data['redirect_url']) ? $data['redirect_url'] : 'd44e705f-9e3a-41de-98b1-1674631637da',
            'mode'          => $data['mode'] ?? 'sandbox'
        );

        include __DIR__ . '/views/setting.php';
    }

    /**
     * Handle the form
     *
     * @return void
     */
    public function form_handler() {

        $error_msg = 'Invalid credentials !';
        $success_msg = 'Settings saved successfully!';

        if ( ! isset( $_POST['submit_mc_eps_setting'] ) ) {
            return;
        }

        $nonce = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '';
        if ( ! wp_verify_nonce( $nonce, 's_mc_eps_setting' ) ) {
            wp_die( esc_html__( 'Are you cheating?', 'eps' ) );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Are you cheating?', 'eps' ) );
        }

        $module_val    = isset( $_POST['module_val'] ) ? sanitize_text_field( wp_unslash( $_POST['module_val'] ) ) : '';
        $merchent_code = isset( $_POST['merchent_code'] ) ? sanitize_text_field( wp_unslash( $_POST['merchent_code'] ) ) : '';
        $password      = isset( $_POST['password'] ) ? sanitize_text_field( wp_unslash( $_POST['password'] ) ) : null;
        $mode_input    = isset( $_POST['mode'] ) ? sanitize_text_field( wp_unslash( $_POST['mode'] ) ) : 'sandbox';
        $mode          = in_array( $mode_input, [ 'sandbox', 'production' ], true ) ? $mode_input : 'sandbox';

        $data = function_exists('eps_get_settings') ? eps_get_settings() : get_option(self::OPTION_KEY); 
        if ( is_string($data) ) {
            $data = maybe_unserialize($data);
        }
        $data = is_array($data) ? $data : [];
        $plugin_key = $data['plugin_key'] ?? "";

        if ( !empty($password) ) {

            $processor = \MCoder\EPS\Gateway\Processor::get_instance();
            $get_key_response = $processor->get_token( null, $password, $module_val, $merchent_code, $mode );

            if ( $get_key_response && isset($get_key_response['token']) && $get_key_response['token'] != null ) {
                $this->successes['success_message'] = __( 'Settings saved and credentials verified successfully!', 'eps' );
                $plugin_key = $password;
            } else {
                $this->errors['error_message'] = __( 'Unauthorized: Invalid credentials!', 'eps' );
                return;
            }

        } else {
            $this->successes['success_message'] = __( 'Settings saved successfully!', 'eps' );
        }

        $dataArray = array(
            'api_base_url'  => isset( $_POST['api_base_url'] ) ? sanitize_text_field( wp_unslash( $_POST['api_base_url'] ) ) : '',
            'module_val'    => $module_val,
            'merchent_code' => $merchent_code,
            'plugin_key'    => $plugin_key,
            'redirect_url'  => isset( $_POST['redirect_url'] ) ? sanitize_text_field( wp_unslash( $_POST['redirect_url'] ) ) : '',
            'mode'          => $mode
        );

        update_option( self::OPTION_KEY, wp_json_encode( $dataArray ) );
    }
}