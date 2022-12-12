<?php

// var_dump('loaded');
add_action( 'rest_api_init', function () {
    $namespace = 'elux-dashboard/v1';
    register_rest_route( $namespace, '/events-by-year', [
        'methods'             => 'GET',
        'callback'            => 'elux_get_events_by_year',
        'permission_callback' => '__return_true',
    ] );
} );

if( ! function_exists( 'elux_get_events_by_year' ) ){
    
    function elux_get_events_by_year( $request ) {

        $disallowed_event_types = array( 'giftcard', 'voucher' );
        $allowed_request_data   = array( 'events', 'participants' );
        $allowed_filter_types   = array( 'months', 'years', 'custom_date_range', 'custom_time_frame' );
        $filter_type            = $request->get_params()['filter_type'];
        $request_data           = $request->get_params()['request_data']; // can be events or participants.
        $request_body           = $request->get_params()['request_body'];
        $response               = array(
            "type"  => $request_data
        );

        if( ! in_array($filter_type, $allowed_filter_types) || ! in_array($request_data, $allowed_request_data) || ! is_array( $request_body ) || empty( $request_body )){
            return rest_ensure_response( array(
                'status_code' => 403,
                'message'     => 'failure',
                'data'        => array(),
            ) );
        }

        switch( $filter_type ){
            case 'custom_time_frame':
            case 'months':
            case 'years':
                $all_yearly_data = array();
                
                foreach( $request_body as $single_year ){
                    $year   = $single_year->year;
                    $months = explode(',', $single_year->months);
                    
                    $yearly_order_ids           = array();
                    $yearly_event_participants  = 0;

                    foreach ( $months as $month ) {
                        $start_date         = $year . '-' . $month . '-01 00:00:00' ;
                        $end_date           = $year . '-' . $month . '-31 11:59:59' ;
                        $monthly_order_ids  = elux_get_all_event_orders_by_date( $start_date, $end_date, $disallowed_event_types );
                        $yearly_order_ids   = array_merge( $yearly_order_ids, $monthly_order_ids );
                    }

                    if( 'events' === $request_data ){
                        $yearly_data = array(
                            "year"  => $year,
                            "elux"  => "400000",
                            "b2b"   => "450000",
                            "b2c"   => "750000",
                            "total" => count( $yearly_order_ids )
                        );
                        array_push( $all_yearly_data, $yearly_data );
                    }

                    if( 'participants' === $request_data ){
                        foreach( $yearly_order_ids as $order_id ){
                            $order = wc_get_order( $order_id );
                            $event = $order->get_items()[0];

                            $participants_qty           = (int) $event->get_quantity();
                            $yearly_event_participants += $participants_qty;
                        }
                        $yearly_data = array(
                            "year"  => $year,
                            "elux"  => "400000",
                            "b2b"   => "450000",
                            "b2c"   => "750000",
                            "total" => $yearly_event_participants
                        );
                        array_push( $all_yearly_data, $yearly_data );
                    }

                }

                $response['years'] = $all_yearly_data;
                break;
            case 'custom_date_range':
            default: 
                return rest_ensure_response( array(
                    'status_code' => 403,
                    'message'     => 'failure',
                    'data'        => array(),
                    ) 
                );

        }

        return rest_ensure_response( array(
            'status_code' => 200,
            'message'     => 'success',
            'data'        => $response,
            ) 
        );
    



        ###### Let's create the logic
        $requests           = $request->get_params();
        $request_years      = $request->get_params()['years'];
        $arr_req_years      = explode( ",", $request_years );
        $arr_req_months     = explode( ",", $request->get_params()['months'] );
        $arr_req_categories = null;
    
        if ( isset( $request->get_params()['categories'] ) && !empty( $request->get_params()['categories'] ) ) {
            $arr_req_categories = explode( ",", $request->get_params()['categories'] );
        }
    
        $customer_types    = explode( ",", $request->get_params()['customer_types'] );
        $gallery_locations = explode( ",", $request->get_params()['gallery_locations'] );
        $device_types      = explode( ",", $request->get_params()['device_types'] );
        $device_categories = explode( ",", $request->get_params()['device_categories'] );
        $status            = explode( ",", $request->get_params()['status'] );
        $cancellation_type = explode( ",", $request->get_params()['cancellation_type'] );
    
        // meta query
        $args = [
            'limit'            => -1,
            'event_location'   => $gallery_locations,
            'event_years'      => '2021,2022,2023',
            'event_months'     => '01,02,03',
            'event_start_date' => '2022-01-01',
            'event_end_date'   => '2022-12-31',
            'return'           => 'ids',
        ];
        $order_data = wc_get_orders( $args );
        // meta query
    
        return count( $order_data );
    
        // consultations_by_acquisition_type
        // return array( 'custom' => 'Data' , "request"=> $request->get_params() );
    }

}

function elux_get_all_event_orders_by_date( $start_date, $end_date, $disallowed_event_types = array() ){
    global $wpdb;
    $query      = "SELECT DISTINCT $wpdb->postmeta.post_id from $wpdb->postmeta LEFT JOIN $wpdb->posts ON $wpdb->postmeta.post_id = $wpdb->posts.ID WHERE $wpdb->posts.post_type = 'shop_order' AND ( $wpdb->postmeta.meta_key = 'order_service_type' AND $wpdb->postmeta.meta_value NOT IN('giftcard','voucher') )";
    $response   = $wpdb->get_results( $wpdb->prepare( $query, $start_date, $end_date ) );
    $order_ids  = array_map( "elux_order_id_array_map", $response );
    return $order_ids;
    // AND ( $wpdb->postmeta.meta_key = 'event_start_time' AND ( $wpdb->postmeta.meta_value BETWEEN '%s' AND '%s' ))             
    echo '<pre>';
    var_dump( $order_ids ); die();
    var_dump( $response ); die();
    var_dump( $wpdb->prepare( $query, $start_date, $end_date ) ); die();
}
elux_get_all_event_orders_by_date( '2022-08-01 00:00:00', '2022-08-31 11:59:59', array( 'giftcard', 'voucher' ) );

function elux_order_id_array_map( $order ){
    return $order->post_id;
}
