<?php 
/**
 * EVENT BY CATEGORY API
 * Overview of Event category
 * 
 * filter parameter list
 * 
 * 
 * 
    
    JSON BODY
    {
        taken_place:    [planed,cancelled],
        type:           event/participant ,

        timeline_type : 'month|year|custom_date_range|custom_time_frame'
        
        timeline_filter : 
            [01,02,03,04,05] // for month
            [2020,2022,2021] // for year
            [ [ 20200101, 20201231  ] [ 20210101, 20211231  ] ], //custom_date_range

            custom_time_frame : {
                count: 5,
                type : year|month
            }

    }
 * 
 * 
 * 
 */


add_action( 'rest_api_init', function () {
    $namespace = 'elux-dashboard/v1';
    register_rest_route( $namespace, '/events-by-category', [
        'methods'             => 'POST',
        'callback'            => 'elux_get_events_by_category',
        'permission_callback' => '__return_true',
    ] );
} );


if( ! function_exists( 'elux_get_events_by_category' ) ){
    function elux_get_events_by_category( $ReqObj ){ 
        
        // this will use to store all relevant information along with post id
        $structure_data   = [];

        // get default year -- by default last five month 
        $default_year   = [ ];
        for( $i=0; $i<5; $i++ ){
            $default_year[] = date("Y") - $i;
        }

        // extract timeline data
        $received_data = $ReqObj->get_json_params();

        $timeline_type = '';
        $timeline_filter = [];


        if( isset( $received_data['timeline_type']) ){
            $timeline_type = sanitize_key( $received_data['timeline_type'] );
        }
        
        if( isset( $received_data['timeline_filter']) ){
            $timeline_filter =  $received_data['timeline_filter'] ;
        }


        $post_ids               = get_product_by_timeline_filter( $timeline_type,$timeline_filter);
        $category_list_unique   = [];    // [ id => 'name' , 51 => steam-demo ]
        

        foreach( $post_ids as $single_post_id  ){
            

            // START --------- Product Category information
            $product_cats = get_the_terms( $single_post_id , 'product_cat' );
            $each_product_category_arr = []; // use to store all the category along with post id 

            foreach( $product_cats as $cat){
                $cat_id     = $cat->term_id;
                $cat_name   = $cat->name;

                // store unique category
                if( !isset($category_list_unique[$cat_id] ) ){
                    $category_list_unique[$cat_id] = $cat_name;
                }

                // push each category in a array
                $each_product_category_arr[$cat_id] = $cat_name;

            }

            // push data 
            $structure_data[$single_post_id]['category'] = $each_product_category_arr;
            // END ------------ Category information assign complete in STRUCTURE DATA variable

            

            
            // START --------- Event status (Taken place, planed, cancelled)
            $product_status = get_post_meta( $single_post_id, 'product_status', true );

            if( $product_status ){
                $structure_data[$single_post_id]['product_status'] = $product_status;
            }else{
                $structure_data[$single_post_id]['product_status'] = 'Not set';
            }
            // END ------------ Event status (Taken place, planed, cancelled)
            

            // START --------- Event time (month:01|02|03... , year: 2020|2021|2021 , day: 1,2,3,4 )
            $event_time_string = (string) get_post_meta( $single_post_id, 'date', true );

            if( is_string($event_time_string) && trim($event_time_string) && strlen($event_time_string) === 8 ){
                
                $event_year     =  substr($event_time_string,0,4) ; 
                $event_month    =  substr($event_time_string,4,2)  ;
                $event_date     = substr($event_time_string,6,2)  ;

                $structure_data[$single_post_id]['event_time'] = [
                    'date' => $event_date,
                    'month' => $event_month,
                    'year' => $event_year,
                ];

            }else{
                $structure_data[$single_post_id]['event_time'] = 'Not set';
            }

            // END --------- Event time 


        } // for loop end
        // print_r($structure_data);

        /*
        =======================================================================
            structure_data end
        =======================================================================
        */

        // prepare data for chart/output

        foreach( $category_list_unique as $cat_id => $cat_name ){

        }





        print_r($category_list_unique);
        print_r($structure_data);

        

        return rest_ensure_response( array(
            'status'    => false,
            'message'   => 'failure',
            'data'      => [],
        ) );
        
    }
}







function get_events_ids_by_year( $start_year, $end_year = false ){
    
    global $wpdb;

    if($end_year == false){
        $end_year = $start_year ;
    }

    $start_date         = $start_year .  '01' . '01' ;
    $end_date           = $end_year .  '12' . "31" ;


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

    return el_get_id_from_response($response);

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


