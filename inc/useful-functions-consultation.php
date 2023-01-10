<?php 
/** 

* This function will return filter key value array based on given order id

*/
function el_get_each_consultation_FILTER_KEY_VALUES_ARR($order_id){

    $order = wc_get_order($order_id);
    $order_items = $order->get_items();

    $output =[];

    $order_service_type = get_post_meta( $order_id, 'order_service_type', true );

    $output['order_service_type'] = $order_service_type;
    $location_arr = [];

    foreach ( $order_items as $item_id => $item ) {


        $product_id = $item->get_product_id();
        $output['product_ids'][] =  $product_id;

        // Customer type
        $customer_type =  get_post_meta( $product_id, 'customer_type', true );
        if($customer_type){
            $output['customer_type'] = $customer_type;
        }

        $location       = (int) get_post_meta( $product_id, 'event_location', true );
        if($location){
            $location_arr[] = $location;
        }

    }

    if(count($location_arr) > 0){
        $output['location'] = $location_arr;
    }

    return $output;

}