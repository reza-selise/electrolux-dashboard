<?php 
add_action( 'rest_api_init', function () {
    $namespace = 'elux-dashboard/v1';
    register_rest_route( $namespace, '/events-by-category', [
        'methods'             => 'GET',
        'callback'            => 'elux_get_events_by_category',
        'permission_callback' => '__return_true',
    ] );
} );

if( ! function_exists( 'elux_get_events_by_category' ) ){
    function elux_get_events_by_category( $ReqObj ){
        
        $final_output   = [];

        $response   = get_events_by_year(2022);
        $post_ids   = array_map( "elux_order_id_array_map", $response );

        // $post_ids_with_content  = [];
        $category_list          = [];
        // var_dump($order_ids);
        


        foreach( $post_ids as $id  ){
            $product_cats = get_the_terms( $id , 'product_cat' );

            $post_ids_with_content[$id]['category'] = [];

            // print_r($product_cats);

            foreach( $product_cats as $cat){
                $cat_id = $cat->term_id;
                $cat_name = $cat->name;

                if( !isset($category_list[$cat_id] ) ){
                    $category_list[$cat_id] = $cat_name;
                }
            }


        }
        print_r($category_list);

        

        return rest_ensure_response( array(
            'status'    => false,
            'message'   => 'failure',
            'data'      => [],
        ) );
        
    }
}




function get_events_by_year( $year ){
    
    global $wpdb;


    $start_date         = $year .  '01' . '01' ;
    $end_date           = $year .  '12' . "31" ;


    $query      = "
        SELECT DISTINCT 
        $wpdb->postmeta.post_id 
        FROM
        $wpdb->postmeta 
        LEFT JOIN 
        $wpdb->posts 
        ON 
        $wpdb->postmeta.post_id = $wpdb->posts.ID 
        WHERE 
        $wpdb->posts.post_type = 'product' 
        AND 
        $wpdb->postmeta.meta_key = 'date' AND  $wpdb->postmeta.meta_value BETWEEN '%s' AND '%s'
    ";


    $response   = $wpdb->get_results( $wpdb->prepare( $query, $start_date, $end_date ) );

    return $response;

}





function get_events_by_month_rang( $month, $year ){
    

    global $wpdb;

    // make month format
    $month = (string) $month;
    $month = trim($month);
    if( strlen($month) == 1 ){
        $month = '0'.$month;
    }


    $start_date         = $year .  $month . '01' ;
    $end_date           = $year .  $month . cal_days_in_month(CAL_GREGORIAN, $month, $year);


    $query      = "
        SELECT DISTINCT 
        $wpdb->postmeta.post_id 
        FROM
        $wpdb->postmeta 
        LEFT JOIN 
        $wpdb->posts 
        ON 
        $wpdb->postmeta.post_id = $wpdb->posts.ID 
        WHERE 
        $wpdb->posts.post_type = 'product' 
        AND 
        $wpdb->postmeta.meta_key = 'date' AND  $wpdb->postmeta.meta_value BETWEEN '%s' AND '%s'
        
    ";



    $response   = $wpdb->get_results( $wpdb->prepare( $query, $start_date, $end_date ) );




    // var_dump($response);
    return $response;

}


