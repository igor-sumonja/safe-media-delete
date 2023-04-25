<?php
/**
 * Safe Media Delete
 *
 * @package SafeMD
 */

namespace SafeMD\Admin;

/**
 * Class Fields - main class for custom taxonomies fields.
 */
class Fields {


	/**
	 * Taxonomy meta key.
	 *
	 * @var string
	 */
	private string $tax_meta_key = SAFE_MD_SLUG . '-tax-image';

	/**
	 * Fields constructor.
	 */
	public function __construct() {
		add_action( 'cmb2_admin_init', array( $this, 'addTaxFields' ) );
	}


	/**
	 * Handle the logic for custom taxonomies fields
	 *
	 * @return void
	 */
	public function addTaxFields(): void {
		$cmb = new_cmb2_box(
			array(
				'id'           => $this->tax_meta_key,
				'title'        => __( 'Image', 'safe-media-delete' ),
				'object_types' => array( 'term' ),
				'taxonomies'   => array( 'category', 'post_tag' ),
				'context'      => 'side',
				'priority'     => 'high',
				'show_names'   => false,
			)
		);

		$cmb->add_field(
			array(
				'id'           => $this->tax_meta_key,
				'name'         => __( 'Image', 'safe-media-delete' ),
				'desc'         => __( 'Upload or select an image for this term.', 'safe-media-delete' ),
				'type'         => 'file',
				'options'      => array(
					'url' => false,
				),
				'text'         => array(
					'add_upload_file_text' => __( 'Upload Image', 'safe-media-delete' ),
				),
				'preview_size' => 'thumbnail',
			)
		);
	}

	/**
	 * Return taxonomy meta key.
	 *
	 * @return string
	 */
	public function getTaxMetaKey(): string {
		return $this->tax_meta_key;
	}


}
