<?php 
/**
 * GET > page  for set the page number start from 1 
 * 
 * section_name = 'name_of_section' 
 * page = '1|2|3' 
 * Sort by = 
 * 
 */

 /*-----------------------------------------------------
    ADD SECTION  BASED COMMENT 
--------------------------------------------------------*/

add_action( 'rest_api_init', function () {
   
    $namespace = 'elux-dashboard/v1';

    register_rest_route( $namespace, '/section-comments', [
        'methods' => 'POST',
        'callback' => 'el_add_section_comments',
        'login_user_id' => get_current_user_id(),
    ]);

} );

function el_add_section_comments($ReqObj){

    if(el_has_rest_Authority($ReqObj) == true ){

        // get user info
        $attrs              = $ReqObj->get_attributes();
        $current_user_id    = intval($attrs['login_user_id']);
        $current_user       = get_user_by( 'id', $current_user_id );


        $received_data = $ReqObj->get_json_params();


        if( 
            isset($received_data['comment_content']) &&
            trim($received_data['comment_content']) &&
            isset($received_data['comment_section']) &&
            trim($received_data['comment_section'])         
        ){

            $comment_content    = sanitize_textarea_field(  $received_data['comment_content'] );
            $email              = $current_user->user_email;
            $section_name       = $received_data['comment_section'];


            // Insert the comment (Return : int|false)
            $is_inserted = wp_insert_comment([
                'comment_approved'      => 1,
                'user_id'               => $current_user_id,
                'comment_author_email'  => $email,
                'comment_author'        => $current_user->user_login,
                'comment_meta'          => [
                    'comment_section'   => sanitize_key( $received_data['comment_section'] ),
                ],
                'comment_content'       => $comment_content,
            ]);




            if( $is_inserted === false ){
                
                return wp_send_json([
                    "status"    => false,
                    'message'   => "Comment not saved",
                    'currentuser' => get_current_user_id(  ),
                ]);
            }

            if( $is_inserted > 0 ){
                return wp_send_json([
                    "status"            => true,
                    'message'           => "Your Comment Added",
                    'dev_message'       => "Comment id ".$is_inserted,
                    'section_name'      => sanitize_key( $received_data['comment_section'] ),
                    'currentuser'       => get_current_user_id(  ),
                ]);
            }

        }else{
            return wp_send_json([
                "status"    => false,
                'message'   => "Please provide a valid comment",
                'currentuser' => get_current_user_id(  ),
            ]);
        }

    }else{
        return wp_send_json([
            'status'    => false,
            'data'      => 'Please login to get the comments or you have to be admin'
        ]);
    }
}



 /*-----------------------------------------------------
    GET SECTION  BASED COMMENT 

    -- PARAMETERS
    1. section_name 
    2. page
    3. comment_year : 2022,2021
--------------------------------------------------------*/

add_action( 'rest_api_init', function () {
    $namespace = 'elux-dashboard/v1';

    register_rest_route( $namespace, '/section-comments', [
        'methods' => 'GET',
        'callback' => 'el_get_section_comments',
        'login_user_id' => get_current_user_id(),
    ]);
});

function el_get_section_comments($ReqObj){

    // for testing
    if(el_has_rest_Authority($ReqObj) == true || true ){
        

        // get section name
        $section_name =  $ReqObj->get_param('section_name') ? sanitize_key( $ReqObj->get_param('section_name') ) : false;

        // check if section name exist
        if($section_name == false){
            return wp_send_json([
                'status'    => false,
                'data'      => [],
                'message'   => "Please provide section_name in query!"
            ]);
        }

        $comments_per_page = 10;

        $args = [
            'meta_key'      => 'comment_section',
            'meta_value'    => $section_name,
            'number'        => $comments_per_page,
            'offset'        => 0,
        ];

        // change page number 
        $comment_page   =  intval(  $ReqObj->get_param('page') );
        
        if( $comment_page ){
            $offset_value   =  ($comment_page - 1) * $comments_per_page;
            if($offset_value){
                $args['offset'] = $offset_value;
            }
        }   

        // Filter by year
        $comment_year   =  intval(  $ReqObj->get_param('comment_year') )  ?  intval(  $ReqObj->get_param('comment_year') ) : date("Y");
        $args['date_query'] = [
            'year' => $comment_year
        ];



        $comments_query = new WP_Comment_Query;
        $comments       = $comments_query->query($args);

        return wp_send_json([
            'status'    => true,
            'data'      => $comments
        ]);

    }else{
        return wp_send_json([
            'status'    => false,
            'data'      => 'Please login to get the comments'
        ]);
    }
}




 /*-----------------------------------------------------
    UPDATE Comment 

    -- PARAMETERS
    1. comment_content  = text
    2. comment_id = number/text
--------------------------------------------------------*/
add_action( 'rest_api_init', function () {
    $namespace = 'elux-dashboard/v1';

    register_rest_route( $namespace, '/section-comments', [
        'methods' => 'PUT',
        'callback' => 'el_update_generic_comments',
        'login_user_id' => get_current_user_id(),
    ]);
});


 /*-----------------------------------------------------
    DELETE Comment 
    
    -- PARAMETERS
    2. comment_id = number/text
    
--------------------------------------------------------*/
add_action( 'rest_api_init', function () {
    $namespace = 'elux-dashboard/v1';

    register_rest_route( $namespace, '/section-comments', [
        'methods' => 'DELETE',
        'callback' => 'el_delete_generic_comments',
        'login_user_id' => get_current_user_id(),
    ]);

} );
