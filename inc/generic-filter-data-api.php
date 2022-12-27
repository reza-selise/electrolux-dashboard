<?php

add_action( 'rest_api_init', function () {
    $namespace = 'elux-dashboard/v1';
    register_rest_route($namespace, '/generic-filter-data', array(
        'methods' => 'GET',
        'callback' => 'get_event_by_locations',
        'permission_callback' => '__return_true',
    ));
});

function get_generic_filter_data($request){
    $return_data = array(
        "customer_types" => [
            "B2B",
            "Elux",
            "B2C"
        ],
        "locations"=> "128,125,132,155",
        "categories"=> [
            "cooking-class",
            "steamdemo"
        ],
        "leads"=> [
            "fb",
            "walking"
        ],
        "sales_emp" => [
            "Mr. Maruf",
            "Abul Hasan"
        ],
        "data_types" => [
            "abcd",
            "bcdef"
        ],
        "event_status" => [
            "cancel_by_admin",
            "cancel_by_customer"
        ]
    );
    // product categories
    $cat_args = array(
        'taxonomy'     => 'product_cat',
        'orderby'      => 'name',
        'show_count'   => 0,
        'hierarchical' => 1,
        // 'title_li'     => $title,
    );
    $all_categories = get_categories($cat_args);

    return json_encode($all_categories);
}