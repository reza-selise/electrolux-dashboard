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
    register_rest_route( $namespace, '/home-consultations-by-month', [
        'methods'             => 'POST',
        'callback'            => 'elux_get_home_consultations_by_month_data',
        'permission_callback' => '__return_true',
    ] );
} );


if( ! function_exists( 'elux_get_home_consultations_by_month_data' ) ){
    function elux_get_home_consultations_by_month_data( $ReqObj ){ 
        
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
                'status_code' => 400,
                'status' =>false,
                'data'  => [],
                'message' => "No valid input found, timeline_type, timeline_filter"
            ]);
        }


        // print_r($timeline_type,$timeline_filter);
        /// 1. ---------- Get product IDs
        $product_ids        = get_ORDERS_IDs_by_timeline_filter_from_event_start_meta( $timeline_type, $timeline_filter, $received_data );
        
        // print_r($product_ids);
 
        /// 2. ---------- Get Structure data along with post id
        $structure_data     = el_get_home_consultations_by_month_STRUCTURE_DATA($product_ids);
        print_r($structure_data);

        // print_r($structure_data);

        /// 3. ---------- Filter data
        $filtered_data      = el_FILTER_PRODUCTS_from_structure_data($structure_data, $received_data,['categories']);

        
        // 4. ---------- Get Final output
        $graph_data         = el_get_home_consultations_by_month_FINAL_DATA($structure_data, $received_data);
        
        print_r($graph_data);


        if($structure_data && $graph_data  && $table_data ){
            return rest_ensure_response( array(
                'status_code' => 200,
                'status'    => true,
                'message'   => 'Data fetch successful',
                'data'      => $graph_data,
                // 'table_data'=> $table_data,
                'graph_data'=> $graph_data
            ) );
        }else{
            return rest_ensure_response( array(
                'status_code' => 400,
                'status'        => false,                
                'message'   => 'No data found.',
                'dev_message'   => '$structure_data & $final_data receive failed to receive',
                'data'      => [],
            ) );
        }
        
    }
}



function el_get_home_consultations_by_month_STRUCTURE_DATA($product_ids){
    /**
     * This will use to serve structure for each product data along with post ids
     * 
     * return 
     * example output
    [
        1500 => [
            'year' => 2022,
            'month' => 01,
            filter_key_values = [
                key > value,
                key > value,
            ]
        ],

        1524 => [
            'year' => 2022,
            'month' => 01,
            filter_key_values = [
                key > value,
                key > value,
            ]
        ],
        
    ]
     * 
     * 
     */

    $structure_data = [];

    /// ------
    foreach( $product_ids as $single_product_id  ){
        
        // START --------- Event status (Taken place, planed, cancelled)
        $product_status     = get_post_meta( $single_product_id, 'product_status', true );
        
        // START --------- 
        $event_time_string  = (string) get_post_meta( $single_product_id, 'event_start_time', true );
        // var_dump($event_time_string);

        // get service type
        $order_service_type     = get_post_meta( $single_product_id, 'order_service_type', true );


        // only interested in "Home Consultation" ORDERS
        if(  
            sanitize_key( $order_service_type ) == sanitize_key( 'home-consultation' )   || 
            count($event_time_string) > 5
        ){


            // gather time value
            $event_year     =   substr($event_time_string,0,4) ; 
            $event_month    =   substr($event_time_string,5,2)  ;
            $event_date     =   substr($event_time_string,8,2)  ;

            $structure_data[$single_product_id]['event_time'] = [
                'date' => $event_date,
                'month' => $event_month,
                'year' => $event_year,
            ];
            $structure_data[$single_product_id]['day']      = $event_date;
            $structure_data[$single_product_id]['month']    = $event_month;
            $structure_data[$single_product_id]['year']     = $event_year;
            // end time Gathering 

        }

        
        
        // add filtering info 
        $filter_arr = el_GET_PRODUCT_FILTER_VALUES($single_product_id);

        foreach( $filter_arr as $filter_key => $filter_value ){
            $structure_data[$single_product_id]['filter_key_values'][$filter_key] = $filter_value;
        }



    } // for loop end

    return $structure_data;
}




function el_get_home_consultations_by_month_FINAL_DATA($structure_data, $requestData){

    $labels_by_month = [
        "01" => "Jan",
        "02" => "Feb",
        "03" => "Mar",
        "04" => "Apr",
        "05" => "May",
        "06" => "Jun",
        "07" => "Jul",
        "08" => "Aug",
        "09" => "Sep",
        "10" => "Oct",
        "11" => "Nov",
        "12" => "Dec",
    ];

    // get dataset by year
    $dataset_by_year    = [];

    // Loop through all the products and store the cat id 
    foreach( $structure_data as $single_product_id =>  $each_product_data  ){

        if( isset($each_product_data['year']) ){

            $year = sanitize_key( $each_product_data['year'] );
            $saved_month_number = sanitize_key($each_product_data['month']);
            
            // loop through all the month
            foreach( $labels_by_month as $month_number => $month_name ){

                $loop_month_number = sanitize_key( $month_number );

                
                if( $loop_month_number == $saved_month_number ){

                    if( isset($dataset_by_year[$year][$loop_month_number]) ){

                        $previous_count = $dataset_by_year[$year][$loop_month_number];
                        $dataset_by_year[$year][$loop_month_number] = $previous_count + 1 ;

                    }else{
                        $dataset_by_year[$year][$loop_month_number] = 1 ;
                    }

                }else{
                    $dataset_by_year[$year][$loop_month_number] = 0 ;
                }

            }
        }
    }// main structure_data loop end 

    // print_r($dataset_by_year);

    $final_datasets = [];
    foreach($dataset_by_year as $year => $months_arr_with_count ){
        $each_data_set = [
            "label" => $year,
            "data"  => []
        ];
        foreach( $months_arr_with_count as $month_number => $count ){
            $each_data_set['data'][] = $count;
        }
        $final_datasets[] = $each_data_set;
    }

    $final_labels = [];
    foreach($labels_by_month as $month_number => $month_name ){
        $final_labels[] = $month_name;
    }

    
    
    return [
        'labels' => $final_labels,
        'datasets' => $final_datasets,
    ];

}



