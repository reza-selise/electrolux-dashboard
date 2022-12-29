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
    register_rest_route( $namespace, '/events-by-cancellation', [
        'methods'             => 'POST',
        'callback'            => 'elux_get_events_by_cancellation',
        'permission_callback' => '__return_true',
    ] );
} );


if( ! function_exists( 'elux_get_events_by_cancellation' ) ){
    function elux_get_events_by_cancellation( $ReqObj ){ 
        
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

        if( 
            isset($received_data['type']) &&
            $received_data['type'] == 'participants' 
        ){


            /// 1. ---------- Get Order IDs
            $order_ids          = get_ORDERS_by_timeline_filter( $timeline_type, $timeline_filter, $received_data);

            //// 2. Structure data for farther filtering for this api only 
            $structure_data     = el_ORDERS_by_cancellation_STRUCTURE_DATA($order_ids);

            
        }else{
    
            /// 1. ---------- Get product IDs
            $product_ids        = get_products_by_timeline_filter( $timeline_type,$timeline_filter, $received_data);
    
            /// 2. ---------- Get Structure data along with post id
            $structure_data     = el_events_by_cancellation_STRUCTURE_DATA($product_ids);
            
        }

        // print_r($structure_data);


        //// 3. ---------- Apply global filter Filter data
        $filtered_data      = el_FILTER_PRODUCTS_from_structure_data($structure_data, $received_data);


        
        //// 4. ---------- Get Final output
        $final_data         = el_events_cancellation_FINAL_DATA($filtered_data, $received_data);

        // print_r($final_data);

 

        if($structure_data && $final_data ){
            return rest_ensure_response( array(
                'status'    => true,
                'status_code' => 200,
                'message'   => 'Data fetch successful',
                'data'      => $final_data,
            ) );
        }else{
            return rest_ensure_response( array(
                'status'    => 400,
                'status'        => false,                
                'message'   => 'No data found.',
                'dev_message'   => '$structure_data & $final_data receive failed to receive',
                'data'      => [],
            ) );
        }
        
    }
}

function el_ORDERS_by_cancellation_STRUCTURE_DATA($product_ids){
    $structure_data = [];


    foreach( $product_ids as $single_product_id  ){
        
        // START --------- Event status (Taken place, planed, cancelled)
        $product_status     =  sanitize_key( get_post_meta( $single_product_id, 'product_status', true ) );
        

        $CURRENT_ORDER = wc_get_order($single_product_id);
        $order_status  = $CURRENT_ORDER->get_status();

        $canceled_by    =  get_post_meta( $single_product_id, 'order_cancelled_by',true);


        // only interest in cancelled order 
        if( $order_status == 'cancelled' && $canceled_by ){

            $event_time_string  =  get_post_meta( $single_product_id, 'event_date', true );
            $event_time_arr     = explode("-", trim($event_time_string));

            $event_year     =   $event_time_arr[0];
            $event_month     =   $event_time_arr[1];
            $event_date     =   $event_time_arr[2];

            // add time
            $structure_data[$single_product_id]['event_time'] = [
                'date' => $event_date,
                'month' => $event_month,
                'year' => $event_year,
            ];

            $structure_data[$single_product_id]['year'] = $event_year;



            // START Storing category 
            $items = $CURRENT_ORDER->get_items();

            
            foreach($items as $item){
                $current_order_product_id = $item->get_product_id();
                $product_cats       = get_the_terms( $current_order_product_id , 'product_cat' );

                $each_product_category_arr = []; // use to store all the category along with post id 

                foreach( $product_cats as $cat){
                    $cat_id     = $cat->term_id;
                    $cat_name   = $cat->name;

                    $each_product_category_arr[$cat_id] = $cat_name;
                    
                }
                $structure_data[$single_product_id]['category'] = $each_product_category_arr;

                // Collect filter information 
                $filter_arr = el_GET_PRODUCT_FILTER_VALUES($current_order_product_id);

                foreach( $filter_arr as $filter_key => $filter_value ){
                    $structure_data[$single_product_id]['filter_key_values'][$filter_key] = $filter_value;
                }


            }

            $structure_data[$single_product_id]['canceled_by'] = $canceled_by;

            // END storing category 
            
        }
        

    } // for loop end

    // print_r($structure_data);
    return $structure_data;


}

