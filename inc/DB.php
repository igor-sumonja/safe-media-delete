<?php
/**
 * Safe Media Delete
 *
 * @package SafeMD
 */

namespace SafeMD;

use SafeMD\Admin\Fields;

/**
 * Class DB - main class for database.
 */
class DB {

	/**
	 * Fields instance.
	 *
	 * @var Fields
	 */
	private Fields $fields;

	/**
	 * DB constructor.
	 *
	 * @param Fields $fields Fields instance.
	 */
	public function __construct( Fields $fields ) {
		$this->fields = $fields;
	}


	/**
	 * Get all post IDs where media is used.
	 *
	 * @param int $mediaID Media ID.
	 *
	 * @return array|bool
	 */
	private function getFeaturedImagePostIds( $mediaID ): array|bool {
		$posts = get_posts(
			array(
				'post_type'      => 'any',
				'meta_key'       => '_thumbnail_id',
				'meta_value'     => $mediaID,
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);

		return ( $posts ) ?: array();
	}

	/**
	 * Get all post IDs where media is used in post content.
	 *
	 * @param int $mediaId  Media ID.
	 *
	 * @return array|bool
	 */
	private function getPostContentPostIds( $mediaId ): array|bool {

		// get all post where media is used in post content.
		$attached_file = get_post_meta( $mediaId, '_wp_attached_file', true );
		$filename      = pathinfo( $attached_file, PATHINFO_FILENAME );

		// get all posts where $filename is used in post content.
		$posts = get_posts(
			array(
				'post_type'      => 'any',
				's'              => $filename,
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);

		return ( $posts ) ?: array();
	}

	/**
	 * Get all terms IDs where media is used.
	 *
	 * @param int $mediaId Media ID.
	 *
	 * @return array|\WP_Error|bool|string
	 */
	private function getTermsWithMediaID( $mediaId ): array|\WP_Error|bool|string {
		$metaKey = $this->fields->getTaxMetaKey() . '_id';

		$terms = get_terms(
			array(
				'taxonomy'   => 'category',
				'fields'     => 'ids', // return only term IDs.
				'hide_empty' => false,
				'meta_query' => array(
					array(
						'key'     => $metaKey,
						'value'   => $mediaId,
						'compare' => 'LIKE',
					),
				),
			)
		);

		return ( $terms ) ?: array();
	}



	/**
	 * Return array with 'Posts' and 'Terms' keys.
	 *
	 * @param int $mediaId Media ID.
	 *
	 * @return array
	 */
	public function getAttachedObjects( $mediaId ): array {
		$attachedObjects = array();

		// check is media used as featured image somewhere and get the all post IDs where it is used.
		$featuredImagePostIds = $this->getFeaturedImagePostIds( $mediaId );

		// check is media used in post content and get the all post IDs where it is used.
		$postContentPostIds = $this->getPostContentPostIds( $mediaId );

		$posts                    = array_unique( array_merge( $featuredImagePostIds, $postContentPostIds ) );
		$attachedObjects['Posts'] = array();
		$attachedObjects['Terms'] = array();

		if ( count( $posts ) > 0 ) {
			foreach ( $posts as $post ) {
				$attachedObjects['Posts'][] = array(
					'id'        => $post,
					'edit_link' => get_edit_post_link( $post ),
				);
			}
		}

		$attachedObjectsTerms = $this->getTermsWithMediaID( $mediaId );
		if ( count( $attachedObjectsTerms ) > 0 ) {
			foreach ( $attachedObjectsTerms as $term ) {
				$attachedObjects['Terms'][] = array(
					'id'        => $term,
					'edit_link' => get_edit_term_link( $term ),
				);
			}
		}

		return $attachedObjects;
	}

	/**
	 * Check if media is safe to delete.
	 *
	 * @param int $mediaId Media ID.
	 *
	 * @return bool
	 */
	public function isSafeToDelete( $mediaId ): bool {
		$attachedObjects = $this->getAttachedObjects( $mediaId );
		return empty( $attachedObjects['Posts'] ) && empty( $attachedObjects['Terms'] );
	}

}
