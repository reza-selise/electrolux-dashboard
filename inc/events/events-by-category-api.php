<?php 
/**
 * EVENT BY CATEGORY API
 * Overview of Event category
 * 
 * filter parameter list
 * 
 * 
 * 
    
    JSON BODY
    {
        taken_place:    [planed,cancelled, etc],
        type:           event/participant ,

        timeline_type : 'month|year|custom_date_range|custom_time_frame'
        
        timeline_filter : 
            [01,02,03,04,05] // for month
            [2020,2022,2021] // for year

            [ [ 20200101, 20201231  ] [ 20210101, 20211231  ] ], //custom_date_range

            custom_time_frame : {
                count: 5,
                type : year|month
            }

    }
 * 
 * lo
 * 
 */


// die();
add_action( 'rest_api_init', function () {
    $namespace = 'elux-dashboard/v1';
    register_rest_route( $namespace, '/events-by-category', [
        'methods'             => 'POST',
        'callback'            => 'elux_get_events_by_category',
        // 'permission_callback' => '__return_true',
    ] );
} );


if( ! function_exists( 'elux_get_events_by_category' ) ){
    function elux_get_events_by_category( $ReqObj ){ 
        
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
        $product_ids               = get_products_by_timeline_filter( $timeline_type,$timeline_filter,$received_data );

        /// 2. ---------- Get Structure data along with post id
        $structure_data         = el_events_by_category_STRUCTURE_DATA($product_ids);
        // print_r($structure_data);

        /// 3. ---------- PASS ONLY FILTERED STRUCTURE DATA
        $filtered_data          = el_FILTER_PRODUCTS_from_structure_data($structure_data, $received_data);

        // print_r($filtered_data);
        /// 4. ---------- Get Final output
        $final_data             = el_events_by_category_FINAL_DATA($filtered_data, $received_data);



        if($structure_data && $final_data ){
            return rest_ensure_response( array(
                'status'    => true,
                'message'   => 'Data fetch successful',
                'data'      => $final_data,
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


// this function will output the final data to 
function el_events_by_category_FINAL_DATA($structure_data, $requestData){
    $all_labels             = [] ;  // to hold x-axis data 
    $category_list_unique   = [];    // [ id => 'name' , 51 => steam-demo ]

    


    /*
        $all_data_sheet     structure

        "2022": {
            "label": "2022",
            "category_data": {
                "15": {
                    "name": "Steamdemo",
                    "count": 1
                }
                "16": {
                    "name": "Cooking class",
                    "count": 1
                }
            }
        },
        "2024": {
            "label": "2024",
            "category_data": {
                "15": {
                    "name": "Steamdemo",
                    "count": 3
                },
                "53": {
                    "name": "Bread rolls, braid and Co",
                    "count": 1
                }
            }
        }

    */
    $all_data_sheet     = [] ;  // to hold y-axis data



    // store the All Label(x-axis) and  dataSheet(y-axis) info
    foreach( $structure_data as $single_product_id =>  $each_product_data  ){

        // make label 
        foreach( $each_product_data['category'] as $id => $cat_name ){
            $all_labels[$id] = $cat_name;
        }
        
        $year   = $each_product_data['event_time']['year'];
        if(!in_array($year, $all_data_sheet)){
            $k = sanitize_key( $year );
            $all_data_sheet[$k]['label'] = $year;
        }

    }// MAIN LOOP END


    // store all category data  with COUNTING
    foreach( $structure_data as $single_product_id =>  $each_product_data  ){

        $year   = $each_product_data['event_time']['year'];

        $k = sanitize_key( $year );

        foreach( $each_product_data['category'] as $id => $cat_name ){

            // push the category name
            $all_data_sheet[$year]['category_data'][$id]['name'] = $cat_name;

            // pushing the category count
            if(isset($all_data_sheet[$year]['category_data'][$id]['count'])){

                if( $requestData['type'] == 'participants' ){
                    $previous_count = intval($all_data_sheet[$year]['category_data'][$id]['count'] ) ;
                    $all_data_sheet[$year]['category_data'][$id]['count'] = $previous_count + $each_product_data['total_sales'] ;
                }else{

                    
                    $previous_count = intval( $all_data_sheet[$year]['category_data'][$id]['count'])  ;
                    
                    $all_data_sheet[$year]['category_data'][$id]['count'] = $previous_count + 1;
                }

            }else{
                if( $requestData['type'] == 'participants' ){
                    $all_data_sheet[$year]['category_data'][$id]['count'] = intval( $each_product_data['total_sales']);
                }else{
                    $all_data_sheet[$year]['category_data'][$id]['count'] = 1;
                }
            }
        }
    }    







    // prepare final label 
    $final_label = [];
    foreach($all_labels as $cat_id => $cat_name){
        $final_label[] = $cat_name;
    }




    // Prepare dataset variable
    /*
    [year] => [
        'label' => Year Name
        'data'  => [20,10,3]  // number will be depend on all label
    ],
    [year] => [
        'label' => Year Name
        'data'  => [20,10,3]  // number will be depend on all label
    ]
    */
    $final_data_sheet_by_year = [];

    

    foreach($all_data_sheet as $dataSheetYear => $dataSheetItem ){
        /*
        dataSheetItem:
        "2022": {
            "label": "2022",
            "category_data": {
                "15": {
                    "name": "Steamdemo",
                    "count": 1
                }
            }
        },
        */

        $category_arr = $dataSheetItem['category_data'];
        $yearlyDataSheet = [];

        foreach($all_labels as $cat_id => $cat_name){
            if( isset($category_arr[$cat_id]) ){
                $yearlyDataSheet[] = $category_arr[$cat_id]['count'];
            }else{
                $yearlyDataSheet[] = "0";
            }
        }
        $final_data_sheet_by_year[$dataSheetYear]['label'] = $dataSheetItem['label'];
        $final_data_sheet_by_year[$dataSheetYear]['data']  = $yearlyDataSheet;
    }

    // format dataset
    $final_data_set = [];
    foreach( $final_data_sheet_by_year as $year => $each_dataset ){
        $final_data_set[] = $each_dataset;
    }


    return [
        'labels'    => $final_label,
        'dataset'   => $final_data_set
    ];
}






function el_events_by_category_STRUCTURE_DATA($product_ids){
    /**
     * This will use to serve structure each product data data along with post ids
     * 
     * return 
     * 
     * example output
    [
        1500 => [
            'year' => 2022,
            'month' => 01,
            'category' => [ cat_id =>  cat_name , cat_id =>  cat_name ]
            'filter_name' => filter_value
            'attribute_name' => attribute_value
            'filter_name_2' => filter_value ,
            "filter_key_values" => {
                key : value to match,
                key : value to match,
            }
        ],
        1496 => [
            'year' => 2021,
            'month' => 12,
            "filter_key_values" => {
                key : value to match,
                key : value to match,
            }
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
        $structure_data[$single_product_id]['total_sales'] = $total_sales;



        if( $product_status ){
            $structure_data[$single_product_id]['product_status'] = $product_status;
        }else{
            $structure_data[$single_product_id]['product_status'] = 'false';
        }
        // END ------------ Event status (Taken place, planed, cancelled)
        

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

        // START ---  CATEGORY ADDING
        $product_cats       = get_the_terms( $single_product_id , 'product_cat' );
        $each_product_category_arr = []; // use to store all the category along with post id 
        foreach( $product_cats as $cat){
            // $cat_id     =  $cat->term_id;
            $cat_id     =  sanitize_key( $cat->name ); // because we want to count if name is same
            $cat_name   = $cat->name;

            // store unique category
            if( !isset($category_list_unique[$cat_id] ) ){
                $category_list_unique[$cat_id] = $cat_name;
            }

            // push each category in a array
            $each_product_category_arr[$cat_id] = $cat_name;

        }
        $structure_data[$single_product_id]['category'] = $each_product_category_arr;
        // $structure_data[$single_product_id]['category'] = [];
        // END ------------ CATEGORY ADDING




        // add filtering info 
        $filter_arr = el_GET_PRODUCT_FILTER_VALUES($single_product_id);

        foreach( $filter_arr as $filter_key => $filter_value ){
            $structure_data[$single_product_id]['filter_key_values'][$filter_key] = $filter_value;
        }

    } // for loop end

    // $poly = pll_get_term(15, 'fr_FR');

    // var_dump($poly);

    // print_r($structure_data);
    return $structure_data;
}
