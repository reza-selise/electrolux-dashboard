<?php 
/**
 * EVENT BY Course type API
 * Overview of Event category
 * sample file name : event-by-cooking-course-type-api.json
 * 
 */


// die();
add_action( 'rest_api_init', function () {
    $namespace = 'elux-dashboard/v1';
    register_rest_route( $namespace, '/events-by-cooking-course-type', [
        'methods'             => 'POST',
        'callback'            => 'elux_get_events_by_cooking_course_type',
        'permission_callback' => '__return_true',
    ] );
} );


if( ! function_exists( 'elux_get_events_by_cooking_course_type' ) ){
    function elux_get_events_by_cooking_course_type( $ReqObj ){ 
        
        // Final data for the response
        $final_data     = [];
        
        // this will use to store all relevant information along with post id
        $structure_data = [];


        // extract json data from request
        $received_data = $ReqObj->get_json_params();

        $timeline_type = false;
        $timeline_filter = false;


        if( isset( $received_data['timeline_type']) && !empty( $received_data['timeline_type']) ){
            $timeline_type = sanitize_key( $received_data['timeline_type'] );
        }
        
        if( isset( $received_data['timeline_filter']) && !empty( $received_data['timeline_filter']) ){
            $timeline_filter =  $received_data['timeline_filter'] ;
        }

        if( $timeline_type == false || $timeline_filter == false  ){
            return rest_ensure_response([
                'status' =>false,
                'data'  => [],
                'message' => "No valid input found, timeline_type, timeline_filter"
            ]);
        }


        // print_r($timeline_type,$timeline_filter);
        /// 1. ---------- Get product IDs
        $product_ids        = get_products_by_timeline_filter( $timeline_type,$timeline_filter);

        /// 2. ---------- Get Structure data along with post id
        $structure_data     = el_events_by_cooking_course_type_STRUCTURE_DATA($product_ids);



        /// 3. ---------- Filter data
        $filtered_data      = el_FILTER_PRODUCTS_from_structure_data($structure_data, $received_data);

        // print_r($filtered_data);
        
        // /// 4. ---------- Get Final output
        $final_data         = el_events_by_cooking_course_type_FINAL_DATA($filtered_data, $received_data);



        if($structure_data && $final_data ){
            return rest_ensure_response( array(
                'status'    => true,
                'message'   => 'Data fetch successful',
                'data'      => $final_data,
            ) );
        }else{
            return rest_ensure_response( array(
                'status'    => false,
                'message'   => 'Something is wrong while updating',
                'dev_message'   => '$structure_data & $final_data failed to receive',
                'data'      => [],
            ) );
        }
        
    }
}



function el_events_by_cooking_course_type_STRUCTURE_DATA($product_ids){
    /**
     * This will use to serve structure for each product data along with post ids
     * 
     * return 
     * example output
    [
        1500 => [
            'year' => 2022,
            'month' => 01,
            'category' => [ cat_id =>  cat_name , cat_id =>  cat_name ]
            'filter_name' => filter_value
            'attribute_name' => attribute_value
            'filter_name_2' => filter_value 
        ],
        1496 => [
            'year' => 2021,
            'month' => 12,
            'filter_name' => filter_value
            'attribute_name' => attribute_value
            'filter_name_2' => filter_value 
        ]
    ]
     * 
     * 
     */

    $structure_data = [];

    /// ------
    foreach( $product_ids as $single_product_id  ){
        
        // START --------- Event status (Taken place, planed, cancelled)
        $product_status     = get_post_meta( $single_product_id, 'product_status', true );
        
        // START --------- Event time (month:01|02|03... , year: 2020|2021|2021 , day: 1,2,3,4 )
        $event_time_string  = (string) get_post_meta( $single_product_id, 'date', true );

        // add total-sale/Participant 
        $total_sales  = intval(get_post_meta( $single_product_id, 'total_sales', true ));
        $structure_data[$single_product_id]['total_sales']      = $total_sales;



        if( $product_status ){
            $structure_data[$single_product_id]['product_status'] = $product_status;
        }else{
            $structure_data[$single_product_id]['product_status'] = 'false';
        }
        // END ------------ Event status (Taken place, planed, cancelled etc)
        

        if( is_string($event_time_string) && trim($event_time_string) && strlen($event_time_string) === 8 ){
            
            $event_year     =   substr($event_time_string,0,4) ; 
            $event_month    =   substr($event_time_string,4,2)  ;
            $event_date     =   substr($event_time_string,6,2)  ;

            $structure_data[$single_product_id]['event_time'] = [
                'date' => $event_date,
                'month' => $event_month,
                'year' => $event_year,
            ];

        }else{
            $structure_data[$single_product_id]['event_time'] = 'false';
        }
        // END --------- Event time 


        // START ---  For Cooking course type ADDING
        $each_product_category_arr = [];

        $parent_category_id =  47;  // Cooking Class Parent category id 
        
        $product_cats       = get_the_terms( $single_product_id , 'product_cat' );

        foreach( $product_cats as $cat){

            // we only interest in Cooking class sub category
            if( intval($cat->parent) == 47 ){
                $cat_id     = $cat->term_id;
                $cat_name   = $cat->name;
                // push each category in a array
                $each_product_category_arr[$cat_id] = $cat_name;
            }

        }
        $structure_data[$single_product_id]['category'] = $each_product_category_arr;
        // END ------------ CATEGORY ADDING

    } // for loop end

    return $structure_data;
}




