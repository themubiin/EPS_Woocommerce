<?php
/**
 * Class Transection
 *
 * @since 1.0.0
 *
 * @package MCoder\EPS\Admin
 *
 * @author Md Mokbul Hossain
 */

namespace MCoder\EPS\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * Class Transection
 */
class Transection {
	/**
	 * Option key to hold the Transection in database
	 */
	const OPTION_KEY = 'mc_eps_transections';

	/**
	 * Get settings field
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */

    public function plugin_page() {
        $eps_plugin_settings = function_exists('eps_get_settings') ? eps_get_settings() : get_option('mc_eps_settings');
        if ( is_string($eps_plugin_settings) ) {
            $eps_plugin_settings = maybe_unserialize($eps_plugin_settings);
        }
        $eps_current_mode = (is_array($eps_plugin_settings) && isset($eps_plugin_settings['mode'])) ? $eps_plugin_settings['mode'] : 'sandbox';

        include __DIR__ . '/views/transection.php';
    }

}