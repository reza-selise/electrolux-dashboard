<?php 
add_action( 'rest_api_init', function () {
    $namespace = 'elux-dashboard/v1';
    register_rest_route( $namespace, '/consultations-per-taste-gallery-by-year', [
        'methods'             => 'POST',
        'callback'            => 'elux_get_consultation_per_taste_gallery_data',
        'permission_callback' => '__return_true',
    ] );
} );


if( ! function_exists( 'elux_get_consultation_per_taste_gallery_data' ) ){
    function elux_get_consultation_per_taste_gallery_data( $ReqObj ){ 
        
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
        $order_ids        = get_ORDERS_IDs_by_timeline_filter_from_event_start_meta( $timeline_type, $timeline_filter, $received_data );
        
        
        // /// 2. ---------- Get Structure data along with post id
        $structure_data     = el_get_consultations_per_taste_gallery_by_year_STRUCTURE_DATA($order_ids, $received_data);
        // print_r($structure_data);


        // /// 3. ---------- Filter data
        // $filtered_data      = el_FILTER_PRODUCTS_from_structure_data($structure_data, $received_data,['categories']);

        
        // // 4. ---------- Get Final output
        $graph_data         = el_get_consultation_per_taste_gallery_by_year_GRAPH_FINAL_DATA($structure_data, $received_data);

        // print_r($graph_data);

        $table_data         =   el_get_consultation_per_taste_gallery_by_year_TABLE_FINAL_DATA($structure_data, $received_data);

        // print_r($table_data);


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


function el_get_consultations_per_taste_gallery_by_year_STRUCTURE_DATA($order_ids, $received_data){
   
    $structure_data = [];

    foreach($order_ids as $order_id){
        
        $order_service_type = get_post_meta( $order_id, 'order_service_type', true );
        $event_location_id = get_post_meta( $order_id, 'event_location', true );
        
        // only interest in consultation and if valid event id assign
        if( 
            (
                $order_service_type == 'onsite-consultation' ||
                $order_service_type == 'live-consultation' 
            ) &&
            intval($event_location_id) > 0
        ){

            $each_structure_data = [];
            $order = wc_get_order($order_id);


            if($event_location_id){
                $each_structure_data['location_id'] = $event_location_id  ;
                $each_structure_data['location_name'] =  get_the_title(  $event_location_id  );
                $each_structure_data['location_slug'] = sanitize_key(  get_the_title( $event_location_id ) );
            }

            $event_time_string  = (string) get_post_meta( $order_id, 'event_start_time', true );

            if( $event_time_string ){

                $event_year     =   substr($event_time_string,0,4) ; 
                $event_month    =   substr($event_time_string,5,2)  ;
                $event_date     =   substr($event_time_string,8,2)  ;

                $each_structure_data['day']      = $event_date;
                $each_structure_data['month']    = $event_month;
                $each_structure_data['year']     = intval($event_year);
            }



            // loop through all the product in the order 
            $order_items =$order->get_items();

            foreach ( $order_items as $item_id => $item ) {

                $product_id = $item->get_product_id();

                // Customer type
                $customer_type =  get_post_meta( $product_id, 'customer_type', true );
                if($customer_type){
                    $each_structure_data['customer_type'] = $customer_type;
                }

                $location       = (int) get_post_meta( $product_id, 'event_location', true );

                if($location){
                    if(isset($output['location'])) {
                        $each_structure_data['location'][$location] = get_the_title( $location );
                    }else{
                        $each_structure_data['location'] = [];
                        $each_structure_data['location'][$location] = get_the_title( $location );
                    }
                }
            }

            // get the filter key values
            $filter_key_values = el_get_each_consultation_FILTER_KEY_VALUES_ARR($order_id);

            $structure_data[$order_id] = $each_structure_data;
            $structure_data[$order_id]['filter_key_values'] = $filter_key_values;
            
        } // consulatation checking end 


    }

    return $structure_data;
}



function el_get_consultation_per_taste_gallery_by_year_GRAPH_FINAL_DATA($structure_data,$received_data ){

    /*
    dataset_by_location [
        location_slug => [
            2008 => 1,
            2009 => 5,
        ],
        location_slug_2 => [
            2008 => 1,
            2009 => 5,
        ]
    ] 
    */

    $dataset_by_location = [];
    $final_labels = [ ]; // List of years

    foreach($structure_data as $order_id => $order_data ){

        $location_slug = $order_data['location_slug'];
        $year = $order_data['year'];

        if( isset($dataset_by_location[$location_slug][$year]) ){
            $previous_count = intval($dataset_by_location[$location_slug][$year]);
            $dataset_by_location[$location_slug][$year] = $previous_count + 1;
        }else{
            $dataset_by_location[$location_slug][$year] =  1;
        }

        // push the label/Year if not found
        if(!in_array($year, $final_labels)){
            $final_labels[] = $year;
        }

    }

    // prepare final data set 
    $final_dataset = [];

    foreach($dataset_by_location as $location_slug => $year_list_arr ){
        $each_data_set = ['label' => $location_slug];

        // to match with the order loop all the labels and push data 
        foreach($final_labels as $year){

            if(isset($year_list_arr[$year])){
                $each_data_set['data'][] = $year_list_arr[$year];
            }else{
                $each_data_set['data'][] = "0";
            }
        }

        $final_dataset[] = $each_data_set;

    }

    return [
        'labels'    => $final_labels,
        'datasets'  => $final_dataset
    ];

}

function el_get_consultation_per_taste_gallery_by_year_TABLE_FINAL_DATA($structure_data, $received_data){
    
    /*
    dataset_by_location [
        location_slug_2 => [
            2008 => [
                b2b => 5,
                b2C => 9,
            ],
            2004 => [
                b2b => 5,
                b2C => 9,
            ],
        ]
    ] 
    */

    $dataset_by_location = [];
    $dataset_by_year = [];
    $final_labels = [ ]; // List of years

    $unique_customer_type   = [];
    $unique_location_name   = [];
    $unique_years           = [] ; 

    foreach($structure_data as $order_id => $order_data ){

        $location_slug = $order_data['location_slug'];
        $location_name = $order_data['location_name'];


        $year = $order_data['year'];
        $customer_type = '';
        if( isset($order_data['customer_type']) ){
            $customer_type = trim( sanitize_key(  $order_data['customer_type'] ));
        }

        if( isset($dataset_by_location[$location_slug][$year][$customer_type]) ){
            $previous_count = intval($dataset_by_location[$location_slug][$year][$customer_type]);
            $dataset_by_location[$location_slug][$year][$customer_type] = $previous_count + 1;
        }else{
            $dataset_by_location[$location_slug][$year][$customer_type] = 1;
        }

        $dataset_by_location[$location_slug]['location_name'] = $location_name;

        // push unique customer type
        if( $customer_type && !in_array($customer_type, $unique_customer_type) ){
            $unique_customer_type[] = $customer_type;
        }

        // push unique location name
        if( !in_array($location_name, $unique_location_name) ){
            $unique_location_name[] = $location_name;
        }

        // push unique year
        if( !in_array($year, $unique_years) ){
            $unique_years[] = $year;
        }


        // dataset by year count
        if( isset($dataset_by_year[$year]) ){
            $previous_count = intval($dataset_by_year[$year]);
            $dataset_by_year[$year] = $previous_count + 1;
        }else{
            $dataset_by_year[$year] = 1;
        }
        // dataset by year count end 

    }

    $final_rows=[];;
    // prepare each row
    foreach($dataset_by_location as $location_slug => $dataset_item){
        $each_row = [];

        $row_total = 0;

        $each_row[] = $dataset_item['location_name'];

        // loop through all the unique years
        foreach($unique_years as $year){
            
            // customer type
            foreach( $unique_customer_type as $customer_type ){

                if(isset($dataset_item[$year][$customer_type])){
                    $each_row[] = $dataset_item[$year][$customer_type];
                    $row_total = $row_total + intval($dataset_item[$year][$customer_type]);
                }else{
                    $each_row[] = "0";
                }
            }
        }

        $each_row[] = $row_total;
        $final_rows[] = $each_row;
        
    }

    // LAST ROW
    $last_row = ["Total"];
    $last_total = 0;
    foreach($unique_years as $year){

        if(isset($dataset_by_year[$year])){
            $last_row[] = intval( $dataset_by_year[$year]);
            $last_total = $last_total + intval($dataset_by_year[$year]) ;

        }else{
            $last_row[] = '0';
        }
    }
    $last_row[] = $last_total ;
    $final_rows[] = $last_row;
    // --- last row end

    

    foreach($unique_years as $year){
            
        // customer type
        foreach( $unique_customer_type as $customer_type ){

            if(isset($dataset_item[$year][$customer_type])){
                $each_row[] = $dataset_item[$year][$customer_type];
                $row_total = $row_total + intval($dataset_item[$year][$customer_type]);
            }else{
                $each_row[] = "0";
            }
        }
    }





    return [

        'unique_customer_type' => $unique_customer_type,
        'years' => $unique_years,
        "rows" => $final_rows
    ];



}