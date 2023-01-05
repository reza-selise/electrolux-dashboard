<?php

add_action( 'rest_api_init', function () {
    $namespace = 'elux-dashboard/v1';
    register_rest_route( $namespace, '/events-by-status', [
        'methods'             => \WP_REST_Server::READABLE,
        'callback'            => 'elux_get_events_by_status',
        'permission_callback' => '__return_true'
    ] );
} );

if( ! function_exists( 'elux_get_events_by_status' ) ){
    
    function elux_get_events_by_status( $request ) {

        // Generic filter TYPE: event / participants and STATUS: planned ? cancelled won't work here.
        $filter_types           = array( 'disallowed_types'   => array( 'voucher', 'onsite-consultation', 'live-consultation', 'home-consultation' ) );
        $allowed_timeline       = array( 'months', 'years', 'custom_date_range', 'custom_time_frame' );
        $allowed_customer_type  = array( 'b2b', 'b2c', 'electrolux_internal', 'all' );

        // required params.
        $data_type              = 'events';
        $timeline               = $request->get_params()['filter_type'];

        // optional params.
        $customer_type          = ! empty( $request->get_params()['customer_type'] ) ? $request->get_params()['customer_type'] : 'all';  // b2b | b2c | electrolux_internal | all etc.
        $locations              = ! empty( $request->get_params()['locations'] ) ? explode( ',', $request->get_params()['locations'] ) : [];      // 188,191,500 etc.
        $categories             = ! empty( $request->get_params()['categories'] ) ? explode( ',', $request->get_params()['categories'] ) : [];     // 15 | 47 | 104
        $sales_person_ids       = ! empty( $request->get_params()['salesperson'] ) ? explode( ',', $request->get_params()['salesperson'] ) : [];     // 7 | 8 | 9
        $consultant_lead_ids    = ! empty( $request->get_params()['consultant_lead'] ) ? explode( ',', $request->get_params()['consultant_lead'] ) : [];     // 7 | 8 | 9
        
        $request_body           = json_decode($request->get_params()['request_body']);
        $response               = array(
            "type"  => $data_type
        );

        if( 
            ! in_array( $timeline, $allowed_timeline ) ||
            ! in_array( $customer_type, $allowed_customer_type ) || 
            ! is_array( $request_body ) || 
            empty( $request_body ) ){
            return rest_ensure_response( array(
                'status_code' => 403,
                'message'     => 'failure',
                'data'        => array(),
            ) );
        }

        switch( $timeline ){
            case 'custom_time_frame':
            case 'months':
            case 'years':
                $all_yearly_data = array();
                
                foreach( $request_body as $single_year ){
                    $year   = $single_year->year;
                    $months = explode(',', $single_year->months);
                    $yearly_order_ids   = array();

                    foreach ( $months as $month ) {
                        $start_date         = $year . '-' . $month . '-01 00:00:00' ;
                        $end_date           = $year . '-' . $month . '-31 23:59:59' ;
                        $monthly_order_ids  = elux_get_all_valid_event_order_ids_between_date( $start_date, $end_date, $filter_types );
                        $yearly_order_ids   = array_merge( $yearly_order_ids, $monthly_order_ids );
                    }

                    $yearly_data = elux_prepare_single_year_by_status_data( $year, $yearly_order_ids, $customer_type, $locations, $categories, $sales_person_ids, $consultant_lead_ids );
                    array_push( $all_yearly_data, $yearly_data );
                }

                $response['years'] = $all_yearly_data;
                break;
            case 'custom_date_range':
                // first, prepare all order id's according to year.
                $yearly_order_ids = array();
                if( is_array( $request_body ) && ! empty( $request_body )){
                    foreach ( $request_body as $single_range ) {
                        if( empty( $single_range->start ) || empty( $single_range->end ) ){
                            continue;
                        }

                        $start_date = date( "Y-m-d", strtotime( $single_range->start ) ) . ' 00:00:00' ;
                        $end_date   = date( "Y-m-d", strtotime( $single_range->end ) ) . ' 23:59:59' ;
                        
                        $start_year = date( "Y", strtotime( $start_date ) );
                        $end_year   = date( "Y", strtotime( $end_date ) );
                        
                        if( $start_year === $end_year ){    // selected range is within same year
                            $range_order_ids = elux_get_all_valid_event_order_ids_between_date( $start_date, $end_date, $filter_types );
                            
                            if( ! array_key_exists( $start_year, $yearly_order_ids ) ){
                                $yearly_order_ids[$start_year] = $range_order_ids;
                            } else {
                                $yearly_order_ids[$start_year] = array_merge( $yearly_order_ids[$start_year], $range_order_ids );
                            }
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
                                
                                $range_order_ids = elux_get_all_valid_event_order_ids_between_date( $single_start_date, $single_end_date, $filter_types );
                                if( ! array_key_exists( $i, $yearly_order_ids ) ){
                                    $yearly_order_ids[$i] = $range_order_ids;
                                } else {
                                    $yearly_order_ids[$i] = array_merge( $yearly_order_ids[$i], $range_order_ids );
                                }
                            }
                        }
                    }
                }

                // store yearly event/participants objects.
                $all_yearly_data = array();
                if( is_array( $yearly_order_ids ) && !empty( $yearly_order_ids ) ){
                    foreach( $yearly_order_ids as $year => $yearly_order_ids ){
                        $yearly_data = elux_prepare_single_year_by_status_data(  $year, $yearly_order_ids, $customer_type, $locations, $categories, $sales_person_ids, $consultant_lead_ids );
                        array_push( $all_yearly_data, $yearly_data );
                    }
                }

                $response['years'] = $all_yearly_data;
                break;
            default: 
                return rest_ensure_response( array(
                    'status_code' => 403,
                    'message'     => 'failure',
                    'data'        => array(),
                    ) 
                );
                break;

        }

        return rest_ensure_response( array(
            'status_code' => 200,
            'message'     => 'success',
            'data'        => $response,
            ) 
        );
    }
    
}

