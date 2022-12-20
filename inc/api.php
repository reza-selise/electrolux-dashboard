<?php

namespace Elux_DB;

defined('ABSPATH') || exit;

class Elux_DB_API {

	public function init() {
		require_once ELUX_DB_PLUGIN_DIR . 'inc/events/events-by-year-api.php';
		require_once ELUX_DB_PLUGIN_DIR . 'inc/events/events-by-month-api.php';
		require_once ELUX_DB_PLUGIN_DIR . 'inc/events/events-by-location-api.php';
		require_once ELUX_DB_PLUGIN_DIR . 'inc/events/generic-comments-api.php';
		require_once ELUX_DB_PLUGIN_DIR . 'inc/events/section-comment-api.php';
		require_once ELUX_DB_PLUGIN_DIR . 'inc/events/events-by-category-api.php';
		require_once ELUX_DB_PLUGIN_DIR . 'inc/events/events-by-cooking-course-type-api.php';
		require_once ELUX_DB_PLUGIN_DIR . 'inc/events/events-by-sale-person-api.php';
		require_once ELUX_DB_PLUGIN_DIR . 'inc/events/events-by-cancellation-api.php';

        // // Initialize classes.
        // Appointments\Api_Appointment::instance();
        // Staffs\Api_Staff::instance();
        // Services\Api_Service::instance();
        // Bookings\Api_Booking::instance();
        // Staffs\Hooks::instance()->init();
    }

}