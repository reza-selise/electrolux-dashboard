<?php 

/** This file will have necessary function  */



/**
 * Include 
 * 'login_user_id' => get_current_user_id(),
 * in the rest API args
 * 
 * 
 * Example use case

    // while register
    register_rest_route( 'el-dashboard-api', '/generic-comments', [
        'methods' => 'GET',
        'callback' => 'el_get_generic_comments',
        'login_user_id' => get_current_user_id(),
    ]);

    // in callback
    if(el_has_rest_Authority($ReqObj) == true ){

        // do stuff
        $attrs              =  $ReqObj->get_attributes();
        $current_user_id    = intval($attrs['login_user_id']);
        $current_user       = get_user_by( 'id', $current_user_id );

    }else{
        return wp_send_json([
            'status'    => false,
            'data'      => 'Please login to get the comments or you have to be admin'
        ]);
    }

 */
function el_has_rest_Authority($EL_REST_REQUEST){
    $attrs =  $EL_REST_REQUEST->get_attributes();
    $has_authority = false;
    // $has_authority = true;

    if( isset($attrs['login_user_id']) && intval($attrs['login_user_id']) > 0  ){

        $user_id = intval($attrs['login_user_id']);
        $current_user = get_user_by( 'id', $user_id );
        $roles = ( array ) $current_user->roles;

        if(in_array('administrator', $roles )){
            $has_authority = true;
        }

    }
    return $has_authority;
}

// this function will return ids based on given range
/**
 * start_range : 20210101
 * end_range : 20211232
 * 
 */
function get_event_ids_by_range( $start_range, $end_range ){
    global $wpdb;
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
    $response   = $wpdb->get_results( $wpdb->prepare( $query, $start_range, $end_range ) );

    return el_get_id_from_response($response);

}

// extract post id from $response obj
function el_get_id_from_response( $response ){
    $output = [];
    foreach($response as $item){
        $output[] = $item->post_id;
    }
    return $output;
}

// Get month end date
function el_get_month_date($month, $year){
    return cal_days_in_month(CAL_GREGORIAN, $month, $year); 
}

// this function will add leading zero
function el_add_leading_zero($month_number){
    
    $month = (string) $month_number ;

    $month = trim($month);
    if( strlen( $month ) == 1){
        $month = "0".$month;
    }
    return $month;

}



/**
 * This function will receive timeline filter and return products ids array
 * 
    timeline_type : 'month|year|custom_date_range|custom_time_frame'
    timeline_filter : 

        month : [01,02,03,04,05],
        year : [2020,2022,2021],
        custom_date_range: [ [ 20200101, 20201231  ], [ 20210101, 20211231  ],

        custom_time_frame : {
            count: 5,
            type : year|month
        }
}
 */
function get_products_by_timeline_filter( $timeline_type , $timeline_filter, $received_data = [] ){

    $final_ids = [];

    /**
     * Fixed Query arr will use for query by start and end date 
     * [
     *       [20220103 ,20220123 ],
     *       [20210103 ,20220123 ]
     *       [20200103 ,20201220 ]
     * ]
     */
    $query_arr = []; 

    // If month type selected selected 
    if($timeline_type == 'months'){
        
        // store the month array 
        $months = $timeline_filter;
        

        $years   = [ ];
        for( $i=0; $i<5; $i++ ){
            $years[] = date("Y") - $i;
        }

        // build query array by range
        foreach($months as $month){
            $month = el_add_leading_zero($month);
            foreach($years as $year ){
                $start_range = $year . $month . '01';
                $end_range   = $year . $month . el_get_month_date($month, $year);

                // push into the query array
                $query_arr[] = [$start_range, $end_range];
            }
        }
    }
    // -------- Time filter end


    // -------- Year filter START
    if($timeline_type == 'years'){
        $years = $timeline_filter;

        if( 
            isset($received_data['year_months'])  && 
            !empty($received_data['year_months']) 
        ){
            $month_arr = $received_data['year_months'];

            foreach($years as $year ){

                foreach($month_arr as $month ){
                    $month = el_add_leading_zero($month);


                    $start_range = $year . $month . '01';
                    $end_range   = $year . $month . el_get_month_date($month, $year);

                    // push into the query array
                    $query_arr[] = [$start_range, $end_range];

                }
            }



        }else{


            foreach($years as $year ){
                $start_range = $year . '01' . '01';
                $end_range   = $year . '12' . '31';

                // push into the query array
                $query_arr[] = [$start_range, $end_range];
            }
        }

    }
    // -------- Year filter END

    // -------- Custom date range filter START
    if($timeline_type == 'custom_date_range'){
        $filter_arr = $timeline_filter;

        foreach($filter_arr as $filter_item ){
            // push into the query array
            $query_arr[] = [$filter_item[0], $filter_item[1]];
        }
    }
    // Custom date range filter END
    



    // do the query 
    foreach( $query_arr as $single_query_arr ){
        $query_ids = get_event_ids_by_range( $single_query_arr[0],$single_query_arr[1] );
        // var_dump($single_query_arr);
        foreach($query_ids as $p_id){
            $final_ids[]=$p_id;
        }
    }


    $unique_ids =  array_unique($final_ids);

    return $unique_ids;


}

