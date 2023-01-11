<?php 
add_action( 'rest_api_init', function () {
    $namespace = 'elux-dashboard/v1';
    register_rest_route( $namespace, '/consultations-per-taste-gallery-by-month', [
        'methods'             => 'POST',
        'callback'            => 'elux_get_consultation_per_taste_gallery_by_month',
        'permission_callback' => '__return_true',
    ] );
} );


if( ! function_exists( 'elux_get_consultation_per_taste_gallery_by_month' ) ){
    function elux_get_consultation_per_taste_gallery_by_month( $ReqObj ){ 
        
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
        $graph_data         = el_get_consultation_per_taste_gallery_by_month_GRAPH_FINAL_DATA($structure_data, $received_data);

        print_r($graph_data);

        // $table_data         =   el_get_consultation_per_taste_gallery_by_year_TABLE_FINAL_DATA($structure_data, $received_data);

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


function el_get_consultation_per_taste_gallery_by_month_GRAPH_FINAL_DATA($structure_data, $received_data){
    $labels = [
        "01","02","03","04","05","06","07","08","09","10","11","12"
    ];

    $labels_months = [
        "Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"
    ];

    $dataset_by_location = [];
    foreach($structure_data as $order_id => $order_data ){

        $location_slug = $order_data['location_slug'];
        $location_name = $order_data['location_name'];

        $month = $order_data['month'];

        if( isset($dataset_by_location[$location_slug][$month]) ){
            $previous_count = intval($dataset_by_location[$location_slug][$month]);
            $dataset_by_location[$location_slug][$month] = $previous_count + 1;
        }else{
            $dataset_by_location[$location_slug][$month] =  1;
        }
        $dataset_by_location[$location_slug]['name'] =  $location_name;

    }

    // prepare final data set 
    $final_dataset = [];

    
    foreach($dataset_by_location as $location_slug => $month_list_arr ){

        $each_data_set = ['label' => $location_slug];
        $each_data_set['name'] =  $month_list_arr['name'];

        // to match with the order loop all the labels and push data 
        foreach($labels as $month_number){

            if(isset($month_list_arr[$month_number])){
                $each_data_set['data'][] = $month_list_arr[$month_number];
            }else{
                $each_data_set['data'][] = "0";
            }
        }

        $final_dataset[] = $each_data_set;

    }

    return [
        "labels"    => $labels_months,
        "dataset"   => $final_dataset,
    ];

    

}