function el_events_by_cooking_course_type_FINAL_DATA($structure_data, $requestData){

    $labels_by_id = [];

    /*
    [
        [2022]['category id'] =>{
            count : 5,
            label : 6,
        }

        [2022]['cat id'] =>{
            count : 5,
            label : 6,
        }
        
    ]
    */
    

    // get dataset by year
    $dataset_by_year    = [];

    // Loop through all the products and store the cat id 
    foreach( $structure_data as $single_product_id =>  $each_product_data  ){

        // loop through all the category 
        if( 
            isset($each_product_data['category']) &&  
            is_array($each_product_data['category'])
        ){

            foreach( $each_product_data['category'] as $cat_id => $cat_name ){

                // labels updated
                $labels_by_id[$cat_id] = $cat_name;
                $year   = $each_product_data['event_time']['year'];

                // assign label
                $dataset_by_year[$year][$cat_id]['label'] = $cat_name ;

                if( 
                    isset($dataset_by_year[$year]) && 
                    isset($dataset_by_year[$year][$cat_id]) && 
                    isset($dataset_by_year[$year][$cat_id]['count']) 
                ){
                    $previous_count = intval($dataset_by_year[$year][$cat_id]['count']);

                    // update count 
                    if( $requestData['type'] == 'participant' ){
                        $dataset_by_year[$year][$cat_id]['count'] = intval($each_product_data['total_sales']) + $previous_count;
                    }else{
                        $dataset_by_year[$year][$cat_id]['count'] = $previous_count + 1;
                    }
                    

                }else{
                    if( $requestData['type'] == 'participant' ){
                        $dataset_by_year[$year][$cat_id]['count'] = intval( $each_product_data['total_sales']);
                    }else{
                        $dataset_by_year[$year][$cat_id]['count'] = 1 ;
                    }

                }
                
            }
        }



    }// main structure_data loop end 



    
    
    // ---- Prepare dataset
    $final_datasets = [];

    foreach( $dataset_by_year as $year => $count_and_label  ){

        $dataset_data_arr = [];
        

        foreach( $labels_by_id as $label_id => $label_name ){
            if( isset($dataset_by_year[$year][$label_id]['count'] )  ){
                $dataset_data_arr[] = (string) $dataset_by_year[$year][$label_id]['count'];
            }else{
                $dataset_data_arr[] = "0";
            }
        }

        // push each dataset
        $final_datasets[] = [
            'label' => $year,
            'data' => $dataset_data_arr
        ];

    }
    // END ---------------- 

    // prepare labels
    $final_labels = [];
    foreach( $labels_by_id as $label_id => $label_name ){
        $final_labels[] = $label_name;
    }

    return [
        'labels' => $final_labels,
        'datasets' => $final_datasets,
    ];


}