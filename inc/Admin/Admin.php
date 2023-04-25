<?php
/**
 * Admin class.
 *
 * @package SafeMD\Admin
 */

namespace SafeMD\Admin;

use SafeMD\DB;

/**
 * Class Admin - main class for admin.
 */
class Admin {


	/**
	 * MediaLibrary instance.
	 *
	 * @var MediaLibrary Media library instance.
	 */
	private MediaLibrary $mediaLibrary;

	/**
	 *  DB instance.
	 *
	 * @var DB
	 */
	private DB $db;

	/**
	 * Admin constructor.
	 *
	 * @param DB $db DB instance.
	 */
	public function __construct( DB $db ) {
		$this->db           = $db;
		$this->mediaLibrary = new MediaLibrary( $this->db );
	}

	/**
	 * Run admin class.
	 *
	 * @return void
	 */
	public function run(): void {

		// run media library class.
		$this->mediaLibrary->run();
	}









}
