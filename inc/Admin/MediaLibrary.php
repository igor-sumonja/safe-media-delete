<?php
/**
 * Media Library
 *
 * @package SafeMD
 */

namespace SafeMD\Admin;

use SafeMD\DB;

/**
 * Class MediaLibrary - main class for media library.
 */
class MediaLibrary {

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
	 * Run media library class.
	 *
	 * @return void
	 */
	public function run(): void {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueueScripts' ) );

		// add custom text to media details.
		add_action( 'attachment_fields_to_edit', array( $this, 'attachmentFieldsToEdit' ), 10, 2 );

			// add new column to media library table.
		add_filter( 'manage_media_columns', array( $this, 'addAttachedObjectsColumn' ) );
		add_action( 'manage_media_custom_column', array( $this, 'displayAttachedObjects' ), 10, 2 );

		// prevent deletion of media that is attached to a post.
		add_action( 'pre_delete_attachment', array( $this, 'preventDeletionOfAttachedMedia' ), 10, 2 );
	}

	/**
	 *
	 * Enqueue scripts.
	 *
	 * @return void
	 */
	public function enqueueScripts(): void {
		// load the asset file built by webpack from which we can get all the dependencies and version.
		$asset_file = include SAFE_MD_DIR . 'build/index.asset.php';
		wp_enqueue_script( SAFE_MD_SLUG, SAFE_MD_URL . 'build/index.js', $asset_file['dependencies'], $asset_file['version'], true );
		wp_localize_script( SAFE_MD_SLUG, 'ajax_object', array( 'ajax_nonce' => wp_create_nonce( 'wp_rest' ) ) );
	}

	/**
	 * Modify attachment fields in media details.
	 *
	 * @param array    $form_fields Array of fields.
	 * @param \WP_Post $post Post object.
	 *
	 * @return array
	 */
	public function attachmentFieldsToEdit( array $form_fields, \WP_Post $post ): array {
		$attached_objects = $this->db->getAttachedObjects( $post->ID );

		if ( count( $attached_objects['Posts'] ) > 0 ) {
			$html = '';

			foreach ( $attached_objects['Posts'] as $post ) {
				$html .= '<a href="' . esc_url( $post['edit_link'] ) . '">' . esc_html( $post['id'] ) . '</a>, ';
			}

			$form_fields['attached_posts'] = array(
				'label' => __( 'Attached Posts', 'safe-media-delete' ),
				'input' => 'html',
				'html'  => $html,
				'value' => '',
				'helps' => '',
			);
		}

		if ( count( $attached_objects['Terms'] ) > 0 ) {
			$html = '';

			foreach ( $attached_objects['Terms'] as $term ) {
				$html .= '<a href="' . esc_url( $term['edit_link'] ) . '">' . esc_html( $term['id'] ) . '</a>, ';
			}

			$form_fields['attached_terms'] = array(
				'label' => __( 'Attached Terms', 'safe-media-delete' ),
				'input' => 'html',
				'html'  => $html,
				'value' => '',
				'helps' => '',
			);
		}

		return $form_fields;
	}


	/**
	 * Modify media library columns on admin view with new column.
	 *
	 * @param array $columns   Array of columns.
	 *
	 * @return array
	 */
	public function addAttachedObjectsColumn( $columns ) : array {
		$columns['attached_objects'] = __( 'Attached Objects', 'safe-media-delete' );
		return $columns;
	}

	/**
	 * Display the data about the media file in the new column.
	 *
	 * @param string $column_name  Column name.
	 * @param int    $post_id Post ID.
	 *
	 * @return void
	 */
	public function displayAttachedObjects( $column_name, $post_id ): void {
		if ( 'attached_objects' === $column_name ) {
			$attached_objects = $this->db->getAttachedObjects( $post_id );

			if ( $attached_objects ) {
				if ( count( $attached_objects['Posts'] ) > 0 ) {
					echo '<strong>' . esc_html__( 'Posts', 'safe-media-delete' ) . ':</strong> ';
					foreach ( $attached_objects['Posts'] as $post ) {
						echo '<a href="' . esc_url( $post['edit_link'] ) . '">' . esc_html( $post['id'] ) . '</a>, ';
					}
				}
				if ( count( $attached_objects['Terms'] ) > 0 ) {
					echo '<br><strong>' . esc_html__( 'Terms', 'safe-media-delete' ) . ':</strong> ';
					foreach ( $attached_objects['Terms'] as $term ) {
						echo '<a href="' . esc_url( $term['edit_link'] ) . '">' . esc_html( $term['id'] ) . '</a>, ';
					}
				}
			}
		}
	}

	/**
	 * Check if media is attached to a post or term and prevent deletion if it is.
	 *
	 * @param null     $delete Null.
	 * @param \WP_Post $post  Post object.
	 *
	 * @return mixed|null
	 */
	public function preventDeletionOfAttachedMedia( $delete, $post ): mixed {
		$safeToDelete = $this->db->isSafeToDelete( $post->ID );
		if ( ! $safeToDelete ) {
			return wp_die( esc_html__( 'This media is attached to a post or term and cannot be deleted', 'safe-media-delete' ) );
		}
		return $delete;
	}

}
