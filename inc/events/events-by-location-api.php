<?php
//--- Maruf---

add_action( 'rest_api_init', function () {
    $namespace = 'elux-dashboard/v1';
    register_rest_route($namespace, '/consultations-by-acquisition-type', array(
        'methods' => 'GET',
        'callback' => 'get_event_by_locations',
        'permission_callback' => '__return_true',
    ));
});

function get_event_by_locations($request){
    $request_data =  $request->get_params()['request_data'];
    $filter_type =  $request->get_params()['filter_type'];
    $request_body =  $request->get_params()['request_body'];
    
    $gallery_locations = $request->get_params()['locations'];
    $gallery_locations_arr =  explode(',',$gallery_locations);
    $response               = array(
        "type"  => $request_data
    );

  
    $my_returned_data = array();
    $my_years = array();
    foreach($gallery_locations_arr as $gallery_location){
        $my_data = array(
            "location"  => $gallery_location,
            "total" => 0,//get_order_count($gallery_location,$filter_type,$start_date,$end_date),
    
        
        );
        
        foreach( $request_body as $single_year){
            $year   = $single_year['year'];
            $months = explode(',', $single_year['months']); 
            // $the_count = 0;
            $yearly_order_ids =  array();
            $yearly_order_ids2 =  array();
            foreach ( $months as $month ) {
                $start_date         = $year . '-' . $month . '-01 00:00:00';
                $end_date           =  new DateTime($year . '-' . $month . '-01 11:59:59');
                $end_date = $end_date->format('Y-m-t h:i:s');
                  
                $monthly_order_ids  = get_order_count($gallery_location,$filter_type,$start_date,$end_date);
                if(!empty($monthly_order_ids)){
                    array_push( $yearly_order_ids, $monthly_order_ids );
                    $yearly_order_ids2 = array_merge( $yearly_order_ids2, $monthly_order_ids );
                   
                }
               
            }

            if(!empty($yearly_order_ids) && 'events'== $request_data){
                $my_data[$single_year['year']] =  count($yearly_order_ids2); // push to main array
               
            }
            if(!empty($yearly_order_ids) && 'participants'== $request_data){
                $my_data[$single_year['year']] = event_person_count($yearly_order_ids2);
            }
            error_log(print_r('year order ids -------------- ',1));
            error_log(print_r($yearly_order_ids2,1));
            
        }

      array_push($my_returned_data, $my_data);  
    }
   
   
    $response['locations'] = $my_returned_data;
    
    return rest_ensure_response( array(
        'status_code' => 200,
        'message'     => 'success',
        'data'        => $response,
        ) 
    );

}


function get_order_count($gallery_location,$filter_type,$start_date,$end_date){ 
    global $wpdb;
    // query 1
    $query =  "SELECT DISTINCT $wpdb->postmeta.post_id from $wpdb->postmeta LEFT JOIN $wpdb->posts ON $wpdb->postmeta.post_id = $wpdb->posts.ID
     WHERE $wpdb->posts.post_type = 'shop_order' 
    --  AND ($wpdb->postmeta.meta_key = 'event_location' AND ( $wpdb->postmeta.meta_value = $gallery_location))
     AND ( $wpdb->postmeta.meta_key = 'event_start_time' AND ( $wpdb->postmeta.meta_value BETWEEN '%s' AND '%s' ))
     ";

    $response   = $wpdb->get_results( $wpdb->prepare( $query, $start_date, $end_date ) );
    $order_ids  = array_map( "elux_order_id_array_map", $response );

    $valid_event_order_ids = array_filter( $order_ids, function( $order_id) use ($gallery_location){
        if( 'voucher' !== get_post_meta( $order_id, 'order_service_type', true ) && $gallery_location == get_post_meta( $order_id, 'event_location', true )){
            return true;
        } else {
            return false;
        }
    } );
    return $valid_event_order_ids;
}

// persopn count yearwise
function event_person_count($orders_ids){
    global $woocommerce;
    error_log(print_r('order id in event_person count ',1));
    error_log(print_r($orders_ids,1));
    $yearly_event_participants  = 0;
    
    if(!empty($orders_ids)){
        foreach( $orders_ids as $order_id ){
            $order      = wc_get_order( $order_id );
           

            if($order->get_items() && isset($order->get_items()[0])){

                $event      = $order->get_items()[0];
                // $product_id = (int) $event->get_product_id();
                // $type       = !empty( get_post_meta( $product_id, 'customer_type', true ) ) ? strtolower(get_post_meta( $product_id, 'customer_type', true )) : '';
            
                $participants_qty           = (int) $event->get_quantity();
            }
            else{
                $participants_qty = 1;
            }
           
            $yearly_event_participants += $participants_qty;
        }
    }
    
    error_log(print_r('perticipant yearly_event_participants ',1));
    error_log(print_r($yearly_event_participants,1));
    return $yearly_event_participants;
//     error_log(print_r('order_ids array length ',1));
//     error_log(print_r(count($orders_ids,1)));

//     global $wpdb;
//     $query =  "SELECT * FROM wp_woocommerce_order_items WHERE order_id  IN ($orders_ids)";
//     $response   = $wpdb->get_results($wpdb->prepare( $query));

//    return count($response);
}