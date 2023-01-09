<?php

namespace Elux_DB;

defined('ABSPATH') || exit;

class Elux_DB_API {

	public function init() {
		require_once ELUX_DB_PLUGIN_DIR . 'inc/events/events-by-year-api.php';
		require_once ELUX_DB_PLUGIN_DIR . 'inc/events/events-by-month-api.php';
		require_once ELUX_DB_PLUGIN_DIR . 'inc/events/events-by-status-api.php';
		require_once ELUX_DB_PLUGIN_DIR . 'inc/events/events-by-location-api.php';
		require_once ELUX_DB_PLUGIN_DIR . 'inc/events/generic-comments-api.php';
		require_once ELUX_DB_PLUGIN_DIR . 'inc/events/section-comment-api.php';
		require_once ELUX_DB_PLUGIN_DIR . 'inc/events/events-by-category-api.php';
		require_once ELUX_DB_PLUGIN_DIR . 'inc/events/events-by-cooking-course-type-api.php';
		require_once ELUX_DB_PLUGIN_DIR . 'inc/events/events-by-sale-person-api.php';
		require_once ELUX_DB_PLUGIN_DIR . 'inc/events/events-by-cancellation-api.php';
		require_once ELUX_DB_PLUGIN_DIR . 'inc/events/events-generic-filter-data-api.php';
		//consultations
		require_once ELUX_DB_PLUGIN_DIR . 'inc/consultations/consultation-by-acquisition-type-api.php';
		require_once ELUX_DB_PLUGIN_DIR . 'inc/consultations/consultation-by-year-api.php';
		
		// Home consultation start 
		require_once ELUX_DB_PLUGIN_DIR . 'inc/home-consultation/overview-of-home-consultations-by-month-api.php';
    }

}