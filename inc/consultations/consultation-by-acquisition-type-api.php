<?php
//---created by:  Maruf---

add_action( 'rest_api_init', function () {
    $namespace = 'elux-dashboard/v1';
    register_rest_route($namespace, '/consultation-by-acquisition-type', array(
        'methods' => 'GET',
        'callback' => 'get_consultation_by_acquisition_type',
        'permission_callback' => '__return_true',
    ));
});

function get_consultation_by_acquisition_type($request){
    $consultation_type = ! empty( $request->get_params()['categories'] ) ? $request->get_params()['categories'] : ''; // all | onsite-consultation | live-consultation
    $consultation_types             = explode( ',', $consultation_type );
   
    $customer_type          = $request->get_params()['customer_type']; // b2b | b2c | electrolux_internal | all etc. [customer_type on product meta]
    $customer_types       = explode( ',', $customer_type ); 
   
    $gallery_locations = $request->get_params()['locations'];
    $gallery_locations =  explode(',',$gallery_locations);
    
    $device_type = $request->get_params()['device_type'];
    $device_types =  explode(',',$device_type);
    
    $device_category = $request->get_params()['device_category'];
    $device_categories =  explode(',',$device_category);
    
    $event_status = $request->get_params()['event_status'];
    $event_status =  explode(',',$event_status); // "reserved,planned,took_place", // all
    
    $cancelation_type = $request->get_params()['cancelation_type'];
    $cancelation_types =  explode(',',$cancelation_type); //  "by_customer,by_electrolux",// all

    $filter_type =  $request->get_params()['filter_type']; // months, years, custom_date_range,custom_time_frame

    $request_body =  $request->get_params()['request_body'];
    if(!empty($request_body) && 'string' == gettype($request_body)){
        $request_body = json_decode($request->get_params()['request_body'],true);
    }

    $return_data = [];
    // filter type months and year
    if($filter_type == 'months' || $filter_type == 'years'){
        if($request_body){
            foreach($request_body as $year_month){
                $year   = $year_month['year'];
                $months = explode(',', $year_month['months']); 
                // new code start
                $yearly_order_data =  array();
                foreach ( $months as $month ) {
                    $start_date         = $year . '-' . $month . '-01 00:00:00';
                    $end_date           =  new DateTime($year . '-' . $month . '-01 11:59:59');
                    $end_date = $end_date->format('Y-m-t h:i:s');
                        
                    $monthly_order_data  = find_consultation_orders($consultation_types,$customer_types, $gallery_locations, $device_types, $device_categories, $event_status, $cancelation_types, $start_date,$end_date);
                    if(!empty($monthly_order_data)){
                        $yearly_order_data = array_merge( $yearly_order_data, $monthly_order_data );
                    }
                }
                $data = array();
                $data['year'] = $year;
                $get_booking_formated_data = get_booking_formated_data($yearly_order_data);
                $data = array_merge( $data, $get_booking_formated_data );
                
                array_push($return_data, $data);
            }
        }
    }
    // filter_type custom_date_range
    if($filter_type == 'custom_date_range' || $filter_type == 'custom_time_frame'){
        $return_data = [];
    }
    // return $return_data;
    return rest_ensure_response( array(
        'status_code' => 200,
        'message'     => 'success',
        'data'        => $return_data,
        ) 
    );
}

function find_consultation_orders($consultation_types,$customer_types, $gallery_locations, $device_types, $device_categories, $event_status, $cancelation_types, $start_date,$end_date){
   
    //    $consultation_types = onsite-consultation | live-consultation;
    global $wpdb;
    $query =  "SELECT DISTINCT $wpdb->postmeta.post_id from $wpdb->postmeta LEFT JOIN $wpdb->posts ON $wpdb->postmeta.post_id = $wpdb->posts.ID
     WHERE $wpdb->posts.post_type = 'shop_order' 
     AND ( $wpdb->postmeta.meta_key = 'event_start_time' AND ( $wpdb->postmeta.meta_value BETWEEN '%s' AND '%s' ))
     ";

    $response   = $wpdb->get_results( $wpdb->prepare( $query, $start_date, $end_date ) );
    $order_ids  = array_map( "elux_order_id_array_map", $response );
    
    // consultation_types
    $valid_event_order_ids = array_filter( $order_ids, function( $order_id) use ($consultation_types){
        if(in_array( get_post_meta( $order_id, 'order_service_type', true ), $consultation_types)){
            return true;
        } else {
            return false;
        }
    } );

    //-------- gallery locations
    $valid_event_order_ids = array_filter( $order_ids, function( $order_id) use ($gallery_locations){
        if(in_array( get_post_meta( $order_id, 'event_location', true ), $gallery_locations)){
            return true;
        } else {
            return false;
        }
    } );
    //------- cancelation_types
    $valid_event_order_ids = array_filter( $order_ids, function( $order_id) use ($cancelation_types){
        if(in_array( get_post_meta( $order_id, 'order_cancelled_by', true ), $cancelation_types)){
            return true;
        } else {
            return false;
        }
    } );



    return $valid_event_order_ids;

  
    // return formated data
    
}

function get_booking_formated_data($order_ids){
    $data = array();
    $data['manual_booking'] = 0;
    $data['walk_in'] = 0;
    $data['booked_online'] = 0;
    $data['total'] = 0;
   
    // ----------------------------------------------------------------
   
   
    foreach($order_ids as $order_id){
        $booking_type = get_post_meta( $order_id, 'consultation_booking_type', true );
        
        if($booking_type == 'Walk-in'){
            $data['walk_in'] += 1;
        }
        elseif($booking_type == 'Booked manually'){
            $data['manual_booking'] += 1;
        }
        elseif($booking_type == 'Customer Booking'){
            $data['booked_online'] += 1;
        }
        else {
            
        }
    }
    $data['total'] =  $data['manual_booking']+$data['walk_in']+$data['booked_online'];
    return $data;
}