<?php
//--- Maruf---

add_action( 'rest_api_init', function () {
    $namespace = 'elux-dashboard/v1';
    register_rest_route($namespace, '/consultations-by-acquisition-type', array(
        'methods' => 'GET',
        'callback' => 'my_awesome_func',
        'permission_callback' => '__return_true',
    ));
});

function handle_custom_query_var($query, $query_vars){
    if (!empty($query_vars['event_location'])) {
      
        $query['meta_query'][] = array(
            'key' => 'event_location',
            'value' => $query_vars['event_location'],
            'compare' => 'IN',

        );
    }
    if (!empty($query_vars['event_start_date'])) {
        $query['meta_query'][] = array(
            'key' => 'start_date',
            'value' => $query_vars['event_start_date'],
            'compare'   => 'BETWEEN',
            'type'      => 'DATETIME'

        );
    }
    return $query;
}
add_filter('woocommerce_order_data_store_cpt_get_orders_query', 'handle_custom_query_var', 10, 2);


function my_awesome_func($request){
    $requests =  $request->get_params();
    $request_years = $request->get_params()['years'];
    $arr_req_years = explode (",", $request_years); 
    $arr_req_months = explode (",", $request->get_params()['months']); 
    $arr_req_categories = null;
    if(isset($request->get_params()['categories']) && !empty($request->get_params()['categories'])){
        $arr_req_categories = explode (",", $request->get_params()['categories']);
    }
    $customer_types = explode (",", $request->get_params()['customer_types']);
    $gallery_locations = explode (",", $request->get_params()['gallery_locations']);
    $device_types = explode (",", $request->get_params()['device_types']);
    $device_categories = explode (",", $request->get_params()['device_categories']);
    $status = explode (",", $request->get_params()['status']);
    $cancellation_type = explode (",", $request->get_params()['cancellation_type']);
    
   
    // meta query
    $args = array(
        'limit' => -1,
        'event_location' => $gallery_locations,
        'event_years' => '2021,2022,2023',
        'event_months' => '01,02,03',
        'event_start_date' => '2022-01-01',
        'event_end_date' => '2022-12-31',
        'return' => 'ids',
    );
    $order_data = wc_get_orders($args);
    // meta query


    return  count($order_data);
    // consultations_by_acquisition_type
    // return array( 'custom' => 'Data' , "request"=> $request->get_params() );
}