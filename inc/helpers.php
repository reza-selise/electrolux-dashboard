<?php

if( ! function_exists( 'elux_get_all_valid_event_order_ids_between_date' ) ){
    function elux_get_all_valid_event_order_ids_between_date( $start_date = '', $end_date = '', $disallowed_event_types = array() ){
        global $wpdb;
        $query      = "SELECT DISTINCT $wpdb->postmeta.post_id from $wpdb->postmeta LEFT JOIN $wpdb->posts ON $wpdb->postmeta.post_id = $wpdb->posts.ID WHERE $wpdb->posts.post_type = 'shop_order' AND ( $wpdb->postmeta.meta_key = 'event_start_time' AND ( $wpdb->postmeta.meta_value BETWEEN '%s' AND '%s' ))";
        $response   = $wpdb->get_results( $wpdb->prepare( $query, $start_date, $end_date ) );
        $order_ids  = array_map( "elux_order_id_array_map", $response );
        $valid_event_order_ids = array_filter( $order_ids, "elux_filter_valid_events" );
        return $valid_event_order_ids;
    }
}
// elux_get_all_valid_event_order_ids_between_date( '2022-10-01 00:00:00', '2022-10-31 11:59:59', array( 'giftcard', 'voucher' ) );

if( ! function_exists( 'elux_order_id_array_map' ) ){
    function elux_order_id_array_map( $order ){
        return $order->post_id;
    }
}

if( !function_exists( 'elux_filter_valid_events' ) ){
    function elux_filter_valid_events( $order_id ){
        if( 'voucher' !== get_post_meta( $order_id, 'order_service_type', true ) ){
            return true;
        } else {
            return false;
        }
    }
}


if( ! function_exists( 'elux_prepare_order_ids_by_location_filter' ) ){

    function elux_prepare_order_ids_by_location_filter( $order_ids, $locations = array() ){
        /* If, user requested for specific location data
        *  and the current order is not from that location,
        *  then skip to the next iteration.
        *
        *  Else, proceed as usual.
        */
        if( is_array( $locations ) && ! empty( $locations ) ){
            $valid_order_ids = array_filter( $order_ids, function( $order_id ) use( $locations ) {
                $event_location = ! empty( get_post_meta( $order_id, 'event_location', true ) ) ? get_post_meta( $order_id, 'event_location', true ) : 0;
                if( ! in_array( $event_location, $locations ) ){
                    return false;
                }
                return true;
            });
            $order_ids = $valid_order_ids;
        }
    
        return $order_ids;
    }
    
}

if( ! function_exists( 'elux_prepare_category_ids_with_localization' ) ){

    function elux_prepare_category_ids_with_localization( $categories = array() ) {

        if( is_array( $categories ) && !empty( $categories )) {
            //15,47,104
            $all_categories     = [];
            $languages          = array( 'de_CH' , 'fr_FR', 'it_IT' );
            $total_languages    = count( $languages );
    
            foreach( $categories as $category_id ){
                for( $i = 0; $i < $total_languages; $i++ ){
                    $term_id = pll_get_term( $category_id, $languages[$i] );
        
                    if( intval( $term_id ) && ! in_array( $term_id, $all_categories )){
                        array_push( $all_categories, $term_id );
                    }
                }
            }
            $categories = $all_categories;
        }
    
        return $categories;
    }
    
}

if( ! function_exists( 'product_has_sales_person' ) ){
    
    function product_has_sales_person( $product_id, $sales_person_ids = array() ){
        
        if( empty( $sales_person_ids ) ){
            return true;
        }
        
        $sales_person = array();
        $sales_person[] = get_post_meta( $product_id, 'salesperson-1', true );
        $sales_person[] = get_post_meta( $product_id, 'salesperson-2', true );
        $sales_person[] = get_post_meta( $product_id, 'salesperson-3', true );
    
        if( empty( $sales_person ) || empty( array_intersect( $sales_person, $sales_person_ids ) ) ){
            return false;
        }
    
        return true;
    }

}