/**
 * This function will use to filter all the data
 * 
 * filter_key_values
 */

function el_FILTER_PRODUCTS_from_structure_data($structure_data, $requestData, $skip_keys = []){

    if( 
        isset($requestData['filter_key_values']) && 
        !empty($requestData['filter_key_values']) 
    ){
        
        // var_dump($requestData);
        $filter_arr = $requestData['filter_key_values'];
        $output = [];

        // loop through all the posts
        foreach($structure_data as $product_id => $product_data ){        

            $is_satisfy = true;

            // loop through all the filter if not match/fount return false
            foreach( $filter_arr as $key => $value ){

                if( isset( $product_data[$key] ) ){


                    $saved_value_to_match   = $product_data[$key] ;
                    $request_value_to_match = sanitize_key( $value );

                    if( $key == 'sales_person' ||  $key == 'category' ){

                        if( 
                            in_array($request_value_to_match, $saved_value_to_match) ||
                            isset($saved_value_to_match[$request_value_to_match])
                        ){
                            // do nothing 
                        }else{
                            $is_satisfy = false;
                        }

                    }else{
                        $saved_value_to_match   = sanitize_key( $product_data[$key] );
                        $request_value_to_match = sanitize_key( $value );

                        if( $saved_value_to_match == $request_value_to_match ){
                            // do nothing
                        }else{
                            $is_satisfy = false;
                        }
                    }

                }else{
                    $is_satisfy = false;
                }

            }

            if($is_satisfy == true){
                $output[$product_id] = $product_data;
            }

        }

        return $output;
    }else{
        return $structure_data;
    }

}


function get_ORDERS_by_timeline_filter( $timeline_type , $timeline_filter, $received_data =[] ){

    $final_ids = [];

    /**
     * Fixed Query arr will use for query by start and end date 
     * [
     *       [20220103 ,20220123 ],
     *       [20210103 ,20220123 ]
     *       [20200103 ,20201220 ]
     * ]
     */
    $query_arr = []; 

    // If month type selected selected 
    if($timeline_type == 'months'){
        
        // store the month array 
        $months = $timeline_filter;
        

        $years   = [ ];
        for( $i=0; $i<5; $i++ ){
            $years[] = date("Y") - $i;
        }

        // build query array by range
        foreach($months as $month){
            $month = el_add_leading_zero($month);
            foreach($years as $year ){
                $start_range = $year . $month . '01';
                $end_range   = $year . $month . el_get_month_date($month, $year);

                // push into the query array
                $query_arr[] = [$start_range, $end_range];
            }
        }
    }
    // -------- Time filter end


    // -------- Year filter START
    if($timeline_type == 'years'){
        $years = $timeline_filter;

        if( 
            isset($received_data['year_months'])  && 
            !empty($received_data['year_months']) 
        ){
            $month_arr = $received_data['year_months'];

            foreach($years as $year ){

                foreach($month_arr as $month ){
                    $month = el_add_leading_zero($month);


                    $start_range = $year . $month . '01';
                    $end_range   = $year . $month . el_get_month_date($month, $year);

                    // push into the query array
                    $query_arr[] = [$start_range, $end_range];

                }
            }
        }else{

            foreach($years as $year ){
                $start_range = $year . '01' . '01';
                $end_range   = $year . '12' . '31';

                // push into the query array
                $query_arr[] = [$start_range, $end_range];
            }
        }

    }
    // -------- Year filter END

    // -------- Custom date range filter START
    if($timeline_type == 'custom_date_range'){
        $filter_arr = $timeline_filter;

        foreach($filter_arr as $filter_item ){
            // push into the query array
            $query_arr[] = [$filter_item[0], $filter_item[1]];
        }
    }
    // Custom date range filter END
    



    // do the query 
    foreach( $query_arr as $single_query_arr ){
        $query_ids = get_order_ids_by_range( $single_query_arr[0],$single_query_arr[1] );
        // var_dump($single_query_arr);
        foreach($query_ids as $p_id){
            $final_ids[]=$p_id;
        }
    }


    $unique_ids =  array_unique($final_ids);

    return $unique_ids;


}