function el_get_home_consultations_by_month_TABLE_FINAL_DATA($structure_data, $requestData){
    
    $labels = []; // store all the years


    foreach($structure_data  as $id =>  $item){
        $year = $item['event_time']['year'];
        if(!in_array($year, $labels)){
            $labels[] = $year;
        }
    }


    /*

        [cat_id] =>{
            label : category name,
            year_data : {
                2022 : 5
                2021 : 5
            }
        }

        [658] =>{
            label : Fish,
            total : 6546546
            year_data : {
                2022 : 54
                2021 : 589
            }
        }

    */

    // count the data 
    $data_by_category_and_year = [];
    
    foreach($structure_data  as $id =>  $product_data){



        $category_list  = $product_data['category'];
        $event_year  = $product_data['event_time']['year'];

        foreach($category_list as $cat_id => $cat_name){

            if( !isset($data_by_category_and_year[$cat_id]) ){
                $data_by_category_and_year[$cat_id]['label'] = $cat_name;
            }


            if( $requestData['type'] == 'participants' ){

                $previous_count =  0;

                if( isset($data_by_category_and_year[$cat_id]['year_data'][$event_year])) {
                    $previous_count = intval( $data_by_category_and_year[$cat_id]['year_data'][$event_year]);
                }

                $this_product_sell_count =  intval($product_data['total_sales']);

                $data_by_category_and_year[$cat_id]['year_data'][$event_year] = $previous_count + $this_product_sell_count;


                
            }else{
                $previous_count = 0;

                if( isset($data_by_category_and_year[$cat_id]['year_data'][$event_year])) {
                    $previous_count = $data_by_category_and_year[$cat_id]['year_data'][$event_year];
                }
    
                $data_by_category_and_year[$cat_id]['year_data'][$event_year] = $previous_count + 1;
            }


            

        }
        
    }

    // Calculate total 
    foreach($data_by_category_and_year as $cat_id => $item){
        $total = 0; 

        $year_data = $data_by_category_and_year[$cat_id]['year_data'];

        foreach($year_data as $year => $year_value ){
            $total = $total + intval($year_value);
        }

        $data_by_category_and_year[$cat_id]['total'] = $total;

    }




    $table_rows = [];

    // print_r($data_by_category_and_year);
    foreach($data_by_category_and_year as $cat_id => $cat_arr ){


        $each_table_row = [ $cat_arr['label'] ];


        // loop through all the label and append the data RESPECTIVELY (in order)
        foreach( $labels as $year ){

            
            // check if year data exist in the array or not 
            if( isset( $cat_arr['year_data'][$year] ) ){
                $number_to_push = $cat_arr['year_data'][$year];
            }else{
                $number_to_push = 0;
            }

            $each_table_row[] = $number_to_push;

        }
        // add last item 
        $each_table_row[] = $cat_arr['total'];

        $table_rows[] = $each_table_row;

    }

    
    $labels[] = "Total";
    array_unshift($labels , "Years");


    return [
        'labels'    => $labels,
        'rows'      => $table_rows
    ];
}





/**
 * get oder IDS from event_start_time
 * 
 * 
 */

function get_ORDERS_IDs_by_timeline_filter_from_event_start_meta( $timeline_type , $timeline_filter, $received_data =[] ){

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
        $query_ids = get_order_ids_by_range_from_event_start_time_meta( $single_query_arr[0],$single_query_arr[1] );
        // var_dump($single_query_arr);
        foreach($query_ids as $p_id){
            $final_ids[]=$p_id;
        }
    }


    $unique_ids =  array_unique($final_ids);

    return $unique_ids;


}

/** 
 * 
 * Get order IDS by range from event_start_time meta
 * 
 */


 function get_order_ids_by_range_from_event_start_time_meta( $start_range, $end_range ){
    
    
    /*
        modify start and end range Because in Database it store as
        YYYY-MM-DD HH:MM:SS
        2023-01-25 11:00:00

        received : 20221231
    */ 

    if( strlen($start_range) == 8 &&  strlen($start_range) == 8 ){
        $start_year = substr($start_range,0, 4);
        $start_month = substr($start_range,4,2);
        $start_date = substr($start_range,6,2);

        // push the final version 
        $start_range = $start_year ."-". $start_month."-". $start_date ." ". "00:00:00";

        $end_year = substr($end_range,0, 4);
        $end_month = substr($end_range,4,2);
        $end_date = substr($end_range,6,2);

        // update the final variable 
        $end_range = $end_year ."-". $end_month."-". $end_date . " ". "23:59:59";

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
        $wpdb->postmeta.meta_key = 'event_start_time' AND  $wpdb->postmeta.meta_value BETWEEN '%s' AND '%s'
    ";
    $response   = $wpdb->get_results( $wpdb->prepare( $query, $start_range, $end_range ) );

    return el_get_id_from_response($response);

}