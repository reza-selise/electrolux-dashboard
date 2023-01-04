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
    return 'hello world';

}
