<?php

add_action( 'rest_api_init', function () {
    $namespace = 'elux-dashboard/v1';
    register_rest_route( $namespace, '/events-by-month', [
        'methods'             => 'GET',
        'callback'            => 'elux_get_events_by_month',
        'permission_callback' => '__return_true',
    ] );
} );

if( ! function_exists( 'elux_get_events_by_month' ) ){
    
    function elux_get_events_by_month( $request ) {
        
        $disallowed_event_types = array( 'giftcard', 'voucher' );
        $allowed_request_data   = array( 'events', 'participants' );
        $allowed_filter_types   = array( 'months', 'years', 'custom_date_range', 'custom_time_frame' );
        $filter_type            = $request->get_params()['filter_type'];
        $request_data           = $request->get_params()['request_data']; // can be events or participants.
        $request_body           = json_decode($request->get_params()['request_body']);
        $response               = array(
            "type"  => $request_data
        );

        if( ! in_array($filter_type, $allowed_filter_types) || ! in_array($request_data, $allowed_request_data) || ! is_array( $request_body ) || empty( $request_body )){
            return rest_ensure_response( array(
                'status_code' => 403,
                'message'     => 'failure',
                'data'        => array(),
            ) );
        }

        switch( $filter_type ){
            case 'custom_time_frame':
            case 'months':
            case 'years':
                $all_yearly_data = array();
                
                foreach( $request_body as $single_year ){
                    $year   = $single_year->year;
                    $months = explode(',', $single_year->months);
                    
                    $single_year_data           = array();
                    $single_year_data["year"]   = $year;

                    foreach ( $months as $month ) {
                        $start_date         = $year . '-' . $month . '-01 00:00:00' ;
                        $end_date           = $year . '-' . $month . '-31 23:59:59' ;
                        $monthly_order_ids  = elux_get_all_valid_event_order_ids_between_date( $start_date, $end_date, $disallowed_event_types );
                        $monthly_data       = elux_prepare_single_month_data( $month, $monthly_order_ids, $request_data );
                        $single_year_data["months"][] = $monthly_data;
                    }
                    array_push( $all_yearly_data, $single_year_data );
                }

                $response['years'] = $all_yearly_data;
                break;
            case 'custom_date_range':
                $all_yearly_data    = array();
                $yearly_order_ids   = array();

                // prepare yearly order ids
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

                if( is_array( $yearly_order_ids ) && !empty( $yearly_order_ids ) ){
                    foreach( $yearly_order_ids as $year => $yearly_order_ids ){
                        $single_year_data           = array();
                        $single_year_data["year"]   = $year;

                        $monthly_order_ids = array();
                        foreach( $yearly_order_ids as $order_id ){
                            if( empty( get_post_meta( $order_id, 'event_start_time', true ) ) ){
                                continue;
                            }
                            $order_start_date   = get_post_meta( $order_id, 'event_start_time', true );
                            $start_month        = date( "m", strtotime( $order_start_date ) );
                            if( ! array_key_exists( $start_month, $monthly_order_ids ) ){
                                $monthly_order_ids[$start_month] = [$order_id];
                            } else {
                                $monthly_order_ids[$start_month] = array_merge( $monthly_order_ids[$start_month], $order_id );
                            }
                        }

                        foreach ( $monthly_order_ids as $month => $ids ) {
                            $monthly_data       = elux_prepare_single_month_data( $month, $ids, $request_data );
                            $single_year_data["months"][] = $monthly_data;
                        }
                        array_push( $all_yearly_data, $single_year_data );
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

function elux_prepare_single_month_data( $month, $monthly_order_ids, $request_data ){
    $monthly_elux                = 0;
    $monthly_b2b                 = 0;
    $monthly_b2c                 = 0;
    $monthly_event_participants  = 0;

    foreach( $monthly_order_ids as $order_id ){
        $order      = wc_get_order( $order_id );
        $order_items = $order->get_items();
        
        if( is_array( $order_items ) && !empty( $order_items )){
            
            foreach( $order_items as $key => $value ){
                $product_id = (int) $value->get_product_id();
                $type       = !empty( get_post_meta( $product_id, 'customer_type', true ) ) ? strtolower(get_post_meta( $product_id, 'customer_type', true )) : '';
            
                $participants_qty           = (int) $value->get_quantity();
                $monthly_event_participants += $participants_qty;
    
                switch( $request_data ){
                    case 'events':
                        if ( 'b2b' === $type ){
                            $monthly_b2b++;
                        } elseif ( 'b2c' === $type ){
                            $monthly_b2c++;
                        } elseif ( 'electrolux_internal' === $type ){
                            $monthly_elux++;
                        }
    
                        break;
                    case 'participants':
                        if ( 'b2b' === $type ){
                            $monthly_b2b += $participants_qty;
                        } elseif ( 'b2c' === $type ){
                            $monthly_b2c += $participants_qty;
                        } elseif ( 'electrolux_internal' === $type ){
                            $monthly_elux += $participants_qty;
                        }
    
                        break;
                    default:
                        break;
                }
            }
        }
    }

    $monthly_data = array(
        "month"  => $month,
        "elux"  => $monthly_elux,
        "b2b"   => $monthly_b2b,
        "b2c"   => $monthly_b2c,
    );
    $monthly_data['total']   = 'events' === $request_data ? count( $monthly_order_ids ) : $monthly_event_participants;
    
    return $monthly_data;
}