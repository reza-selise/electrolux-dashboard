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

function handle_custom_query_var($query, $query_vars){
    if (!empty($query_vars['event_location'])) {
     
        $query['meta_query'][] = array(
            'key' => 'event_location',
            'value' => explode(',', $query_vars['event_location']),
            'compare' => 'IN',

        );
    }
    if(!empty($query_vars['filter_type']) && $query_vars['filter_type'] == 'month'){
        $request_body =  $query_vars['request_body'];


    }
//    die($query_vars['event_location']);
  
    // if (!empty($query_vars['event_start_date'])) {
    //     $query['meta_query'][] = array(
    //         'key' => 'start_date',
    //         'value' => $query_vars['event_start_date'],
    //         'compare'   => 'BETWEEN',
    //         'type'      => 'DATETIME'

    //     );
    // }
    return $query;
}
add_filter('woocommerce_order_data_store_cpt_get_orders_query', 'handle_custom_query_var', 10, 2);


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
            "total" => count(get_order_count($gallery_location,$filter_type,$start_date,$end_date)),
    
        
        );
        
        foreach( $request_body as $key=>$single_year){
            $my_data[$single_year['year']] = rand(10,100);
               
        }

      array_push($my_returned_data, $my_data);  
    }
   
    // foreach( $request_body as $single_year){
    //     $all_locations_data = array();
    //     $year   = $single_year->year;
    //     $months = explode(',', $single_year->months);
    //     $yearly_order_ids           = array();
    //     $yearly_event_participants  = 0;
        
    //     foreach ( $months as $month ) {
    //         $start_date         = $year . '-' . $month . '-01 00:00:00' ;
    //         $end_date           = $year . '-' . $month . '-31 11:59:59' ;
    //         $monthly_order_ids  = get_order_count($gallery_location,$filter_type,$start_date,$end_date);
    //         $yearly_order_ids   = array_merge( $yearly_order_ids, $monthly_order_ids );
    //     }
        
    //     if( 'events' === $request_data ){
    //         $locations_data = array(
    //             "location"  => $gallery_locations[0],
    //             "2022"  => "400000",
    //             "2021"   => "450000",
    //             "2023"   => "750000",
    //             "total" => count( $yearly_order_ids )
    //         );
    //         array_push( $all_locations_data, $locations_data );
    //     }

    // }
    $response['locations'] = $my_returned_data;
    
    return rest_ensure_response( array(
        'status_code' => 200,
        'message'     => 'success',
        'data'        => $response,
        ) 
    );

   
    // return $request_body;
    // return get_order_count($gallery_location,$filter_type,$start_date,$end_date );
    // consultations_by_acquisition_type
    // return array( 'custom' => 'Data' , "request"=> $request->get_params() );
}


function get_order_count($gallery_location,$filter_type,$start_date,$end_date){
    $args = array(
        'limit' => -1,
        'event_location' => $gallery_locations,
        'filter_type' => $filter_type,
        // 'request_body' => $request_body,
        'event_start_date' => $start_date,
        'event_end_date' => $end_date,
        'return' => 'ids'
    );
    $order_data = wc_get_orders($args);
    return $order_data;
}