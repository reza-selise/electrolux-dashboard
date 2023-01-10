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
        print_r($structure_data);

        // // print_r( count( $structure_data));

        // /// 3. ---------- Filter data
        // $filtered_data      = el_FILTER_PRODUCTS_from_structure_data($structure_data, $received_data,['categories']);

        
        // // 4. ---------- Get Final output
        // $graph_data         = el_get_home_consultations_by_month_FINAL_DATA($structure_data, $received_data);


        // $table_data         =   el_get_home_consultations_by_month_TABLE_FINAL_DATA($structure_data, $received_data);

        


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
        
        // only interest in consultation
        if( 
            $order_service_type == 'onsite-consultation' ||
            $order_service_type == 'live-consultation' 
        ){

            $each_structure_data = [];
            $order = wc_get_order($order_id);

            // location
            
            // $event_location_id =  $order->get_meta('event_location');

            // $structure_data[$order_id]['location'] = $event_location_id;

            $event_location_id = get_post_meta( $order_id, 'event_location', true );
            if($event_location_id){
                $each_structure_data['location'][$event_location_id] = get_the_title( $event_location_id ) ;
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

