<?php
/**
 * Class Payment
 *
 *
 * @package MCoder\EPS\API
 */

namespace MCoder\EPS\API;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


use MCoder\EPS\Gateway\Processor;
use WP_Error;
use WP_Http;
use WP_REST_Server;

/**
 * Class Payment
 */
class Payment extends EPSBaseRestController {

	/**
	 * Initialize the class
	 */
	public function __construct() {
		$this->rest_base = 'payment';
	}

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 *
	 * @return void
	 */
	public function register_routes() {

		register_rest_route(
			$this->get_namespace(),
			sprintf( '/%s/create-payment', $this->rest_base ),
			[
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'create_payment' ],
					'permission_callback' => [ $this, 'admin_permissions_check' ],
				],
				'schema' => [ $this, 'get_item_schema' ],
			]
		);

	}


	/**
	 * Create Payment data.
	 * Returning with payment data and request header.
	 *
	 * @param object $request Request Object.
	 *
	 *
	 * @return WP_Error|\WP_REST_Response
	 */
	public function create_payment( $request ) {

		$get_amount      = $request->get_param( 'amount' );
		$mc_processor    = Processor::get_instance();
		$amount          = $get_amount ? $get_amount : wp_rand( 10, 100 );
		$invoice_id      = sprintf( 'TBP%s', str_pad( wp_rand( 10, 999 ), 5, 0, STR_PAD_LEFT ) );
		$create_payment  = $mc_processor->create_payment( (float) $amount, $invoice_id );

		if ( is_wp_error( $create_payment ) ) {
			return new WP_Error(
				'mc_eps_rest_api_payment_create_payment_error',
				esc_html( $create_payment->get_error_message() ),
				[ 'status' => WP_Http::BAD_REQUEST ]
			);
		}

		$response = [
			'title'          => __( 'Create Payment', 'eps' ),
			'data'           => $create_payment,
			'invoice_id'     => $invoice_id,
			'amount'         => $amount,
		];

		return rest_ensure_response( $response );
	}


	/**
	 * Registering single route which will have a id as a argument.
	 *
	 * @param string $path            Route Path.
	 * @param array  $callback_method Callback function to serve.
	 *
	 *
	 * @return void
	 */
	private function register_single_route( $path, array $callback_method ) {
		register_rest_route(
			$this->get_namespace(),
			sprintf( '/%s/%s', $this->rest_base, $path ),
			[
				'args'   => [
					'id' => [
						'description' => __( 'Unique identifier for the payment.', 'eps' ),
						'type'        => 'string',
					],
				],
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => $callback_method,
					'permission_callback' => [ $this, 'admin_permissions_check' ],
					'args'                => $this->get_collection_params(),
				],
				'schema' => [ $this, 'get_item_schema' ],
			]
		);
	}
}