function elux_prepare_single_year_by_status_data(  $year, $yearly_order_ids, $customer_type, $locations = array(), $categories = array(), $sales_person_ids = array(), $consultant_lead_ids = array() ){
    $planned        = 0;
    $cancelled      = 0;
    $taken_place    = 0;
    $total          = 0;

    // filter order id's by location.
    $yearly_order_ids   = elux_prepare_order_ids_by_location_filter( $yearly_order_ids, $locations );

    // prepare all category id's including localization.
    $categories         = elux_prepare_category_ids_with_localization( $categories );

    if( is_array( $yearly_order_ids ) && ! empty( $yearly_order_ids ) ){
        foreach( $yearly_order_ids as $order_id ){
            $order          = wc_get_order( $order_id );
            $order_items    = $order->get_items();
            
            if( is_array( $order_items ) && !empty( $order_items )){
                foreach( $order_items as $item ){
                    $product_id = (int) $item->get_product_id();
                    $type       = !empty( get_post_meta( $product_id, 'customer_type', true ) ) ? strtolower(get_post_meta( $product_id, 'customer_type', true )) : '';
                    $status     = !empty( get_post_meta( $product_id, 'product_status', true ) ) ? str_replace(' ', '-', strtolower(get_post_meta( $product_id, 'product_status', true ))) : '';
                    $product_cat= get_the_terms( $product_id , 'product_cat' );

                    // filter event by category.
                    // check if any one of the product categories exist in the $categories array.
                    if( !empty( $categories ) && empty( array_intersect( $product_cat, $categories ) )){
                        continue;
                    }

                    // filter event by customer type.
                    if( ( $type !== $customer_type ) && ( 'all' !== $customer_type ) ){
                        continue;
                    }

                    // filter event by sales person id.
                    if( ! product_has_sales_person( $product_id, $sales_person_ids ) ) {
                        continue;
                    }

                    // filter event by fb lead id.
                    if( ! product_has_consultant_lead( $product_id, $consultant_lead_ids ) ) {
                        continue;
                    }

                    if ( 'canceled' === $status ){
                        $cancelled++;
                    } elseif ( 'planned' === $status ){
                        $planned++;
                    } elseif ('took-place' === $status ){
                        $taken_place++;
                    }

                    $total++;
                }
            }
        }
    }

    $yearly_data = array(
        "year"          => $year,
        "planned"       => $planned,
        "cancelled"     => $cancelled,
        "taken_place"   => $taken_place,
        "total"         => $total,
    );
    
    return $yearly_data;
}