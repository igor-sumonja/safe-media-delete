<?php
/**
 * REST API
 *
 * @package SafeMD
 */

namespace SafeMD;

use WP_REST_Request;
use WP_REST_Response;

/**
 * Class RestAPI - main class for REST API.
 */
class RestAPI {


	/**
	 * Namespace for REST route.
	 *
	 * @var string
	 */
	private string $namespace = 'assignment/v1';

	/**
	 * DB instance.
	 *
	 * @var DB
	 */
	private DB $db;


	/**
	 * Constructor.
	 *
	 * @param DB $db DB instance.
	 */
	public function __construct( DB $db ) {
		$this->db = $db;
	}


	/**
	 * Run plugin.
	 *
	 * @return void
	 */
	public function run(): void {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}


	/**
	 * Register routes.
	 *
	 * @return void
	 */
	public function register_routes(): void {
		register_rest_route(
			$this->namespace,
			'/image/(?P<id>\d+)',
			array(
				'methods'             => 'GET',
				'callback'            => array(
					$this,
					'get_image_details',
				),
				'permission_callback' => function () {
					return current_user_can( 'edit_others_posts' );
				},
				'args'                => array(
					'id' => array(
						'validate_callback' => function ( $param ) {
							return is_numeric( $param );
						},
					),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/image/(?P<id>\d+)/delete',
			array(
				'methods'             => 'DELETE',
				'callback'            => array(
					$this,
					'delete_image',
				),
				'permission_callback' => function () {
					return current_user_can( 'edit_others_posts' );
				},
				'args'                => array(
					'id' => array(
						'validate_callback' => function ( $param ) {
							return is_numeric( $param );
						},
					),
				),
			)
		);
	}


	/**
	 * Get image details.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response
	 */
	public function get_image_details( WP_REST_Request $request ): WP_REST_Response {
		$image_id = $request->get_param( 'id' );
		$image    = wp_prepare_attachment_for_js( $image_id );

		if ( ! $image ) {
			return new WP_REST_Response( array( 'error' => 'Image not found' ), 404 );
		}

		$response = array(
			'ID'               => $image['id'],
			'Date'             => $image['dateFormatted'],
			'Slug'             => get_post_field( 'post_name', $image_id ),
			'Type'             => $image['subtype'],
			'Link'             => $image['link'],
			'Alt_text'         => $image['alt'],
			'Attached_Objects' => wp_json_encode( $this->db->getAttachedObjects( $image_id ) ),
		);

		return new WP_REST_Response( $response, 200 );
	}


	/**
	 * Delete image if it is not attached to any post.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response
	 */
	public function delete_image( WP_REST_Request $request ): WP_REST_Response {
		$image_id = $request->get_param( 'id' );

		$deleted = wp_delete_attachment( $image_id, true );

		if ( ! $deleted ) {
			return new WP_REST_Response( array( 'error' => 'Failed to delete image.' ), 500 );
		}

		return new WP_REST_Response( array( 'success' => 'Image deleted successfully.' ), 200 );
	}


}