function el_events_by_cancellation_STRUCTURE_DATA($product_ids){
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
        $product_status     =  sanitize_key( get_post_meta( $single_product_id, 'product_status', true ) );
        
        
        if( $product_status == 'canceled' ){

            $structure_data[$single_product_id]['product_status'] = $product_status;
            $canceled_by  = (string) get_post_meta( $single_product_id, 'canceled_by', true );

            $structure_data[$single_product_id]['canceled_by'] = $canceled_by;


            // START --------- Event time (month:01|02|03... , year: 2020|2021|2021 , day: 1,2,3,4 )
            $event_time_string  = (string) get_post_meta( $single_product_id, 'date', true );

            // add total-sale/Participant 
            $total_sales  = intval(get_post_meta( $single_product_id, 'total_sales', true ));
            $structure_data[$single_product_id]['total_sales']      = $total_sales;

            $structure_data[$single_product_id]['product_status'] = 'false';
    
            if( is_string($event_time_string) && trim($event_time_string) && strlen($event_time_string) === 8 ){
                
                $event_year     =   substr($event_time_string,0,4) ; 
                $event_month    =   substr($event_time_string,4,2)  ;
                $event_date     =   substr($event_time_string,6,2)  ;
    
                $structure_data[$single_product_id]['event_time'] = [
                    'date' => $event_date,
                    'month' => $event_month,
                    'year' => $event_year,
                ];

                $structure_data[$single_product_id]['year'] =  $event_year;

    
            }else{
                $structure_data[$single_product_id]['event_time'] = 'false';
            }
        
            // END --------- Event time 


            // START ---  For Cooking course type ADDING
            $each_product_category_arr = [];

            
            $product_cats       = get_the_terms( $single_product_id , 'product_cat' );

            foreach( $product_cats as $cat){

                $cat_id     = $cat->term_id;
                $cat_name   = $cat->name;
                // push each category in a array
                $each_product_category_arr[$cat_id] = $cat_name;

            }
            $structure_data[$single_product_id]['category'] = $each_product_category_arr;
            // END ------------ CATEGORY ADDING

            // Collect filter information 
            $filter_arr = el_GET_PRODUCT_FILTER_VALUES($single_product_id);

            foreach( $filter_arr as $filter_key => $filter_value ){
                $structure_data[$single_product_id]['filter_key_values'][$filter_key] = $filter_value;
            }

            // Filter information adding done


        }// if its  a cancelled event

    } // for loop end

    return $structure_data;

}




function el_events_cancellation_FINAL_DATA($structure_data, $requestData){

    $labels_by_key = [];

    // get dataset by year
    $dataset_by_year    = [];

    // Loop through all the products and store the cat id 
    foreach( $structure_data as $single_product_id =>  $each_product_data  ){

        // loop through all the category 
        if( 
            isset($each_product_data['canceled_by']) &&  
            !empty($each_product_data['canceled_by'])
        ){

            $key = sanitize_key( $each_product_data['canceled_by'] );

            $labels_by_key[$key] = $each_product_data['canceled_by'] ; // push the label
            $year = $each_product_data['year']; // get the year

            if( 
                isset($dataset_by_year[$year]) && 
                isset($dataset_by_year[$year][$key]) && 
                isset($dataset_by_year[$year][$key]['count']) 
            ){
                $previous_count = intval($dataset_by_year[$year][$key]['count']);
                $dataset_by_year[$year][$key]['count'] = $previous_count + 1;
            }else{
                $dataset_by_year[$year][$key]['count'] = $previous_count + 1;
                $dataset_by_year[$year][$key]['label'] =  $each_product_data['canceled_by'];
            }
        }

    }// main structure_data loop end 



    
    
    // ---- Prepare dataset
    $final_datasets = [];

    foreach( $dataset_by_year as $year => $count_and_label  ){

        $dataset_data_arr = [];
        

        foreach( $labels_by_key as $label_key => $label_name ){
            if( isset($dataset_by_year[$year][$label_key]['count'] )  ){
                $dataset_data_arr[] = (string) $dataset_by_year[$year][$label_key]['count'];
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
    foreach( $labels_by_key as $label_key => $label_name ){
        $final_labels[] = $label_name;
    }

    return [
        'labels' => $final_labels,
        'datasets' => $final_datasets,
    ];


}