<?php

add_action( 'rest_api_init', function () {
    $namespace = 'elux-dashboard/v1';
    register_rest_route($namespace, '/generic-filter-data', array(
        'methods' => 'GET',
        'callback' => 'get_generic_filter_data',
        'permission_callback' => '__return_true',
    ));
});

function get_generic_filter_data($request){
    $return_data = array(
        "customer_types" => array(
            [
                "slug"=> "b2b",
                "name" => "B2B"
            ],
            [
                "slug"=> "fb_lead",
                "name" => "FB Lead"
            ],
            [
                "slug"=> "walking",
                "name" => "Walking"
            ]
        ),        
        "leads"=> array(
            [
                "slug"=> "fb_leads",
                "name" => "FB Leads"
            ],
            [
                "slug"=> "walking",
                "name" => "Walking"
            ]
        ),
        "sales_emp" => [
            "Mr. Maruf",
            "Abul Hasan"
        ],
        "data_types" => [
            "abcd",
            "bcdef"
        ],
        "event_status" => array(
        [
            "slug"=>"cancel_by_admin",
            "name"=>"Cancel by Admin",
        ],
        [
            "slug"=>"cancel_by_customer",
            "name"=>"Cancel by Customer",
        ])
    );
  
    // product categories
    $cat_args = array(
        'taxonomy'     => 'product_cat',
        'orderby'      => 'name',
        'show_count'   => 0,
        'hierarchical' => 1,
        'lang'         => 'de',
    );
    $all_categories = get_categories($cat_args);

    $categories = [];
    foreach($all_categories as $key=>$cat){
        $cat_child = get_terms('product_cat',array('child_of' => $cat->term_id));
        $cat_child_data = [];
        if($cat_child){
            foreach($cat_child as $child){
                $cat_child = array(
                    "id"=> $child->term_id,
                    "slug"=> $child->slug,
                    "name"=> $child->name,
                );
                array_push($cat_child_data, $cat_child);
            }
        }
        $cat_data = array(
            "id"=> $cat->term_id,
            "slug"=> $cat->slug,
            "name"=> $cat->name,
            "sub_category" =>  $cat_child_data,
        );
       array_push($categories,$cat_data);

      
        
    }
    $return_data['categories'] = $categories;
    //----- locations-----
    $args = array(
        'posts_per_page'   => -1,
        'post_type'        => 'location',
        'post_status' =>    'publish',
        'return' => 'id,slug,title',
        'orderby'        => 'title',
        'order'          => 'ASC',
    );
    $the_post_query = new WP_Query( $args );
    $locations = [];
    if ( $the_post_query->have_posts() ) {
       
        while ( $the_post_query->have_posts() ) {
            $the_post_query->the_post();
            $the_title = get_the_title();
            $the_slug = get_post_field( 'post_name', get_post() );
            $the_id = get_the_id();

            $loc_data = array(
                "id"=> $the_id,
                "slug"=>  $the_slug,
                "name"=>  $the_title
            );
           array_push($locations,$loc_data);
        }
       
        /* Restore original Post Data */
        wp_reset_postdata();
    }
    $return_data['locations'] = $locations;
    return $return_data;
}