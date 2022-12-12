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
        'login_user_id' => get_current_user_id(),
    ]);
});

function el_get_generic_comments($ReqObj){
    
    // for testing
    if(el_has_rest_Authority($ReqObj) == true || true ){
        // has admin access 
        



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


        // Filter by year
        $comment_year   =  intval(  $ReqObj->get_param('comment_year') )  ?  intval(  $ReqObj->get_param('comment_year') ) : date("Y");
        if( $comment_year ){
            $args['date_query'] = [
                'year' => $comment_year
            ];
        }
        

        $comments_query = new WP_Comment_Query;
        $comments = $comments_query->query($args);

        return wp_send_json([
            'status' => true,
            'sent_args' => $args,
            'data'  =>$comments
        ]);

    

    }else{
        return wp_send_json([
            'status'    => false,
            'data'      => 'Please login to get the comments'
        ]);
    }


}


// ------------- ADD Generic Comments ---------------

add_action( 'rest_api_init', function () {
   

    register_rest_route( 'el-dashboard-api', '/generic-comments', [
        'methods' => 'POST',
        'callback' => 'el_add_generic_comments',
        'login_user_id' => get_current_user_id(),
    ]);

} );

function el_add_generic_comments($ReqObj){

    if(el_has_rest_Authority($ReqObj) == true ){

        // get user info
        $attrs              =  $ReqObj->get_attributes();
        $current_user_id    = intval($attrs['login_user_id']);
        $current_user       = get_user_by( 'id', $current_user_id );



        $received_data = $ReqObj->get_json_params();


        if( isset($received_data['comment_content']) ){
            $comment_content    = sanitize_textarea_field(  $received_data['comment_content'] );
            $email              = $current_user->user_email;


            // Insert the comment (Return : int|false)
            $is_inserted = wp_insert_comment([
                'comment_approved'  => 1,
                'user_id'           => $current_user_id,
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
                    'currentuser' => get_current_user_id(  ),
                ]);
            }

            if( $is_inserted > 0 ){
                return wp_send_json([
                    "status"    => true,
                    'message'   => "Your Comment Added",
                    'dev_message'   => "Comment id ".$is_inserted,
                    'currentuser' => get_current_user_id(  ),

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
        'login_user_id' => get_current_user_id(),

    ]);
} );


function el_update_generic_comments($ReqObj){
  

    if(el_has_rest_Authority($ReqObj) == true ){
        
        $received_data = $ReqObj->get_json_params();

        $attrs              =  $ReqObj->get_attributes();
        $current_user_id    = intval($attrs['login_user_id']);
        $current_user       = get_user_by( 'id', $current_user_id );

        if( 
            isset($received_data['comment_content']) &&  
            isset($received_data['comment_id']) &&
            trim($received_data['comment_content'])   
        ){

            $comment_id         = intval($received_data['comment_id']);
            $comment_content    = sanitize_textarea_field($received_data['comment_content']);

            // get comment 
            $comment            = get_comment( $comment_id );

            // 
            if( $comment ){
                $comment_user_id = intval($comment->user_id);

                $user_matched = $comment_user_id === $current_user_id ? true : false;

                // $user_matched = true; // testing

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

    }else{
        return wp_send_json([
            'status'    => false,
            'data'      => 'Please login to get the comments or you have to be admin'
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
        'login_user_id' => get_current_user_id(),
    ]);
} );

function el_delete_generic_comments($ReqObj){

    if(el_has_rest_Authority($ReqObj) == true ){
        
        // get comment 
        $received_data      = $ReqObj->get_json_params();
        $comment_id         = intval($received_data['comment_id']);
        $comment            = get_comment( $comment_id );

        // get current user
        $attrs              =  $ReqObj->get_attributes();
        $current_user_id    = intval($attrs['login_user_id']);
        $current_user       = get_user_by( 'id', $current_user_id );



        // 
        if( $comment_id && $comment ){
            $comment_user_id = $comment->user_id;

            $user_matched = $comment_user_id === $current_user_id ? true : false;

            // $user_matched = true; // testing

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
                'message'   => "Please provide Valid Comment ID!"
            ]);
        }

    }else{
        return wp_send_json([
            'status'    => false,
            'data'      => 'Please login to get the comments or you have to be admin'
        ]);
    }
}