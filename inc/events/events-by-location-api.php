<?php
//--- Maruf---

add_action( 'rest_api_init', function () {
    $namespace = 'elux-dashboard/v1';
    register_rest_route($namespace, '/events-by-location', array(
        'methods' => 'GET',
        'callback' => 'get_event_by_locations',
        'permission_callback' => '__return_true',
    ));
});

function get_event_by_locations($request){
    $event_status           = $request->get_params()['event_status']; // planned | cancelled etc. [event_status on product meta]
    $event_status       = explode( ',', $event_status );    
   
    $customer_type          = $request->get_params()['customer_type']; // b2b | b2c | electrolux_internal | all etc. [customer_type on product meta]
    $customer_type       = explode( ',', $customer_type ); 
   
    $booking_type           = $request->get_params()['booking_type']; // Booked Manually | Walk-in | Customer Booking [consultation_booking_type on order meta]
    $booking_type           = explode( ',', $booking_type ); 
    
    $sales_person_ids       = ! empty( $request->get_params()['salesperson'] ) ? $request->get_params()['salesperson'] : '';     // 7 | 8 | 9
    $sales_person_ids       = explode( ',', $sales_person_ids );     // user id [event_consultant on order meta]
    
    $categories             = ! empty( $request->get_params()['categories'] ) ? $request->get_params()['categories'] : '';     // 15 | 47 | 104
    $categories             = explode( ',', $categories );  // category ids [product category on product]
    $fb_leads               = $request->get_params()['fb_leads']; //product_event_consultant [on product meta]
    $fb_leads             = explode( ',', $fb_leads );
    // FB lead = consultant lead on product edit page

    $request_data =  $request->get_params()['request_data']; // events,participants 
    $filter_type =  $request->get_params()['filter_type'];
    $request_body =  $request->get_params()['request_body'];
    if(!empty($request_body) && 'string' == gettype($request_body)){
        $request_body = json_decode($request->get_params()['request_body'],true);
    }
    $gallery_locations = $request->get_params()['locations'];
    $gallery_locations_arr =  explode(',',$gallery_locations);
    $response               = array(
        "type"  => $request_data
    );

  
    $my_returned_data = array();
    $my_years = array();
    foreach($gallery_locations_arr as $gallery_location){
        $my_data = array(
            "location"  => get_the_title($gallery_location),
            "total" => 0, 
        );
        if(!empty($request_body)){
            // by months and year
            if($filter_type == 'months' || $filter_type == 'years'){
                foreach( $request_body as $single_year){
                    $year   = $single_year['year'];
                    $months = explode(',', $single_year['months']); 
                    // $the_count = 0;
                    $yearly_order_ids =  array();
                    $yearly_order_ids2 =  array();
                    foreach ( $months as $month ) {
                        $start_date         = $year . '-' . $month . '-01 00:00:00';
                        $end_date           =  new DateTime($year . '-' . $month . '-01 11:59:59');
                        $end_date = $end_date->format('Y-m-t h:i:s');
                          
                        $monthly_order_ids  = get_order_count($gallery_location,$filter_type,$start_date,$end_date,$event_status, $customer_type, $booking_type, $sales_person_ids, $categories,$fb_leads);
                        if(!empty($monthly_order_ids)){
                            array_push( $yearly_order_ids, $monthly_order_ids );
                            $yearly_order_ids2 = array_merge( $yearly_order_ids2, $monthly_order_ids );
                           
                        }
                       
                    }
        
                    if(!empty($yearly_order_ids2) && 'events'== $request_data){
                        // $my_data[$single_year['year']] =  count($yearly_order_ids2); // push to main array
                        $my_data[$single_year['year']] =  unique_product_count_using_order_id($yearly_order_ids2);
                       
                    }
                    
                    elseif(!empty($yearly_order_ids2) && 'participants'== $request_data){
                        $my_data[$single_year['year']] = event_person_count($yearly_order_ids2,$event_status, $customer_type, $booking_type, $sales_person_ids, $categories, $fb_leads);
                    }
                    else{
                        $my_data[$single_year['year']] = 0;
                    }   
                    // location wise total count
                    $my_data['total'] += $my_data[$single_year['year']];                 
                }
            }
            // by custom date range
            if($filter_type == 'custom_date_range' || $filter_type == 'custom_time_frame'){
                $yearly_order_ids = array();
               
                    foreach ( $request_body as $single_range ) {
                        
                        if( empty( $single_range['start'] ) || empty( $single_range['end'] ) ){
                            continue;
                        }
                       
                        $start_date = $single_range['start'] . ' 00:00:00' ;
                        $end_date   = $single_range['end'] . ' 23:59:59' ;
                        
                        $start_year = date( "Y", strtotime( $start_date ) );
                        $end_year   = date( "Y", strtotime( $end_date ) );
                        
                        
                        if( $start_year === $end_year ){    // selected range is within same year
                            $range_order_ids =  get_order_count($gallery_location,$filter_type,$start_date,$end_date,$event_status, $customer_type, $booking_type, $sales_person_ids, $categories,$fb_leads);
                            
                            if( ! array_key_exists( $start_year, $yearly_order_ids ) ){
                                $yearly_order_ids[$start_year] = $range_order_ids;
                                
                                if('events'== $request_data){
                                    // $my_data[$start_year] = count($yearly_order_ids[$start_year]);
                                    $my_data[$start_year] = unique_product_count_using_order_id($yearly_order_ids[$start_year]);
                                }
                                elseif('participants'== $request_data){
                                    $my_data[$start_year] = event_person_count($yearly_order_ids[$start_year],$event_status, $customer_type, $booking_type, $sales_person_ids, $categories,$fb_leads);
                                }
                                
                            } else {
                                $yearly_order_ids[$start_year] = array_merge( $yearly_order_ids[$start_year], $range_order_ids );
                                // $yearly_order_ids[$start_year] =  count($yearly_order_ids[$start_year]);
                            
                                // $my_data[$start_year] = $yearly_order_ids[$start_year];

                                if('events'== $request_data){
                                    // $my_data[$start_year] = count($yearly_order_ids[$start_year]);
                                    $my_data[$start_year] = unique_product_count_using_order_id($yearly_order_ids[$start_year]);
                                }
                                elseif('participants'== $request_data){
                                    $my_data[$start_year] = event_person_count($yearly_order_ids[$start_year],$event_status, $customer_type, $booking_type, $sales_person_ids, $categories,$fb_leads);
                                }

                                
                            }

                            // location wise total count
                            $my_data['total'] +=  $my_data[$start_year]; 

                        } elseif ( $start_year !== $end_year ) {    // selected range is not within same year
                            for( $i = $start_year; $i <= $end_year; $i++ ) {
                                if ( $i === $start_year ){
                                    $single_start_date  = $start_date;
                                    $single_end_date    = $start_year . "-12-31 23:59:59";
                                } elseif ( $i === $end_year ) {
                                    $single_start_date  = $end_year . "-01-01 00:00:00";
                                    $single_end_date    = $end_date;
                                } else {
                                    $single_start_date  = $i . "-01-01 00:00:00";
                                    $single_end_date    = $i . "-12-31 23:59:59";
                                }
                                
                                $range_order_ids =  get_order_count($gallery_location,$filter_type,$start_date,$end_date,$event_status, $customer_type, $booking_type, $sales_person_ids, $categories,$fb_leads);
                                if( ! array_key_exists( $i, $yearly_order_ids ) ){
                                    $yearly_order_ids[$i] = $range_order_ids;

                                    // $yearly_order_ids[$i] =  count($yearly_order_ids[$i]);
                                    // $my_data[$i] = $yearly_order_ids[$i];
                                    if('events'== $request_data){
                                        // $my_data[$i] = count($yearly_order_ids[$i]);
                                        $my_data[$i] = unique_product_count_using_order_id($yearly_order_ids[$i]);
                                    }
                                    elseif('participants'== $request_data){
                                        $my_data[$i] = event_person_count($yearly_order_ids[$i],$event_status, $customer_type, $booking_type, $sales_person_ids, $categories,$fb_leads);
                                    }

                                } else {
                                    $yearly_order_ids[$i] = array_merge( $yearly_order_ids[$i], $range_order_ids );
                                    // $yearly_order_ids[$i] =  count($yearly_order_ids[$i]);
                                    // $my_data[$i] = $yearly_order_ids[$i];

                                    if('events'== $request_data){
                                        // $my_data[$i] = count($yearly_order_ids[$i]);
                                        $my_data[$i] = unique_product_count_using_order_id($yearly_order_ids[$i]);
                                    }
                                    elseif('participants'== $request_data){
                                        $my_data[$i] = event_person_count($yearly_order_ids[$i],$event_status, $customer_type, $booking_type, $sales_person_ids, $categories,$fb_leads);
                                    }
                                }
                                 // location wise total count
                                $my_data['total'] +=  $my_data[$i];
                            }
                           
                        }
                    }
                    $my_data['location'] = $gallery_location;
            }
        }
        

      array_push($my_returned_data, $my_data);  
    }
   
   
    $response['locations'] = $my_returned_data;
    
    return rest_ensure_response( array(
        'status_code' => 200,
        'message'     => 'success',
        'data'        => $response,
        ) 
    );

}


