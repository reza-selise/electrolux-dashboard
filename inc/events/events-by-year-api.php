<?php

add_action( 'rest_api_init', function () {
    $namespace = 'elux-dashboard/v1';
    register_rest_route( $namespace, '/events-by-year', [
        'methods'             => 'GET',
        'callback'            => 'elux_get_events_by_year',
        'permission_callback' => '__return_true',
    ] );
} );

if( ! function_exists( 'elux_get_events_by_year' ) ){
    
    function elux_get_events_by_year( $request ) {

        $disallowed_event_types = array( 'giftcard', 'voucher' );
        $allowed_data_type      = array( 'events', 'participants' );
        $allowed_timeline       = array( 'months', 'years', 'custom_date_range', 'custom_time_frame' );
        $allowed_event_status   = array( 'planned', 'cancelled', 'taken_place' );
        $allowed_customer_type  = array( 'b2b', 'b2c', 'electrolux_internal', 'all' );

        $timeline               = $request->get_params()['filter_type'];
        $data_type              = $request->get_params()['request_data']; // events | participants etc.
        $event_status           = $request->get_params()['event_status']; // planned | cancelled etc.
        $customer_type          = $request->get_params()['customer_type']; // b2b | b2c | electrolux_internal | all etc.
        $locations              = ! empty($request->get_params()['locations']) ? $request->get_params()['locations'] : '';     // 188,191,500 etc.
        $locations              = explode( ',', $locations );

        $request_body           = json_decode($request->get_params()['request_body']);
        $response               = array(
            "type"  => $data_type
        );

        if( 
            ! in_array( $timeline, $allowed_timeline ) || 
            ! in_array( $data_type, $allowed_data_type ) || 
            ! in_array( $event_status, $allowed_event_status ) || 
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
                    
                    $yearly_order_ids           = array();
                    foreach ( $months as $month ) {
                        $start_date         = $year . '-' . $month . '-01 00:00:00' ;
                        $end_date           = $year . '-' . $month . '-31 23:59:59' ;
                        $monthly_order_ids  = elux_get_all_valid_event_order_ids_between_date( $start_date, $end_date, $disallowed_event_types );
                        $yearly_order_ids   = array_merge( $yearly_order_ids, $monthly_order_ids );
                    }

                    $yearly_data = elux_prepare_single_year_data( $year, $yearly_order_ids, $data_type, $event_status, $customer_type, $locations, $categories );
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

                        $start_date = $single_range->start . ' 00:00:00' ;
                        $end_date   = $single_range->end . ' 23:59:59' ;
                        
                        $start_year = date( "Y", strtotime( $start_date ) );
                        $end_year   = date( "Y", strtotime( $end_date ) );
                        
                        
                        if( $start_year === $end_year ){    // selected range is within same year
                            $range_order_ids = elux_get_all_valid_event_order_ids_between_date( $start_date, $end_date, $disallowed_event_types );
                            
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
                                
                                $range_order_ids = elux_get_all_valid_event_order_ids_between_date( $single_start_date, $single_end_date, $disallowed_event_types );
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
                        $yearly_data = elux_prepare_single_year_data( $year, $yearly_order_ids, $data_type, $event_status, $customer_type, $locations, $categories );
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

function elux_prepare_single_year_data( $year, $yearly_order_ids, $data_type, $event_status, $customer_type, $locations = array(), $categories = array() ){
    $yearly_elux                = 0;
    $yearly_b2b                 = 0;
    $yearly_b2c                 = 0;
    $yearly_event_participants  = 0;
    $yearly_events              = 0;

    // filter order id's by location.
    $yearly_order_ids = elux_prepare_single_year_data_filter_location( $yearly_order_ids, $locations );

    if( is_array( $yearly_order_ids ) && ! empty( $yearly_order_ids ) ){
        foreach( $yearly_order_ids as $order_id ){
            $order          = wc_get_order( $order_id );
            $order_items    = $order->get_items();
            
            if( is_array( $order_items ) && !empty( $order_items )){
                foreach( $order_items as $key => $value ){
                    $product_id = (int) $value->get_product_id();
                    $type       = !empty( get_post_meta( $product_id, 'customer_type', true ) ) ? strtolower(get_post_meta( $product_id, 'customer_type', true )) : '';
                    $status     = !empty( get_post_meta( $product_id, 'product_status', true ) ) ? str_replace(' ', '_', strtolower(get_post_meta( $product_id, 'product_status', true ))) : '';
                    
                    // filter event by event_status.
                    if( $status !== $event_status ){
                        continue;
                    }

                    // filter event by customer type.
                    if( ( $type !== $customer_type ) && ( 'all' !== $customer_type ) ){
                        continue;
                    }

                    $participants_qty           = (int) $value->get_quantity();
                    $yearly_event_participants += $participants_qty;
                    $yearly_events++;
            
                    switch( $data_type ){
                        case 'events':
                            if ( 'b2b' === $type ){
                                $yearly_b2b++;
                            } elseif ( 'b2c' === $type ){
                                $yearly_b2c++;
                            } elseif ( 'electrolux_internal' === $type ){
                                $yearly_elux++;
                            }
                            break;
                            
                        case 'participants':
                            if ( 'b2b' === $type ){
                                $yearly_b2b += $participants_qty;
                            } elseif ( 'b2c' === $type ){
                                $yearly_b2c += $participants_qty;
                            } elseif ( 'electrolux_internal' === $type ){
                                $yearly_elux += $participants_qty;
                            }
                            break;

                        default:
                            break;
                    } 
                }
            }
        }
    }

    $yearly_data = array(
        "year"  => $year,
        "elux"  => $yearly_elux,
        "b2b"   => $yearly_b2b,
        "b2c"   => $yearly_b2c,
    );
    $yearly_data['total']   = ( 'events' === $data_type ) ? $yearly_events : $yearly_event_participants;
    
    return $yearly_data;
}

function elux_prepare_single_year_data_filter_location( $yearly_order_ids, $locations = array() ){
    /* If, user requested for specific location data
    *  and the current order is not from that location,
    *  then skip to the next iteration.
    *
    *  Else, proceed as usual.
    */
    if( is_array( $locations ) && ! empty( $locations ) ){
        $valid_order_ids = array_filter( $yearly_order_ids, function( $order_id ) use( $locations ) {
            $event_location = ! empty( get_post_meta( $order_id, 'event_location', true ) ) ? get_post_meta( $order_id, 'event_location', true ) : 0;
            if( ! in_array( $event_location, $locations ) ){
                return false;
            }
            return true;
        });
        return $valid_order_ids;
    }

    return $yearly_order_ids;
}