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

function my_awesome_func($request){
    $requests =  $request->get_params();
    // consultations_by_acquisition_type
    return array( 'custom' => 'Data' , "request"=> $request->get_params() );
}