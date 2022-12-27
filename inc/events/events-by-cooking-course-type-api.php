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
        $product_ids        = get_products_by_timeline_filter( $timeline_type,$timeline_filter, $received_data );

        /// 2. ---------- Get Structure data along with post id
        $structure_data     = el_events_by_cooking_course_type_STRUCTURE_DATA($product_ids);



        /// 3. ---------- Filter data
        $filtered_data      = el_FILTER_PRODUCTS_from_structure_data($structure_data, $received_data);

        
        // /// 4. ---------- Get Final output
        $graph_data         = el_events_by_cooking_course_type_FINAL_DATA($filtered_data, $received_data);

        $table_data         = el_event_by_cooking_course_TABLE_FINAL_DATA($filtered_data, $received_data);

        // print_r($table_data);


        if($structure_data && $graph_data  && $table_data ){
            return rest_ensure_response( array(
                'status'    => true,
                'message'   => 'Data fetch successful',
                'data'      => $graph_data,
                'table_data'=> $table_data,
                'graph_data'=> $graph_data
            ) );
        }else{
            return rest_ensure_response( array(
                'status'        => false,                
                'message'   => 'No data found.',
                'dev_message'   => '$structure_data & $final_data receive failed to receive',
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
            "total_sales" => 46
            'filter_name' => filter_value
            'attribute_name' => attribute_value
            'filter_name_2' => filter_value,
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

                // took year
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
                    if( $requestData['type'] == 'participants' ){
                        $dataset_by_year[$year][$cat_id]['count'] = intval($each_product_data['total_sales']) + $previous_count;
                    }else{
                        $dataset_by_year[$year][$cat_id]['count'] = $previous_count + 1;
                    }
                    

                }else{
                    if( $requestData['type'] == 'participants' ){
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



function el_event_by_cooking_course_TABLE_FINAL_DATA($structure_data, $requestData){
    
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