function get_order_count($gallery_location,$filter_type,$start_date,$end_date,$event_status, $customer_type, $booking_type, $sales_person_ids, $categories, $fb_leads){ 
    global $wpdb;
    $categories = elux_prepare_category_ids_with_localization($categories);


    // query 1
    $query =  "SELECT DISTINCT $wpdb->postmeta.post_id from $wpdb->postmeta LEFT JOIN $wpdb->posts ON $wpdb->postmeta.post_id = $wpdb->posts.ID
     WHERE $wpdb->posts.post_type = 'shop_order' 
    --  AND ($wpdb->postmeta.meta_key = 'event_location' AND ( $wpdb->postmeta.meta_value = $gallery_location))
     AND ( $wpdb->postmeta.meta_key = 'event_start_time' AND ( $wpdb->postmeta.meta_value BETWEEN '%s' AND '%s' ))
     ";

    $response   = $wpdb->get_results( $wpdb->prepare( $query, $start_date, $end_date ) );
    $order_ids  = array_map( "elux_order_id_array_map", $response );

    $event_types = [
        // "steamdemo",
        // "cooking-class",
        // "event_location"  
        'giftcard',
        'voucher', 
        'onsite-consultation', 
        'live-consultation', 
        'home-consultation'
    ];

    $valid_event_order_ids = array_filter( $order_ids, function( $order_id) use ($gallery_location,$event_types){
        if($gallery_location == get_post_meta( $order_id, 'event_location', true ) && !in_array( get_post_meta( $order_id, 'order_service_type', true ), $event_types)){
            return true;
        } else {
            return false;
        }
    } );
   
    // new code
    foreach($valid_event_order_ids as $order_key => $order_id){
        $order          = wc_get_order( $order_id );
        $order_items    = $order->get_items();
        $order_booking_type = get_post_meta($order_id,'consultation_booking_type', true);

        if( is_array( $order_items ) && !empty( $order_items )){
            foreach( $order_items as $item ){
                $product_id = (int) $item->get_product_id();
               
                $status     = !empty( get_post_meta( $product_id, 'product_status', true ) ) ? str_replace(' ', '-', strtolower(get_post_meta( $product_id, 'product_status', true ))) : ''; // event_status
                $type       = !empty( get_post_meta( $product_id, 'customer_type', true ) ) ? strtolower(get_post_meta( $product_id, 'customer_type', true )) : '';// customer type
                $product_cat = get_the_terms( $product_id , 'product_cat' );
                // $sales_person_1 = get_post_meta( $product_id, 'product_status', true);
                $product_fb_lead = !empty( get_post_meta( $product_id, 'product_event_consultant', true ) ) ? strtolower(get_post_meta( $product_id, 'product_event_consultant', true )) : ''; // Culinary Ambassador [product_event_consultant]

                $product_cats = [];
                if($product_cat){
                    foreach($product_cat as $p_cat){
                        array_push($product_cats, $p_cat->term_id);
                    }
                }
                
                // event status [reserved,planned,took_place]
                if($event_status[0] == 'all'){
                //    return;
                }
                else{
                    if(!in_array($status, $event_status)){
                        unset($valid_event_order_ids[$order_key]);
                    }
                }
                // type , customer type
                if($customer_type[0] == 'all'){
                    // return;
                }
                else{
                    if(!in_array($type, $customer_type)){
                        unset($valid_event_order_ids[$order_key]);
                    }
                }
                // $sales_person_ids

                if($sales_person_ids[0] == 'all'){
                    // return;
                }
                else{
                    if( ! product_has_sales_person( $product_id, $sales_person_ids ) ) {
                        unset($valid_event_order_ids[$order_key]);
                    }    
                }
                // categories
              
                if($categories && $categories[0] == 'all'){
                    // return;
                }
                else{
                    // error_log(print_r('product_cats',1));
                    // error_log(print_r($product_cats,1));
                    if($categories){
                        foreach($categories as $category){
                            if(!in_array($category,$product_cats)){
                                unset($valid_event_order_ids[$order_key]);
                                // error_log(print_r('i am here unset ',1));
                            }
                            else{
                                // error_log(print_r('i am here setsss ',1));
                            }
                        }
                    }
                    
                }
                //--------------------------- FB Leads---------------
                if($fb_leads[0] == 'all'){
                    
                }
                else{
                    if(!in_array($product_fb_lead,$fb_leads)){
                        unset($valid_event_order_ids[$order_key]);
                    }
                   
                }
                // FB leads  $fb_leads

                
            }
        }

        //---------------- booking type
        if($booking_type[0] == 'all'){
            return $valid_event_order_ids;
        }
        else{

            if(!in_array($order_booking_type, $booking_type)){
                unset($valid_event_order_ids[$order_key]);
            }
            return $valid_event_order_ids;
        }
        
    }
    // end new code
 
   
    return $valid_event_order_ids;
}
// function unique product count for data_type events
function unique_product_count_using_order_id($order_ids){
    global $woocommerce;
    $yearly_event_participants  = 0;
    $unique_product_ids = [];

    if(!empty($order_ids)){
        foreach( $order_ids as $order_id ){
            $order      = wc_get_order( $order_id );
            $order_items    = $order->get_items();

            if( is_array( $order_items ) && !empty( $order_items )){
                foreach( $order_items as $item ){
                    $product_id = (int) $item->get_product_id();
                    if(!in_array( $product_id, $unique_product_ids, true)){
                        array_push($unique_product_ids, $product_id);
                    }
                }
            }
        }
    }
    return count($unique_product_ids);
}


