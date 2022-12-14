<?php

if( ! function_exists( 'elux_get_all_valid_event_order_ids_between_date' ) ){
    function elux_get_all_valid_event_order_ids_between_date( $start_date = '', $end_date = '', $disallowed_event_types = array() ){
        global $wpdb;
        $query      = "SELECT DISTINCT $wpdb->postmeta.post_id from $wpdb->postmeta LEFT JOIN $wpdb->posts ON $wpdb->postmeta.post_id = $wpdb->posts.ID WHERE $wpdb->posts.post_type = 'shop_order' AND ( $wpdb->postmeta.meta_key = 'event_start_time' AND ( $wpdb->postmeta.meta_value BETWEEN '%s' AND '%s' ))";
        $response   = $wpdb->get_results( $wpdb->prepare( $query, $start_date, $end_date ) );
        $order_ids  = array_map( "elux_order_id_array_map", $response );
        $valid_event_order_ids = array_filter( $order_ids, function( $order_id ){
            if( 'voucher' !== get_post_meta( $order_id, 'order_service_type', true ) ){
                return true;
            } else {
                return false;
            }
        } );
        return $valid_event_order_ids;
    }
}
// elux_get_all_valid_event_order_ids_between_date( '2022-10-01 00:00:00', '2022-10-31 11:59:59', array( 'giftcard', 'voucher' ) );

if( ! function_exists( 'elux_order_id_array_map' ) ){
    function elux_order_id_array_map( $order ){
        return $order->post_id;
    }
}