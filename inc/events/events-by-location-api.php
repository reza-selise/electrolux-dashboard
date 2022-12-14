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

    $start_date= ''; 
    $end_date = '';

    $my_returned_data = array();
    $my_years = array();
    foreach($gallery_locations_arr as $gallery_location){
        $my_data = array(
            "location"  => $gallery_location,
            "total" => 0,//get_order_count($gallery_location,$filter_type,$start_date,$end_date),
    
        
        );
        
        foreach( $request_body as $key=>$single_year){
            // $my_data[$single_year['year']] = rand(10,100);

            // code here--------------
            $year   = $single_year['year'];
            $months = explode(',', $single_year['months']); 
            // $the_count = 0;
            $yearly_order_ids = [];
            
            // error_log(print_r('months', 1));
            // error_log(print_r( $months, 1));
            foreach ( $months as $month ) {
                $start_date         = $year . '-' . $month . '-01 00:00:00';
                $end_date           =  new DateTime($year . '-' . $month . '-01 11:59:59');
                $end_date = $end_date->format('Y-m-t h:i:s');
                  
                $monthly_order_ids  = get_order_count($gallery_location,$filter_type,$start_date,$end_date);
                if(!empty($monthly_order_ids)){
                    array_push( $yearly_order_ids, $monthly_order_ids );
                    
                }
            }
            $my_data[$single_year['year']] =  count($yearly_order_ids); // push to main array
                   
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