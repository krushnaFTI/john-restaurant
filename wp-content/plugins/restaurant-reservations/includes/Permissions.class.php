<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'rtbPermissions' ) ) {
/**
 * Class to handle plugin permissions for Restaurant Reservations
 *
 * @since 2.0.0
 */
class rtbPermissions {

	private $plugin_permissions;
	private $permission_level;

	public function __construct() {
		$this->plugin_permissions = array(
			"styling" => 2,
			"import" => 2,
			"export" => 2,
			"custom_fields" => 2,
			"mailchimp" => 2,
			"templates" => 2,
			"designer" => 2,
			"premium_view_bookings" => 2,
			"premium_seat_restrictions" => 2,
			"payments" => 3,
			"reminders" => 3,
			"premium_table_restrictions" => 3
		);
	}

	public function set_permissions() {
		update_option( "rtb-permission-level", 3);
	}

	public function get_permission_level() {
		$this->permission_level = 3;

		if ( ! $this->permission_level ) { $this->set_permissions(); }
	}

	public function check_permission( $permission_type = '' ) {
		if ( ! $this->permission_level ) { $this->get_permission_level(); }

		return true;
	}

	public function update_permissions() {
		$this->permission_level = 3;
	}
}

}