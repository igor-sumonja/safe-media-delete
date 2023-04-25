<?php
/**
 * Safe Media Delete
 *
 * @package SafeMD
 */

namespace SafeMD;

use SafeMD\Admin\Admin;
use SafeMD\Admin\Fields;

/**
 * Class SafeMediaDelete
 *
 * @package SafeMD
 */
class SafeMediaDelete {


	/**
	 * Admin instance.
	 *
	 * @var Admin
	 */
	public Admin $admin;

	/**
	 * RestAPI instance.
	 *
	 * @var RestAPI
	 */
	private RestAPI $rest;

	/**
	 * Fields instance.
	 *
	 * @var Fields $fields
	 */

	/**
	 * Fields instance.
	 *
	 * @var Fields $fields
	 */
	private Fields $fields;

	/**
	 * DB instance.
	 *
	 * @var DB $db
	 */
	private DB $db;


	/**
	 * SafeMediaDelete constructor.
	 * Initialize all classes.
	 */
	public function __construct() {
		$this->fields = new Fields();
		$this->db     = new DB( $this->fields );
		$this->admin  = new Admin( $this->db );
		$this->rest   = new RestAPI( $this->db );
	}//end __construct()


	/**
	 * Run plugin.
	 *
	 * @return void
	 */
	public function run(): void {
		$this->admin->run();
		$this->rest->run();
	}//end run()


}//end class
