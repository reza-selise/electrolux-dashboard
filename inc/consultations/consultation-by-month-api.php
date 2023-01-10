<?php

add_action( 'rest_api_init', function () {
    $namespace = 'elux-dashboard/v1';
    register_rest_route( $namespace, '/consultation-by-month', [
        'methods'             => 'GET',
        'callback'            => 'elux_get_consultations_by_month',
        'permission_callback' => '__return_true',
    ] );
} );

if( ! function_exists( 'elux_get_consultations_by_month' ) ){
    
    function elux_get_consultations_by_month( $request ) {
        
        $filter_types           = array( 
            'allowed_types'   => array( 
                'onsite-consultation', 
                'live-consultation', 
            ) 
        );
        $allowed_data_type      = 'events';
        $allowed_timeline       = array( 'months', 'years', 'custom_date_range', 'custom_time_frame' );
        // $allowed_event_status   = array( 'planned', 'cancelled', 'taken_place' );
        // $allowed_customer_type  = array( 'b2b', 'b2c', 'electrolux_internal', 'all' );
        
        // required params.
        // $data_type              = $request->get_params()['request_data']; // can be events or participants.
        // $event_status           = $request->get_params()['event_status'];   // planned | cancelled etc.
        $timeline               = $request->get_params()['filter_type'];
        
        // optional params.
        // $customer_type          = ! empty( $request->get_params()['customer_type'] ) ? $request->get_params()['customer_type'] : 'all';  // b2b | b2c | electrolux_internal | all etc.
        // $locations              = ! empty( $request->get_params()['locations'] ) ? explode( ',', $request->get_params()['locations'] ) : [];      // 188,191,500 etc.
        // $categories             = ! empty( $request->get_params()['categories'] ) ? explode( ',', $request->get_params()['categories'] ) : [];     // 15 | 47 | 104
        // $sales_person_ids       = ! empty( $request->get_params()['salesperson'] ) ? explode( ',', $request->get_params()['salesperson'] ) : [];     // 7 | 8 | 9
        // $consultant_lead_ids    = ! empty( $request->get_params()['consultant_lead'] ) ? explode( ',', $request->get_params()['consultant_lead'] ) : [];     // 7 | 8 | 9
        
        $request_body           = json_decode($request->get_params()['request_body']);
        $response               = array(
            "type"  => $allowed_data_type
        );

        if( 
            empty( $timeline ) ||
            empty( $request_body ) ||
            ! in_array( $timeline, $allowed_timeline ) || 
            // ! in_array( $data_type, $allowed_data_type ) || 
            // ! in_array( $event_status, $allowed_event_status ) || 
            // ! in_array( $customer_type, $allowed_customer_type ) || 
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
                $all_years_graph_data = array();
                $all_months_table_data = array(
                    "01"    => array(
                        "onsite"    => array(
                            "b2b"   => 10,
                            "b2c"   => 20,
                        ),
                        "live"      => array(
                            "b2b"   => 10,
                            "b2c"   => 20,
                        ),
                    ),
                    "02"    => array(
                        "onsite"    => array(
                            "b2b"   => 10,
                            "b2c"   => 20,
                        ),
                        "live"      => array(
                            "b2b"   => 10,
                            "b2c"   => 20,
                        ),
                    ),
                    "03"    => array(
                        "onsite"    => array(
                            "b2b"   => 10,
                            "b2c"   => 20,
                        ),
                        "live"      => array(
                            "b2b"   => 10,
                            "b2c"   => 20,
                        ),
                    ),
                );
                
                foreach( $request_body as $single_year ){
                    $year   = $single_year->year;
                    $months = explode(',', $single_year->months);
                    
                    $single_year_data           = array();
                    $single_year_data["year"]   = $year;

                    foreach ( $months as $month ) {
                        $start_date         = $year . '-' . $month . '-01 00:00:00' ;
                        $end_date           = $year . '-' . $month . '-31 23:59:59' ;
                        $monthly_order_ids  = elux_get_all_valid_event_order_ids_between_date( $start_date, $end_date, $filter_types );
                        $monthly_data       = elux_prepare_single_month_consultations( $month, $monthly_order_ids );
                        
                        // prepare data for graph.
                        $single_year_data["months"][] = array(
                                "month" => $monthly_data["month"],
                                "total" => $monthly_data["total"],
                        );

                        // prepare data for table.
                        if( !array_key_exists( $month, $all_months_table_data ) ){
                            $all_months_table_data[$month]["onsite"]["b2b"] = (int) $monthly_data["onsite"]["b2b"];
                            $all_months_table_data[$month]["onsite"]["b2c"] = (int) $monthly_data["onsite"]["b2c"];
                            $all_months_table_data[$month]["live"]["b2b"]   = (int) $monthly_data["live"]["b2b"];
                            $all_months_table_data[$month]["live"]["b2c"]   = (int) $monthly_data["live"]["b2c"];
                        } else {
                            $all_months_table_data[$month]["onsite"]["b2b"] += (int) $monthly_data["onsite"]["b2b"];
                            $all_months_table_data[$month]["onsite"]["b2c"] += (int) $monthly_data["onsite"]["b2c"];
                            $all_months_table_data[$month]["live"]["b2b"]   += (int) $monthly_data["live"]["b2b"];
                            $all_months_table_data[$month]["live"]["b2c"]   += (int) $monthly_data["live"]["b2c"];
                        }

                    }
                    array_push( $all_years_graph_data, $single_year_data );
                }

                $response['graph'] = $all_years_graph_data;
                $response["table"] = $all_months_table_data;
                break;
            case 'custom_date_range':
                $all_yearly_data    = array();
                $yearly_order_ids   = array();
                $all_months_table_data = array(
                    "01"    => array(
                        "onsite"    => array(
                            "b2b"   => 10,
                            "b2c"   => 20,
                        ),
                        "live"      => array(
                            "b2b"   => 10,
                            "b2c"   => 20,
                        ),
                    ),
                    "02"    => array(
                        "onsite"    => array(
                            "b2b"   => 10,
                            "b2c"   => 20,
                        ),
                        "live"      => array(
                            "b2b"   => 10,
                            "b2c"   => 20,
                        ),
                    ),
                    "03"    => array(
                        "onsite"    => array(
                            "b2b"   => 10,
                            "b2c"   => 20,
                        ),
                        "live"      => array(
                            "b2b"   => 10,
                            "b2c"   => 20,
                        ),
                    ),
                );

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
                                $monthly_order_ids[$start_month] = array_push( $monthly_order_ids[$start_month], $order_id );
                            }
                        }

                        foreach ( $monthly_order_ids as $month => $ids ) {
                            $monthly_data       = elux_prepare_single_month_consultations( $month, $ids );
                            $single_year_data["months"][] = array(
                                "month" => $monthly_data["month"],
                                "total" => $monthly_data["total"],
                            );

                            // prepare data for table.
                            if( !array_key_exists( $month, $all_months_table_data ) ){
                                $all_months_table_data[$month]["onsite"]["b2b"] = (int) $monthly_data["onsite"]["b2b"];
                                $all_months_table_data[$month]["onsite"]["b2c"] = (int) $monthly_data["onsite"]["b2c"];
                                $all_months_table_data[$month]["live"]["b2b"]   = (int) $monthly_data["live"]["b2b"];
                                $all_months_table_data[$month]["live"]["b2c"]   = (int) $monthly_data["live"]["b2c"];
                            } else {
                                $all_months_table_data[$month]["onsite"]["b2b"] += (int) $monthly_data["onsite"]["b2b"];
                                $all_months_table_data[$month]["onsite"]["b2c"] += (int) $monthly_data["onsite"]["b2c"];
                                $all_months_table_data[$month]["live"]["b2b"]   += (int) $monthly_data["live"]["b2b"];
                                $all_months_table_data[$month]["live"]["b2c"]   += (int) $monthly_data["live"]["b2c"];
                            }
                        }
                        array_push( $all_yearly_data, $single_year_data );
                    }
                }

                $response['graph'] = $all_yearly_data;
                $response["table"] = $all_months_table_data;
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

