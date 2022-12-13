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
            // 'value' => explode(',', $query_vars['event_location']),
            // 'compare' => 'IN',
            'value' => $query_vars['event_location'],
            'compare' => '=',

        );
    }
    // if(!empty($query_vars['filter_type']) && $query_vars['filter_type'] == 'month'){
    //     $request_body =  $query_vars['request_body'];
    // }
    
    if (!empty($query_vars['event_start_date'])) {
        $query['meta_query'][] = array(
            'key' => 'event_start_time',
            'value' => array($query_vars['event_start_date'],$query_vars['event_end_date']),
            'compare'   => 'BETWEEN',
            'type'      => 'DATETIME'

        );
    }
    // if (!empty($query_vars['event_end_date'])) {
    //     $query['meta_query'][] = array(
    //         'key' => 'event_end_time',
    //         'value' => $query_vars['event_end_date'],
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

    $start_date= ''; 
    $end_date = '';

    $my_returned_data = array();
    $my_years = array();
    foreach($gallery_locations_arr as $gallery_location){
        $my_data = array(
            "location"  => $gallery_location,
            "total" => count(get_order_count($gallery_location,$filter_type,$start_date,$end_date)),
    
        
        );
        
        foreach( $request_body as $key=>$single_year){
            $my_data[$single_year['year']] = rand(10,100);

            // code here--------------
            $year   = $single_year['year'];
            $months = explode(',', $single_year['months']); 
            $the_count = 0;

            foreach ( $months as $month ) {
                $start_date         = $year . '-' . $month . '-01 00:00:00';
                $end_date           =  new DateTime($year . '-' . $month . '-01 11:59:59');
                $end_date = $end_date->format('Y-m-t h:i:s');
                
                // $monthly_order_ids  = elux_get_all_event_orders_by_date( $start_date, $end_date, $disallowed_event_types );
                // $yearly_order_ids   = array_merge( $yearly_order_ids, $monthly_order_ids );
                $the_count += count(get_order_count($gallery_location,$filter_type,$start_date,$end_date));
                
            }
            $my_data[$single_year['year']] =  $the_count;
            // $start_date         = $year . '-' . $month . '-01 00:00:00';
            // $end_date           =  new DateTime($year . '-' . $month . '01 11:59:59');
            // $end_date = $end_date->format('Y-m-t h:i:s');
            
            // $my_data[$single_year['year']] = count(get_order_count($gallery_location,$filter_type,$start_date,$end_date));
               
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
    error_log(print_r('start date in query args',1));
    error_log(print_r($start_date,1));
    
    $args = array(
        'limit' => -1,
        
        'event_location' => $gallery_locations,
        'filter_type' => $filter_type,
        // 'request_body' => $request_body,
        'event_start_date' => $start_date,
        'event_end_date' => $end_date,
        'return' => 'ids',
        'meta_query'    => array(
            array(
                'relation' => 'AND',
                array(
                    'key'   => 'event_start_time',
                    'compare' => 'EXISTS'
                )
            )
            
        )
    );
    // $order_data = new WC_Order_Query($args);
    $order_data = wc_get_orders($args);
    return $order_data;

    // new query
    



}