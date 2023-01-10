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

        // print_r( count( $structure_data));

        /// 3. ---------- Filter data
        $filtered_data      = el_FILTER_PRODUCTS_from_structure_data($structure_data, $received_data,['categories']);

        
        // 4. ---------- Get Final output
        $graph_data         = el_get_home_consultations_by_month_FINAL_DATA($structure_data, $received_data);


        $table_data         =   el_get_home_consultations_by_month_TABLE_FINAL_DATA($structure_data, $received_data);

        


        if($structure_data && $graph_data   ){
            return rest_ensure_response( array(
                'status_code' => 200,
                'status'    => true,
                'message'   => 'Data fetch successful',
                // 'data'      => $graph_data,
                'table_data'=> $table_data,
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
            sanitize_key( $order_service_type ) == sanitize_key( 'home-consultation' )
        ){


            // gather time value
            $event_year     =   substr($event_time_string,0,4) ; 
            $event_month    =   substr($event_time_string,5,2)  ;
            $event_date     =   substr($event_time_string,8,2)  ;

            // $structure_data[$single_product_id]['event_time'] = [
            //     'date' => $event_date,
            //     'month' => $event_month,
            //     'year' => $event_year,
            // ];
            $structure_data[$single_product_id]['day']      = $event_date;
            $structure_data[$single_product_id]['month']    = $event_month;
            $structure_data[$single_product_id]['year']     = $event_year;
            // end time Gathering 

        }

        
        
        // add filtering info 
        $filter_arr = el_GET_ORDER_FILTER_VALUES($single_product_id);

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
    $dataset_by_year    = el_get_home_consultations_dataset_by_year($structure_data);


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
    $dataset_by_year    = el_get_home_consultations_dataset_by_year($structure_data);
    $rows = [];

    foreach( $dataset_by_year as $year => $month_arr ){
        $each_row = [$year];

        $row_total = 0;
        foreach($month_arr as $month_key => $count){
            $each_row[] = $count;
            $row_total = $row_total + intval($count);
        }
        
        $each_row[] = $row_total;
        $rows[] = $each_row;

    }


    // Gather data by month 
    $last_row_data_by_month = []; // [ "01" => 10 , "02" => 24,  "03" => 5, ... ]
    $final_total = 0;// total final for the month 
    

    foreach( $dataset_by_year as $year => $month_arr ){

        foreach($month_arr as $month_key => $count){
            
            if( isset($last_row_data_by_month[$month_key]) ){    

                $previous_count = $last_row_data_by_month[$month_key];
                $last_row_data_by_month[$month_key] = $previous_count +   intval($count) ;

            }else{
                $last_row_data_by_month[$month_key] = intval($count);
            }
            $final_total = $final_total + intval($count);
        }
    }

    $last_row = [];

    foreach($last_row_data_by_month as $month_key => $month_value){
        $last_row[] = $month_value;
    }

    $last_row[] = $final_total;
    array_unshift($last_row,"Total");


    $rows[] = $last_row;

    $labels = ["Year"];

    // prepare column

    foreach( $labels_by_month as $month_key => $month_name ){
        $labels[] = $month_name;
    }   
    $labels[] = "Total";


    return [
        'labels'    => $labels,
        'rows'      => $rows
    ];



}


/*
Get get dataset by year 

*/
function el_get_home_consultations_dataset_by_year($structure_data){
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

    // var_dump( "Total data found > ". count($structure_data));

    // Loop through all the products and store the cat id 
    foreach( $structure_data as $single_product_id =>  $each_product_data  ){

        if( isset($each_product_data['year']) ){

            $saved_year = sanitize_key($each_product_data['year'] );
            $saved_month = sanitize_key($each_product_data['month']);

            


            foreach( $labels_by_month as $loop_month_number => $loop_month_name ){


                if( 
                    isset($dataset_by_year[$saved_year][$loop_month_number])

                ){

                    if( $loop_month_number == $saved_month ){
                        $previous_count = $dataset_by_year[$saved_year][$loop_month_number] ;
                        $dataset_by_year[$saved_year][$loop_month_number] = $previous_count + 1 ;
                    }

                    
                }else{
                    
                    if( $loop_month_number == $saved_month ){

                        $dataset_by_year[$saved_year][$loop_month_number] =  1 ;

                    }else{
                        $dataset_by_year[$saved_year][$loop_month_number] = 0;
                    } 



                }


            }
        }
    }// main structure_data loop end 

    return $dataset_by_year;

}


// collect filter information which will use to do GLOBAL FILTERING 
// Category : https://docs.google.com/document/d/1nDTzBubjUGB20VPOThgd0Hoc3eeQb4UNFfHR6Ggse3Y/edit
function el_GET_ORDER_FILTER_VALUES($order_id){
    


    $output = [];

    $order = wc_get_order($order_id);

    $order_items =$order->get_items();

    foreach ( $order_items as $item_id => $item ) {
        $product_id = $item->get_product_id();

        $customer_type =  get_post_meta( $product_id, 'customer_type', true );

        if($customer_type){
            if(isset($output['customer_type'])) {
                $output['customer_type'][] = $customer_type;
            }else{
                $output['customer_type'] = [];
                $output['customer_type'][] = $customer_type;
            }
        }

        $location       = get_post_meta( $single_product_id, 'event_location', true );

        if($location){
            if(isset($output['location'])) {
                $output['location'][] = $location;
            }else{
                $output['location'] = [];
                $output['location'][] = $location;
            }
        }
        
    }




    $order_service_type = $order->get_meta('order_service_type');
    if($order_service_type){
        $output['category'] = $order_service_type;
    }
     
    return $output;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     
    

}