function get_order_ids_by_range( $start_range, $end_range ){
    
    
    /*
        modify start and end range Because in Database it store as
        YYYY-MM-DD

        received : 20221231
    */ 

    if( strlen($start_range) == 8 &&  strlen($start_range) == 8 ){
        $start_year = substr($start_range,0, 4);
        $start_month = substr($start_range,4,2);
        $start_date = substr($start_range,6,2);

        // push the final version 
        $start_range = $start_year ."-". $start_month."-". $start_date;

        $end_year = substr($end_range,0, 4);
        $end_month = substr($end_range,4,2);
        $end_date = substr($end_range,6,2);

        // update the final variable 
        $end_range = $end_year ."-". $end_month."-". $end_date;

        // var_dump($start_range);
        // var_dump($end_range);


    }else{
        return [];
    }

    

    global $wpdb;
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
        $wpdb->posts.post_type = 'shop_order' 
        AND 
        $wpdb->postmeta.meta_key = 'event_date' AND  $wpdb->postmeta.meta_value BETWEEN '%s' AND '%s'
    ";
    $response   = $wpdb->get_results( $wpdb->prepare( $query, $start_range, $end_range ) );

    return el_get_id_from_response($response);

}


// collect filter information which will match with the 
function el_GET_PRODUCT_FILTER_VALUES($single_product_id){
    
    $output = [];

    // get customer type
    $customerType   = get_post_meta( $single_product_id,  'customer_type', true );

    if( $customerType ){
        $output['customer_type'] = $customerType;
    }
    
    // location
    $location       = get_post_meta( $single_product_id, 'event_location', true );
    if( $location ){
        $output['location'] = $location;
    }

    // get category 

    $product_cats       = get_the_terms( $single_product_id , 'product_cat' );
    $each_product_category_arr = []; // use to store all the category along with post id 
    foreach( $product_cats as $cat){
        
        $cat_id     = $cat->term_id ; 
        $cat_name   = $cat->name;

        // push each category in a array
        $each_product_category_arr[$cat_id] = $cat_name;

    }
    $category_list  = $each_product_category_arr;

    if( $category_list ){
        $output['category'] = $category_list;
    }

    // Lead
    $consultant_lead = get_post_meta( $single_product_id, 'consultant_lead', true );
    if( $consultant_lead ){
        $output['consultant_lead'] = $consultant_lead;
    }

    // Sales persons 
    $sales_person_arr = [];
    $salesperson_id_1     = get_post_meta( $single_product_id, 'salesperson-1', true );
    $salesperson_id_2     = get_post_meta( $single_product_id, 'salesperson-2', true );
    $salesperson_id_3     = get_post_meta( $single_product_id, 'salesperson-3', true );


    if(  intval($salesperson_id_1)  ){ 
        $sales_person_arr[] = $salesperson_id_1;
    }

    if(  intval($salesperson_id_2)  ){ 
        $sales_person_arr[] = $salesperson_id_2;
    }

    if(  intval($salesperson_id_3)  ){ 
        $sales_person_arr[] = $salesperson_id_3;
    }

    if( $sales_person_arr ){
        $output['sales_person'] = $sales_person_arr;
    }

    //-------------------------------------------------------------

    if( get_post_type( $single_product_id ) == 'shop_order' ){

        $CURRENT_ORDER = wc_get_order($single_product_id);
        $order_status  = $CURRENT_ORDER->get_status();

        $output['event_status'] = $order_status;

    }else{

        $product_status     = get_post_meta( $single_product_id, 'product_status', true );

        if( $product_status ){
            $output['event_status'] = $product_status;
        }
    
    }

    return $output;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     
    

}