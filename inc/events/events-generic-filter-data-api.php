<?php

// add_action( 'rest_api_init', function () {
//     $namespace = 'elux-dashboard/v1';
//     register_rest_route($namespace, '/generic-filter-data', array(
//         'methods' => 'GET',
//         'callback' => 'get_generic_filter_data',
//         'permission_callback' => '__return_true',
//     ));
// });



function get_generic_filter_data(){
    $return_data = array(
        "customer_types" => array(
            [
                "value"=> "electrolux_internal",
                "label" => "Elux"
            ],
            [
                "value"=> "b2b",
                "label" => "B2B"
            ],
            [
                "value"=> "b2c",
                "label" => "B2C"
            ]
        ), 
        "booking_types"=> array(
            [
                "value"=> "Booked Manually",
                "label" => "Booked Manually"
            ],
            [
                "value"=> "Walk-in",
                "label" => "Walk-in"
            ]
        ),      
        "lead_types"=> array(
            [
                "value"=> "fb_leads",
                "label" => "FB Leads"
            ]
        ),
        "data_types" => [
            [
                "value"=> "events",
                "label" => "Events"
            ],
            [
                "value"=> "participants",
                "label" => "Participants"
            ]
        ],
        "event_status" => array(
            [
                "value"=>"reserved",
                "label"=>"Reserved",
            ],
            [
                "value"=>"planned",
                "label"=>"Planned",
            ],
            [
                "value"=>"took_place",
                "label"=>"Took place",
            ],
            [
                "value"=>"canceled",
                "label"=>"Canceled",
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
        'role__in' => array('administrator','elex_instructor','elex_consultant', 'sales'),
        // 'role__in' => array('sales'),
        'order'          => 'ASC',
    );

    $user_query = new WP_User_Query( $args );
    $sales_users = [];
    $fb_leads = [];
    $culinary_ambassadors = [
        "role_name"=> "Culinary Ambassadors",
        "users" => [],
    ];
    $consultants = [
        "role_name"=> "Consultants",
        "users" => [],
    ];
    $admins = [
        "role_name"=> "Admins",
        "users" => [],
    ];

    if ( ! empty( $user_query->get_results() ) ) {
        foreach ( $user_query->get_results() as $user ) {
           $user_name = $user->data->display_name;
           $user_id = $user->data->ID;
           $user_email = $user->data->user_email;
           $user_role = $user->roles[0];
                     
           $usr_data = array(
                "value"=> $user_id,
                "email"=>  $user_email,
                "label"=>  $user_name,
                "role"=>   $user_role,
            );
            // sales
            if($user_role == 'sales'){
                array_push($sales_users,$usr_data);
            }
            elseif($user_role == 'elex_instructor'){
                array_push($culinary_ambassadors["users"], $usr_data);
            }
            elseif($user_role == 'elex_consultant'){
                array_push($consultants["users"], $usr_data);
            }
            elseif($user_role == 'administrator'){
                array_push($admins["users"], $usr_data);
            }
        }
        wp_reset_postdata();
    }
    $fb_leads = [
        $culinary_ambassadors,
        $consultants,
        $admins
    ];

    $return_data['fb_leads'] =  $fb_leads;
    $return_data['sales_employee'] = $sales_users;

   
    // return  $return_data;
    return  $return_data;
}