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
        
        $filter_types           = array( 
            'disallowed_types'   => array( 
                'voucher', 
                'onsite-consultation', 
                'live-consultation', 
                'home-consultation' 
                ) 
            );
        $allowed_data_type      = array( 'events', 'participants' );
        $allowed_timeline       = array( 'months', 'years', 'custom_date_range', 'custom_time_frame' );
        $allowed_event_status   = array( 'planned', 'cancelled', 'taken_place' );
        $allowed_customer_type  = array( 'b2b', 'b2c', 'electrolux_internal', 'all' );
        
        // required params.
        $data_type              = $request->get_params()['request_data']; // can be events or participants.
        $event_status           = $request->get_params()['event_status'];   // planned | cancelled etc.
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
            empty( $data_type ) || 
            empty( $event_status ) ||
            empty( $timeline ) ||
            empty( $request_body ) ||
            ! in_array( $timeline, $allowed_timeline ) || 
            ! in_array( $data_type, $allowed_data_type ) || 
            ! in_array( $event_status, $allowed_event_status ) || 
            ! in_array( $customer_type, $allowed_customer_type ) || 
            ! is_array( $request_body ) ){
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
                    
                    $single_year_data           = array();
                    $single_year_data["year"]   = $year;

                    foreach ( $months as $month ) {
                        $start_date         = $year . '-' . $month . '-01 00:00:00' ;
                        $end_date           = $year . '-' . $month . '-31 23:59:59' ;
                        $monthly_order_ids  = elux_get_all_valid_event_order_ids_between_date( $start_date, $end_date, $filter_types );
                        $monthly_data       = elux_prepare_single_month_data( $month, $monthly_order_ids, $data_type, $event_status, $customer_type, $locations, $categories, $sales_person_ids, $consultant_lead_ids );
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
                            $monthly_data       = elux_prepare_single_month_data( $month, $monthly_order_ids, $data_type, $event_status, $customer_type, $locations, $categories, $sales_person_ids, $consultant_lead_ids );
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

function elux_prepare_single_month_data( $month, $monthly_order_ids, $data_type, $event_status, $customer_type, $locations = array(), $categories = array(), $sales_person_ids = array(), $consultant_lead_ids = array() ){
    $monthly_elux                = 0;
    $monthly_b2b                 = 0;
    $monthly_b2c                 = 0;
    $monthly_event_participants  = 0;
    $monthly_events              = [];

    // filter order id's by location.
    $monthly_order_ids   = elux_prepare_order_ids_by_location_filter( $monthly_order_ids, $locations );

    // prepare all category id's including localization.
    $categories          = elux_prepare_category_ids_with_localization( $categories );

    if( is_array( $monthly_order_ids ) && ! empty( $monthly_order_ids ) ){
        foreach( $monthly_order_ids as $order_id ){
            $order      = wc_get_order( $order_id );
            $order_items = $order->get_items();
            
            if( is_array( $order_items ) && !empty( $order_items )){
                foreach( $order_items as $key => $value ){
                    $product_id = (int) $value->get_product_id();
                  
                    // don't check this step for participants.
                    if( 'events' === $data_type && in_array( $product_id, $monthly_events ) ){
                        continue;
                    }

                    $type       = !empty( get_post_meta( $product_id, 'customer_type', true ) ) ? strtolower(get_post_meta( $product_id, 'customer_type', true )) : '';
                    $status     = !empty( get_post_meta( $product_id, 'product_status', true ) ) ? str_replace(' ', '_', strtolower(get_post_meta( $product_id, 'product_status', true ))) : '';
                    $product_cat= get_the_terms( $product_id , 'product_cat' );

                    // filter event by category.
                    // check if any one of the product categories exist in the $categories array.
                    if( !empty( $categories ) && empty( array_intersect( $product_cat, $categories ) )){
                        continue;
                    }

                    // filter event by event_status.
                    if( $status !== $event_status ){
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
                    
                    $participants_qty           = (int) $value->get_quantity();
                    $monthly_event_participants += $participants_qty;
                    
                    array_push( $monthly_events, $product_id );
        
                    switch( $data_type ){
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
    }

    $monthly_data = array(
        "month" => $month,
        "elux"  => $monthly_elux,
        "b2b"   => $monthly_b2b,
        "b2c"   => $monthly_b2c,
    );
    $monthly_data['total']   = ( 'events' === $data_type ) ? count( $monthly_events ) : $monthly_event_participants;
    
    return $monthly_data;
}