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
                "slug"=> "electrolux_internal",
                "name" => "Electrolux Internal"
            ],
            [
                "slug"=> "b2b",
                "name" => "B2B"
            ],
            [
                "slug"=> "b2c",
                "name" => "B2C"
            ]
        ), 
        "booking_types"=> array(
            [
                "slug"=> "Booked Manually",
                "name" => "Booked Manually"
            ],
            [
                "slug"=> "Walk-in",
                "name" => "Walk-in"
            ]
        ),      
        "lead_types"=> array(
            [
                "slug"=> "fb_leads",
                "name" => "FB Leads"
            ]
        ),
        "data_types" => [
            [
                "slug"=> "events",
                "name" => "Events"
            ],
            [
                "slug"=> "participants",
                "name" => "Participants"
            ]
        ],
        "event_status" => array(
            [
                "slug"=>"reserved",
                "name"=>"Reserved",
            ],
            [
                "slug"=>"planned",
                "name"=>"Planned",
            ],
            [
                "slug"=>"took_place",
                "name"=>"Took place",
            ],
            [
                "slug"=>"canceled",
                "name"=>"Canceled",
            ]
        )
    );
  
    // -------------------- product categories
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
    //----------------------------------- locations-----
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
        wp_reset_postdata();
    }
    $return_data['locations'] = $locations;
    //------------------------------------- Users by role----------------------------------------------------------------
    $usr_args = array(
        'role__in' => array('elex_instructor','elex_consultant', 'sales','shop_manager'),
        // 'orderby'        => 'user_nicename',
        'order'          => 'ASC',
    );

    $user_query = new WP_User_Query( $args );
    $users = [];
    if ( ! empty( $user_query->get_results() ) ) {
        foreach ( $user_query->get_results() as $user ) {
            error_log(print_r('users' ,1));
            error_log(print_r($user ,1));
           $user_name = $user->data->display_name;
           $user_id = $user->data->ID;
           $user_email = $user->data->user_email;
           $user_role = $user->roles[0];
          
           $usr_data = array(
                "id"=> $user_id,
                "email"=>  $user_email,
                "name"=>  $user_name,
                "role"=>   $user_role,
            );
            array_push($users,$usr_data);
        }
        wp_reset_postdata();
    }
    $return_data['sales_employee'] = $users;
    return  $return_data;
}