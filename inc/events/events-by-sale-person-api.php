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
    register_rest_route( $namespace, '/events-by-sale-person', [
        'methods'             => 'POST',
        'callback'            => 'elux_get_events_by_salesperson',
        'permission_callback' => '__return_true',
    ] );
} );


if( ! function_exists( 'elux_get_events_by_salesperson' ) ){
    function elux_get_events_by_salesperson( $ReqObj ){ 
        
        
        // this will use to store all relevant information along with post id
        $structure_data = [];


        // extract json data from request
        $received_data = $ReqObj->get_json_params();

        $timeline_type      = false;
        $timeline_filter    = false;


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
        $product_ids        = get_products_by_timeline_filter( $timeline_type,$timeline_filter, $received_data);

        /// 2. ---------- Get Structure data along with post id
        $structure_data     = el_events_by_salesperson_STRUCTURE_DATA($product_ids);


        /// 3. ---------- Filter data
        $filtered_data      = el_FILTER_PRODUCTS_from_structure_data($structure_data, $received_data);

        
        /// 4. ---------- Get Final output
        $final_data         = el_events_salesperson_FINAL_DATA($filtered_data, $received_data);


        if($structure_data && $final_data ){
            return rest_ensure_response( array(
                'status'    => true,
                'message'   => 'Data fetch successful',
                'data'      => $final_data,
            ) );
        }else{
            return rest_ensure_response( array(
                'status'        => false,                
                'message'       => 'No data found.',
                'dev_message'   => '$structure_data & $final_data receive failed to receive',
                'data'          => [],
            ) );
        }
        
    }
}



function el_events_by_salesperson_STRUCTURE_DATA($product_ids){
    /**
     * This will use to serve structure for each product data along with post ids
     * 
     * return 
     * example output
      *  [
      *       1500 => [
      *           'year' => 2022,
       *          'month' => 01,
        *         
        *        'person_ids'=[], // important 
     *            
       *          'category' => [ cat_id =>  cat_name , cat_id =>  cat_name ]
      *           'filter_name' => filter_value

       *          'attribute_name' => attribute_value
        *         'filter_name_2' => filter_value 
       *      ],
        *     1496 => [
      *          'year' => 2021,
       *          'month' => 12,
       *          'filter_name' => filter_value
         *        'attribute_name' => attribute_value
        *         'filter_name_2' => filter_value 
        *     ]
      *   ]
     * 
     * 
     */

    $structure_data = [];

    /// ------
    foreach( $product_ids as $single_product_id  ){
        
        


        // One event can be assign to multiple sale person id 

        // Sales person name
        $salesperson_id_1     = get_post_meta( $single_product_id, 'salesperson-1', true );
        $salesperson_id_2     = get_post_meta( $single_product_id, 'salesperson-2', true );
        $salesperson_id_3     = get_post_meta( $single_product_id, 'salesperson-3', true );

        $sales_person_arr = [];

        if(  intval($salesperson_id_1)  ){ 
            $sales_person_arr[] = $salesperson_id_1;
        }

        if(  intval($salesperson_id_2)  ){ 
            $sales_person_arr[] = $salesperson_id_2;
        }

        if(  intval($salesperson_id_3)  ){ 
            $sales_person_arr[] = $salesperson_id_3;
        }

        
        $structure_data[$single_product_id]['sales_persons_ids'] = $sales_person_arr;

        /// --- END sales person

        $event_time_string  = (string) get_post_meta( $single_product_id, 'date', true );

        // $structure_data[$single_product_id]['name'] = $salesperson_name;
        

        if( is_string($event_time_string) && trim($event_time_string) && strlen($event_time_string) === 8 ){
            
            $event_year     =   substr($event_time_string,0,4) ; 
            $event_month    =   substr($event_time_string,4,2)  ;
            $event_date     =   substr($event_time_string,6,2)  ;

            $structure_data[$single_product_id]['event_time'] = [
                'date' => $event_date,
                'month' => $event_month,
                'year' => $event_year,
            ];

            $structure_data[$single_product_id]['year'] = $event_year;

        }else{
            $structure_data[$single_product_id]['event_time'] = 'false';
        }
        // END --------- Event time 


        // START ---  For Cooking course type ADDING
        $each_product_category_arr = [];

        
        $product_cats       = get_the_terms( $single_product_id , 'product_cat' );

        foreach( $product_cats as $cat){

            // we only interest in Cooking class sub category
            $cat_id     = $cat->term_id;
            $cat_name   = $cat->name;
            // push each category in a array
            $each_product_category_arr[$cat_id] = $cat_name;

        }
        $structure_data[$single_product_id]['category'] = $each_product_category_arr;
        // END ------------ CATEGORY ADDING

    } // for loop end

    // print_r($structure_data);

    return $structure_data;
}





function el_events_salesperson_FINAL_DATA($structure_data, $requestData){


    // make table header
    /*  
        2020 => 2022,
        2020 => 2022,
        2020 => 2022,
    */
    $table_years_columns = [];


    /*
        user_id = [
            'name' => name
            'years' => [
                2021 => 5
                2021 => 6
                2021 => 7
            ]
        ]
        $table_rows_by_sale_person_id[$person_user_id]['years'][$2]

    */

    $table_rows_by_sale_person_id = [];


    // Loop through all the products and store year count
    foreach( $structure_data as $single_product_id =>  $each_product_data  ){

        // when sales persons ids set
        if( 
            isset($each_product_data['sales_persons_ids']) &&
            count($each_product_data['sales_persons_ids']) > 0 &&

            isset($each_product_data['year']) &&
            !empty($each_product_data['year']) 
        ){

            $current_product_year = $each_product_data['year'];
            
            $table_years_columns[$current_product_year] = $current_product_year;
            
            $sales_person_ids = $each_product_data['sales_persons_ids'];
            
            foreach( $sales_person_ids as $inx => $person_id  ){
                
                if( isset($table_rows_by_sale_person_id[$person_id]) &&  $table_rows_by_sale_person_id[$person_id]['years'][$current_product_year]  ){
                    $previous_count = intval( $table_rows_by_sale_person_id[$person_id]['years'][$current_product_year]);
                }else{
                    $previous_count = 0;
                }
                $table_rows_by_sale_person_id[$person_id]['years'][$current_product_year] = $previous_count + 1;
                
                
                $current_user   = get_user_by( 'id', $person_id );
                $person_name    = $current_user->first_name . " " .$current_user->last_name ; 
                $table_rows_by_sale_person_id[$person_id]['name'] = $person_name;
            }


        } // only interest if sales person set

    }// main structure_data loop end 

    
    // prepare the table row

    // print_r($table_rows_by_sale_person_id);
   
    $table_rows = [];

    foreach( $table_rows_by_sale_person_id as $person_id => $year_arr ){

        $person_info = $table_rows_by_sale_person_id[$person_id];
        

        $each_row = [];

        // first item is name
        $each_row[] = $person_info['name'];

        $total_count = 0;

        $current_user = get_user_by( 'id', $person_id );
        $person_years = $person_info['years'];


        foreach($table_years_columns as $year_key => $year_text ){
            if( isset($person_years[$year_key]) && !empty($person_years[$year_key]) ){
                $each_row[] = $person_years[$year_key];

                $total_count = $total_count + $person_years[$year_key];
            }else{
                $each_row[] = "0";
            }
        }


        $each_row[] = $total_count;

        
        $table_rows[] = $each_row;

    }

    // modify the year columns
    $table_years_columns[] = 'Total';
    array_unshift($table_years_columns, "Sales Person");


    return [
        'table_header' => $table_years_columns,
        'table_rows' => $table_rows,
    ];


}