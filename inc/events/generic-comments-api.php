<?php

// ------------- GET Comments ---------------
/**
 * GET > page  for set the page number start from 1 
 * 
 */

add_action( 'rest_api_init', function () {
    register_rest_route( 'el-dashboard-api', '/generic-comments', [
        'methods' => 'GET',
        'callback' => 'el_get_generic_comments',
    ]);
});

function el_get_generic_comments($ReqObj){

    $comments_per_page = 10;

    $args = [
        'meta_key'      => 'comment_section',
        'meta_value'    => 'generic',
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


    $comments_query = new WP_Comment_Query;
    $comments = $comments_query->query($args);

    return wp_send_json([
        'status' => true,
        'data'  =>$comments
    ]);

}


// ------------- ADD Generic Comments ---------------

add_action( 'rest_api_init', function () {
    register_rest_route( 'el-dashboard-api', '/generic-comments', [
        'methods' => 'POST',
        'callback' => 'el_add_generic_comments',
    ]);
} );

function el_add_generic_comments($ReqObj){

    // $comment_content  =  $()

    $received_data = $ReqObj->get_json_params();


    if( isset($received_data['comment_content']) ){
        $comment_content = sanitize_textarea_field(  $received_data['comment_content'] );
        $current_user = wp_get_current_user();
        $email = $current_user->user_email;


        // Insert the comment (Return : int|false)
        $is_inserted = wp_insert_comment([
            'comment_approved'  => 1,
            'user_id'           => get_current_user_id(  ),
            'comment_author_email' => $email,
            'comment_author'    => $current_user->user_login,
            'comment_meta'      => [
                'comment_section' => 'generic'
            ],
            'comment_content'   => $comment_content,
        ]);




        if( $is_inserted === false ){
            
            return wp_send_json([
                "status"    => false,
                'message'   => "Comment not saved",
            ]);
        }

        if( $is_inserted > 0 ){
            return wp_send_json([
                "status"    => true,
                'message'   => "Your Comment Added",
                'dev_message'   => "Comment id ".$is_inserted,
            ]);
        }

    }else{
        return wp_send_json([
            "status"    => false,
            'message'   => "Please provide a valid comment",
        ]);
    }


}

// ------------- UPDATE Generic Comments ---------------

/**
 * Require parm:
 * 
 * comment_content = text,
 * comment_id = number/text
 * 
 */

add_action( 'rest_api_init', function () {
    register_rest_route( 'el-dashboard-api', '/generic-comments', [
        'methods' => 'PUT',
        'callback' => 'el_update_generic_comments',
    ]);
} );


function el_update_generic_comments($ReqObj){
  

    
    $received_data = $ReqObj->get_json_params();

    if( isset($received_data['comment_content']) &&  isset($received_data['comment_id']) ){

        $comment_id         = intval($received_data['comment_id']);
        $comment_content    = sanitize_textarea_field($received_data['comment_content']);

        // get comment 
        $comment            = get_comment( $comment_id );

        // 
        if( $comment ){
            $comment_user_id = $comment->user_id;

            $user_matched = $comment_user_id === get_current_user_id(  ) ? true : false;

            $user_matched = true; // testing

            // check is user create the comment 
            if($user_matched){

                $update_status = wp_update_comment( [
                    'comment_ID'        => $comment_id,
                    'comment_content'   => $comment_content
                ]);

                if(  $update_status == 1 ){
                    return wp_send_json([
                        'status'    => true,
                        'message'   => "Your comment updated!"
                    ]);
                }


                if( is_wp_error( $update_status ) || $update_status == 0 ){
                    return wp_send_json([
                        'status'    => false,
                        'message'   => "Something is wrong while updating!"
                    ]);
                }

            }else{
                return wp_send_json( [
                    'status'    => false,
                    'message'   => "Your are not authorize to update this comment."
                ]);
            }


        }else{
            return wp_send_json( [
                'status'    => false,
                'message'   => "Comment not found"
            ]);
        }

        
    }else{
        return wp_send_json( [
            'status'    => false,
            'message'   => "Please provide comment text / Comment id"
        ]);
    }


}



// ------------- DELETE Generic Comments ---------------
/**
 * Require parm:
 * 
 * comment_id = number/text
 * 
 */

add_action( 'rest_api_init', function () {
    register_rest_route( 'el-dashboard-api', '/generic-comments', [
        'methods' => 'DELETE',
        'callback' => 'el_delete_generic_comments',
    ]);
} );

function el_delete_generic_comments($ReqObj){

    
    $received_data = $ReqObj->get_json_params();

    $comment_id         = intval($received_data['comment_id']);

    // get comment 
    $comment            = get_comment( $comment_id );

    // 
    if( $comment_id && $comment ){
        $comment_user_id = $comment->user_id;

        $user_matched = $comment_user_id === get_current_user_id(  ) ? true : false;

        $user_matched = true; // testing

        // check is user create the comment 
        if($user_matched){

            $delete_status = wp_delete_comment($comment_id);

            if($delete_status){
                return wp_send_json( [
                    'status'    => true,
                    'message'   => "Your comment deleted!"
                ]);    
            }else{
                return wp_send_json( [
                    'status'    => false,
                    'message'   => "Something is wrong while delete the Comment"
                ]);    
            }

        }else{
            return wp_send_json( [
                'status'    => false,
                'message'   => "Your are not authorize to delete this comment."
            ]);
        }
    }else{
        return wp_send_json( [
            'status'    => false,
            'message'   => "Please provide Valid Comment id"
        ]);
    }


}