function elux_prepare_single_month_consultations( $month, $monthly_order_ids, $locations = array(), $categories = array(), $sales_person_ids = array(), $consultant_lead_ids = array() ){
    $response   = array(
        "month" => $month,
        "onsite"    => array(
            "b2b"   => 0,
            "b2c"   => 0,
        ),
        "live"    => array(
            "b2b"   => 0,
            "b2c"   => 0,
        ),
        "total" => 0
    );
    $monthly_events              = [];
    $order_type_map =   array(
        "onsite-consultation"   => "onsite",
        "live-consultation"     => "live",
    );

    // filter order id's by location.
    // $monthly_order_ids   = elux_prepare_order_ids_by_location_filter( $monthly_order_ids, $locations );

    // prepare all category id's including localization.
    // $categories          = elux_prepare_category_ids_with_localization( $categories );

    if( is_array( $monthly_order_ids ) && ! empty( $monthly_order_ids ) ){
        foreach( $monthly_order_ids as $order_id ){
            $order          = wc_get_order( $order_id );
            $order_items    = $order->get_items();
            $order_type     = $order_type_map[get_post_meta( $order_id, 'order_service_type', true )];
            
            if( is_array( $order_items ) && !empty( $order_items )){
                foreach( $order_items as $key => $value ){
                    $product_id = (int) $value->get_product_id();
                  
                    // don't check this step for participants.
                    // if( 'events' === $data_type && in_array( $product_id, $monthly_events ) ){
                    //     continue;
                    // }

                    $type       = !empty( get_post_meta( $product_id, 'customer_type', true ) ) ? strtolower(get_post_meta( $product_id, 'customer_type', true )) : '';
                    // $status     = !empty( get_post_meta( $product_id, 'product_status', true ) ) ? str_replace(' ', '_', strtolower(get_post_meta( $product_id, 'product_status', true ))) : '';
                    // $product_cat= get_the_terms( $product_id , 'product_cat' );

                    // filter event by category.
                    // check if any one of the product categories exist in the $categories array.
                    // if( !empty( $categories ) && empty( array_intersect( $product_cat, $categories ) )){
                    //     continue;
                    // }

                    // filter event by event_status.
                    // if( $status !== $event_status ){
                    //     continue;
                    // }

                    // filter event by customer type.
                    // if( ( $type !== $customer_type ) && ( 'all' !== $customer_type ) ){
                    //     continue;
                    // }

                    // filter event by sales person id.
                    // if( ! product_has_sales_person( $product_id, $sales_person_ids ) ) {
                    //     continue;
                    // }

                    // filter event by fb lead id.
                    // if( ! product_has_consultant_lead( $product_id, $consultant_lead_ids ) ) {
                    //     continue;
                    // }
                    
                    // $participants_qty           = (int) $value->get_quantity();
                    // $monthly_event_participants += $participants_qty;
                    
                    // array_push( $monthly_events, $product_id );
                    if ( 'b2b' === $type || 'b2c' === $type ){
                        $response[$order_type][$type]++;
                        $response["total"]++;
                    } else {
                        continue;
                    }
                    // if ( 'b2b' === $type ){
                    //     $monthly_b2b++;
                    // } elseif ( 'b2c' === $type ){
                    //     $monthly_b2c++;
                    // } elseif ( 'electrolux_internal' === $type ){
                    //     $monthly_elux++;
                    // }
                }
            }
        }
    }

    // $monthly_data = array(
    //     "month" => $month,
    //     "total" => count( $monthly_events ),
    // );
    return $response;
}