// persopn count yearwise
function event_person_count($orders_ids,$event_status, $customer_type, $booking_type, $sales_person_ids, $categories,$fb_leads){
    global $woocommerce;
    // error_log(print_r('order id in event_person count ',1));
    // error_log(print_r($orders_ids,1));
    $yearly_event_participants  = 0;
    
    if(!empty($orders_ids)){
        foreach( $orders_ids as $order_id ){
            $order      = wc_get_order( $order_id );
            $order_items    = $order->get_items();

            if( is_array( $order_items ) && !empty( $order_items )){
                foreach( $order_items as $item ){
                    
                    //------------------- new code ------------------------------

                    $product_id = (int) $item->get_product_id();
               
                    $status     = !empty( get_post_meta( $product_id, 'product_status', true ) ) ? str_replace(' ', '-', strtolower(get_post_meta( $product_id, 'product_status', true ))) : ''; // event_status
                    $type       = !empty( get_post_meta( $product_id, 'customer_type', true ) ) ? strtolower(get_post_meta( $product_id, 'customer_type', true )) : '';// customer type
                    $product_cat = get_the_terms( $product_id , 'product_cat' );
                    // $sales_person_1 = get_post_meta( $product_id, 'product_status', true);
                    $product_fb_lead = !empty( get_post_meta( $product_id, 'product_event_consultant', true ) ) ? strtolower(get_post_meta( $product_id, 'product_event_consultant', true )) : ''; // Culinary Ambassador [product_event_consultant]
                  
                    $product_cats = [];
                    if($product_cat){
                        foreach($product_cat as $p_cat){
                            array_push($product_cats, $p_cat->term_id);
                        }
                    }
                    
                    // event status [reserved,planned,took_place]
                    if($event_status[0] == 'all'){
                        $participants_qty = (int) $item->get_quantity();
                    }
                    else{
                        if(!in_array($status, $event_status)){
                            $participants_qty = 0;
                        }
                        else{
                            $participants_qty = (int) $item->get_quantity();
                        }
                    }
                    // type , customer type
                    if($customer_type[0] == 'all'){
                        $participants_qty = (int) $item->get_quantity();
                    }
                    else{
                        if(!in_array($type, $customer_type)){
                            $participants_qty = 0;
                        }
                        else{
                            $participants_qty = (int) $item->get_quantity();
                        }
                    }
                    // $sales_person_ids
    
                    if($sales_person_ids[0] == 'all'){
                        $participants_qty = (int) $item->get_quantity();
                    }
                    else{
                        if( ! product_has_sales_person( $product_id, $sales_person_ids ) ) {
                            $participants_qty = 0;
                        } 
                        else{
                            $participants_qty = (int) $item->get_quantity();
                        }  
                    }
                    // categories
                  
                    if($categories[0] == 'all'){
                        $participants_qty = (int) $item->get_quantity();
                    }
                    else{
                        // error_log(print_r('product_cats',1));
                        // error_log(print_r($product_cats,1));
                        foreach($categories as $category){
                            if(!in_array($category,$product_cats)){
                                $participants_qty = 0;
                                
                            }
                            else{
                                $participants_qty = (int) $item->get_quantity();
                            }
                        }
                    }
                    //--- fb leads
                                       
                    if($fb_leads[0] == 'all'){
                        $participants_qty = (int) $item->get_quantity();
                    }
                    else{
                        if(!in_array($product_fb_lead,$fb_leads)){
                            $participants_qty = 0;
                        }
                        else{
                            $participants_qty = (int) $item->get_quantity();
                        }
                       
                    }
                    //------------------- end new code ------------------------------

                    // $participants_qty = (int) $item->get_quantity();

                }

                
            }
            else{
                $participants_qty = 1;
            }
           
            $yearly_event_participants += $participants_qty;
        }
    }
    
    
    return $yearly_event_participants;

}