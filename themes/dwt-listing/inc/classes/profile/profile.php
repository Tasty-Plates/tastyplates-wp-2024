<?php
if (!class_exists('dwt_listing_profile')) {

    class dwt_listing_profile
    {
        // user object
        var $user_info;

        public function __construct()
        {
            $this->user_info = get_userdata(get_current_user_id());
        }

        // Get User Listings
        function dwt_listing_user_posted_listings($args, $listing_status)
        {
            $all_listings = new dwt_listing_listings();
            $all_listings->dwt_listing_get_all_listings($args, $listing_status);
        }

        //Fetch all owner listings
        function dwt_listing_fetch_owner_listings($listing_status, $paged)
        {
            $meta_query_args = array();
            $meta_key = '';
            $meta_value = '';
            $meta_compare = '=';
            if ($listing_status == 'publish') {
                $listing_status = 'publish';
                $meta_query_args = array(array('key' => 'dwt_listing_listing_status', 'value' => 1, 'compare' => '='));
            } else if ($listing_status == 'pending') {
                $listing_status = 'pending';
                $meta_query_args = array(array('key' => 'dwt_listing_listing_status', 'value' => 1, 'compare' => '='));
            } else if ($listing_status == 'featured') {
                $listing_status = 'publish';
                $meta_key = 'dwt_listing_is_feature';
                $meta_value = '1';
            } else if ($listing_status == 'expired') {
                $listing_status = 'publish';
                $meta_key = 'dwt_listing_listing_status';
                $meta_value = '0';
            } else if ($listing_status == 'trash') {
                $listing_status = 'trash';
                $meta_key = 'dwt_listing_listing_status';
                $meta_value = '0';
            } else {
                $listing_status = 'publish';
                $meta_query_args = array(array('key' => 'dwt_listing_listing_status', 'value' => 1, 'compare' => '='));
            }
            $args = array
            (
                'post_type' => 'listing',
                'author' => $this->user_info->ID,
                'post_status' => $listing_status,
                'posts_per_page' => get_option('posts_per_page'),
                'paged' => $paged,
                'order' => 'DESC',
                'orderby' => 'date',
                'meta_key' => $meta_key,
                'meta_value' => $meta_value,
                'meta_query' => $meta_query_args
            );
            $args = dwt_listing_wpml_show_all_posts_callback($args);
            return $args;
        }

        //Fetch all bookmarks
        function dwt_listing_fetch_bookmark_listings($listing_status, $paged)
        {
            global $wpdb, $dwt_listing_options;
            $listing_status = 'favourite';
            $user_id = $this->user_info->ID;
            $rows = $wpdb->get_results("SELECT meta_value FROM $wpdb->usermeta WHERE user_id = '$user_id' AND meta_key LIKE 'dwt_listing_fav_listing_id%'");
            $listing_idz = array(0);
            foreach ($rows as $row) {
                //get curent post language by id
                $post_language_information = apply_filters('wpml_post_language_details', NULL, $row->meta_value);
                //fetch language code
                $curent_post_lang_code = isset($post_language_information['language_code']) ? $post_language_information['language_code'] : '';
                //get current selected language code
                $page_current_lang = apply_filters('wpml_current_language', NULL);
                //if ($dwt_listing_options['dwt_listing_display_post'] === false) {
                if ($curent_post_lang_code == $page_current_lang) {
                    $listing_idz[] = $row->meta_value;
                }
            }
            $args = array(
                'post_type' => 'listing',
                'post__in' => $listing_idz,
                'post_status' => 'publish',
                'posts_per_page' => get_option('posts_per_page'),
                'paged' => $paged,
                'order' => 'DESC',
                'orderby' => 'ID'
            );
            return $args;
        }

        //Fetch owner listings for events
        function dwt_listing_fetch_my_listings()
        {
            $meta_query_args = array(array('key' => 'dwt_listing_listing_status', 'value' => 1, 'compare' => '='));
            $args = array
            (
                'post_type' => 'listing',
                'author' => $this->user_info->ID,
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'order' => 'DESC',
                'orderby' => 'date',
                'meta_query' => $meta_query_args
            );
            return $args;
        }

        //Fetch all events
        function dwt_listing_fetch_owner_events($listing_status, $paged)
        {
            $meta_query_args = array();
            $meta_key = '';
            $meta_value = '';
            $meta_compare = '=';
            if ($listing_status == 'publish') {
                $listing_status = 'publish';
                $meta_query_args = array(array('key' => 'dwt_listing_event_status', 'value' => 1, 'compare' => '='));
            } else if ($listing_status == 'pending') {
                $listing_status = 'pending';
                $meta_query_args = array(array('key' => 'dwt_listing_event_status', 'value' => 1, 'compare' => '='));
            } else if ($listing_status == 'expired') {
                $listing_status = 'publish';
                $meta_key = 'dwt_listing_event_status';
                $meta_value = '0';
            } else {
                $listing_status = 'publish';
                $meta_query_args = array(array('key' => 'dwt_listing_event_status', 'value' => 1, 'compare' => '='));
            }
            $args = array
            (
                'post_type' => 'events',
                'author' => $this->user_info->ID,
                'post_status' => $listing_status,
                'posts_per_page' => get_option('posts_per_page'),
                'paged' => $paged,
                'order' => 'DESC',
                'orderby' => 'date',
                'meta_key' => $meta_key,
                'meta_value' => $meta_value,
                'meta_query' => $meta_query_args
            );
            $args = dwt_listing_wpml_show_all_posts_callback($args);
            return $args;
        }

        // Latest Events
        function dwt_listing_fetch_owner_events_admin()
        {

            $listing_status = 'publish';
            $meta_query_args = array(array('key' => 'dwt_listing_event_status', 'value' => 1, 'compare' => '='));
            $args = array
            (
                'post_type' => 'events',
                'author' => $this->user_info->ID,
                'post_status' => $listing_status,
                'posts_per_page' => 7,
                'order' => 'DESC',
                'orderby' => 'date',
                'meta_query' => $meta_query_args
            );
            return $args;
        }

        //Fetch all events for public users
        function dwt_listing_users_public_eventz($listing_status, $paged, $user_id)
        {
            $meta_query_args = array();
            $listing_status = 'publish';
            $meta_query_args = array(array('key' => 'dwt_listing_event_status', 'value' => 1, 'compare' => '='));
            $args = array
            (
                'post_type' => 'events',
                'author' => $user_id,
                'post_status' => $listing_status,
                'posts_per_page' => get_option('posts_per_page'),
                'paged' => $paged,
                'order' => 'DESC',
                'orderby' => 'date',
                'meta_query' => $meta_query_args
            );
            return $args;
        }

    }

}
// Ajax handler for update profile
add_action('wp_ajax_dwt_listing_profile_update', 'dwt_listing_update_proflie');
if (!function_exists('dwt_listing_update_proflie')) {

    function dwt_listing_update_proflie()
    {
        global $dwt_listing_options;
        $params = array();
        parse_str($_POST['collect_data'], $params);
        $user_name = sanitize_text_field($params['user-name']);
        $phon_no = sanitize_text_field($params['phon-no']);
        $location = sanitize_text_field($params['location']);
        $user_timezone = sanitize_text_field($params['user_timezome']);
        $about = ($params['about-yourself']);
        //social links
        $facebook = sanitize_text_field($params['social-facebook']);
        $twitter = sanitize_text_field($params['social-twitter']);
        $google = sanitize_text_field($params['social-google']);
        $linkedin = sanitize_text_field($params['social-linkedin']);
        $youtube = sanitize_text_field($params['social-youtube']);
        $instagram = sanitize_text_field($params['social-insta']);
        $hours_type = sanitize_text_field($params['my_hours_type']);
        //Profile updation
        $profile = new dwt_listing_profile();
        $uid = $profile->user_info->ID;

        wp_update_user(array('ID' => $uid, 'display_name' => $user_name));
        update_user_meta($uid, 'd_user_contact', $phon_no);
        update_user_meta($uid, 'd_user_location', $location);
        update_user_meta($uid, 'd_user_timezone', $user_timezone);
        update_user_meta($uid, 'd_about_user', $about);
        update_user_meta($uid, 'd_fb_link', $facebook);
        update_user_meta($uid, 'd_twitter_link', $twitter);
        update_user_meta($uid, 'd_google_link', $google);
        update_user_meta($uid, 'd_linked_link', $linkedin);
        update_user_meta($uid, 'd_youtube_link', $youtube);
        update_user_meta($uid, 'd_insta_link', $instagram);
        if ($hours_type == '24') {
            update_user_meta($uid, 'dwt_listing_user_hours_type', '24');
        } else {
            update_user_meta($uid, 'dwt_listing_user_hours_type', '12');
        }
        echo '1';
        die();
    }

}

add_action('wp_ajax_dwt_listing_resetmy', 'dwt_listing_resetmyPass');
// Reset Password
if (!function_exists('dwt_listing_resetmyPass')) {

    function dwt_listing_resetmyPass()
    {
        global $dwt_listing_options;
        //get user id
        $profile = new dwt_listing_profile();
        $uid = $profile->user_info->ID;
        $params = array();
        parse_str($_POST['collect_data'], $params);
        $new_password = sanitize_text_field($params['dwt_listing_mypass']);
        wp_set_password($new_password, $uid);
        echo '1';
        die();
    }

}

add_action('wp_ajax_upload_user_pic', 'dwt_listing_user_profile_pic');
if (!function_exists('dwt_listing_user_profile_pic')) {

    function dwt_listing_user_profile_pic()
    {
        /* img upload */
        $condition_img = 7;
        $img_count = count(explode(',', $_POST["image_gallery"]));

        if (!empty($_FILES["my_file_upload"])) {

            require_once ABSPATH . 'wp-admin/includes/image.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';

            $files = $_FILES["my_file_upload"];

            foreach ($files['name'] as $key => $value) {
                if ($files['name'][$key]) {
                    $file = array(
                        'name' => $files['name'][$key],
                        'type' => $files['type'][$key],
                        'tmp_name' => $files['tmp_name'][$key],
                        'error' => $files['error'][$key],
                        'size' => $files['size'][$key]
                    );

                    $_FILES = array("my_file_upload" => $file);
                    // Allow certain file formats
                    $imageFileType = end(explode('.', $file['name']));
                    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                        echo '2';
                        die();
                    }
                    foreach ($_FILES as $file => $array) {
                        if ($imgcount >= $condition_img) {
                            break;
                        }
                        $attach_id = media_handle_upload($file, $post_id);
                        $attachment_ids[] = $attach_id;
                        $image_link = wp_get_attachment_image_src($attach_id, 'dwt_listing_user-profile');
                    }
                    if ($imgcount > $condition_img) {
                        break;
                    }
                    $imgcount++;
                }
            }
        }
        /* img upload */
        $attachment_idss = array_filter($attachment_ids);
        $attachment_idss = implode(',', $attachment_idss);


        $arr = array();
        $arr['attachment_idss'] = $attachment_idss;
        $arr['ul_con'] = $ul_con;

        $profile = new dwt_listing_profile();
        $uid = $profile->user_info->ID;
        update_user_meta($uid, 'dwt_listing_user_pic', $attach_id);
        echo '1|' . $image_link[0];
        die();
    }

}


// Ajax handler for add to cart
add_action('wp_ajax_dwt_listing_package_cart', 'dwt_listing_add_to_cart');
add_action('wp_ajax_nopriv_dwt_listing_package_cart', 'dwt_listing_add_to_cart');
if (!function_exists('dwt_listing_add_to_cart')) {

    function dwt_listing_add_to_cart()
    {
        global $dwt_listing_options;
        global $woocommerce;
        if (get_current_user_id() == "") {
            echo '1';
            die();
        }
        $link = '';
        $link = function_exists('wc_get_cart_url') ? wc_get_cart_url() : $woocommerce->cart->get_cart_url();
        $product_id = $_POST['package_id'];
        $qty = $_POST['qty'];
        $package_type = $_POST['package_refer'];
        //check package type
        if (is_user_logged_in() && $package_type == 'free') {
            $profile = new dwt_listing_profile();
            $uid = $profile->user_info->ID;
            update_user_meta($uid, 'd_user_package_id', $product_id);
            update_user_meta($uid, 'd_is_free_pgk', $product_id);
            dwt_listing_store_user_package($uid, $product_id);
            $woocommerce->cart->empty_cart();
            echo dwt_listing_pagelink('dwt_listing_header-page');
            die();
        } else {
            if ($woocommerce->cart->add_to_cart($product_id, $qty)) {
                echo '' . $link;
            } else {
                echo '' . $link;
            }
        }
        die();
    }

}

if (!function_exists('dwt_listing_adding_into_cart')) {

    function dwt_listing_adding_into_cart($package_id, $quantity)
    {
        global $woocommerce;
        $woocommerce->cart->add_to_cart($package_id, $quantity);
    }

}


if (!function_exists('dwt_listing_hide_package_quantity')) {

    function dwt_listing_hide_package_quantity($return, $product)
    {
        if ($product->get_type() == 'dwt_listing_pkgs' || $product->get_type() == 'subscription') {
            return true;
        } else {
            return false;
        }
    }

}
add_filter('woocommerce_is_sold_individually', 'dwt_listing_hide_package_quantity', 10, 2);

// Bookmark Favourite Listing
add_action('wp_ajax_dwt_listing_listing_bookmark', 'dwt_listing_make_bookmark');
add_action('wp_ajax_nopriv_dwt_listing_listing_bookmark', 'dwt_listing_make_bookmark');
if (!function_exists('dwt_listing_make_bookmark')) {

    function dwt_listing_make_bookmark()
    {
        //check user is logged in or not
        dwt_listing_authenticate_check();
        $listing_id = $_POST['listing_id'];
        if (get_user_meta(get_current_user_id(), 'dwt_listing_fav_listing_id' . $listing_id, true) == $listing_id) {
            echo '0|' . __("You have bookmark this listing already.", 'dwt-listing');
        } else {
            update_user_meta(get_current_user_id(), 'dwt_listing_fav_listing_id' . $listing_id, $listing_id);
            echo '1|' . __("Added to your favourites.", 'dwt-listing');
        }
        die();
    }

}

// compare listings ajax is here 


//Compare Listings
add_action('wp_ajax_mw_listing_compare', 'dwt_framework_compare_listings');
add_action('wp_ajax_nopriv_mw_listing_compare', 'dwt_framework_compare_listings');
if (!function_exists('dwt_framework_compare_listings'))
{
    function dwt_framework_compare_listings()
	{
        
		// if(!wp_verify_nonce($_POST['security'], 'check-security'))
		// {
		// 	$return = array('message' => esc_html__('Invalid security token sent.', 'dwt-listing'));
		// 	wp_send_json_error($return);
		// }
        $listing_id = $_POST['listing_id'];
		if(isset($_POST['compare_listing']) && $_POST['compare_listing'] != "")
		{
        
            $compare_listing = (int)$_POST['compare_listing'];
 
           
                 session_start();
                 if (!isset($_SESSION['compare_listings']))
                 {
                     $_SESSION['compare_listings'] = array();
                 }
                 if( is_array( $_SESSION[ 'compare_listings' ] ) && !empty($_SESSION[ 'compare_listings' ] ) && in_array( $compare_listing, $_SESSION[ 'compare_listings' ] ))
                 {
                     if (($key = array_search($compare_listing,  $_SESSION[ 'compare_listings' ])) !== false) {
                      unset($_SESSION['compare_listings'][$key]);
        
                     }
                 }
                 else if(!empty($_SESSION[ 'compare_listings' ] ) && count($_SESSION[ 'compare_listings' ] ) >= 3)
                 {
                     $return = array('message' => esc_html__('There was an error while processing your request. Please, reload the page and try again.', 'dwt-listing'));
			         wp_send_json_error($return);
                 }
                 else
                 {
                   array_push($_SESSION['compare_listings'], $compare_listing);
                 }

            $custom_msg = $all_idz = $compare_list = '';
            if(!empty($_SESSION['compare_listings']) && is_array($_SESSION['compare_listings']) && count($_SESSION['compare_listings']) > 0)
            {
               $i = 1;
               $page_link = dwt_framwork_get_link('page-compare.php');
               foreach($_SESSION['compare_listings'] as $listing_id)
               {
                   $all_idz = '';
                   $all_idz = dwt_listing_fetch_listing_gallery($listing_id);
                   if ($i == 2 || $i == 3){ $compare_list .='<div class="vsbox">vs</div>'; }
                   $compare_list .='<div class="compare-listings-box">
                    <a href="javascript:void(0)" class="remove_compare_list" data-property-id="'.esc_attr($listing_id).'"><i class="fas fa-times"></i></a>
                     <a class="clr-black" href="'.esc_url(get_the_permalink($listing_id)).'"><img class="img-fluid" src="'.esc_url(dwt_listing_return_listing_idz($all_idz,'maxwheels-blog-thumb')).'" alt="'.esc_attr(get_post_meta($all_idz, '_wp_attachment_image_alt', TRUE)).'"></a>
                    </div>';
                   
                   $i++;
                   
               }
               $compare_list .='<div class="compare-action-btns"><a class="btn btn-theme btn-block btn-custom-sm" href="'.esc_url($page_link).'">'.esc_html__('Compare','dwt-listing').'</a>
                    <a class="btn btn-warning btn-block btn-custom-sm clear-all-compare">'.esc_html__('Clear All','dwt-listing').'</a></div>';
                $return = array('compare_list' => $compare_list);
                wp_send_json_success($return);  
            }
            else
            {
                $return = array('custom_msg' => dwt_framework_get_options('mw_empty_list'));
                wp_send_json_error($return);  
            }

        }
        else
		{
			$return = array('message' => esc_html__('There was an error while processing your request. Please, reload the page and try again.', 'dwt-listing'));
			wp_send_json_error($return);
		}
    }
}


//Clear All Compare Listings
add_action('wp_ajax_mw_listing_compare_clear', 'dwt_listings_framwork_clear_all');
add_action('wp_ajax_nopriv_mw_listing_compare_clear', 'dwt_listings_framwork_clear_all');
if (!function_exists('dwt_listings_framwork_clear_all'))
{
    function dwt_listings_framwork_clear_all()
	{
            session_start();
            unset($_SESSION['compare_listings']);
            $compare_list = array();
            $return = array('compare_list' => $compare_list);
            wp_send_json_success($return);  
    }
}





// Get Listing Owner Details
if (!function_exists('dwt_listing_listing_owner')) {

    function dwt_listing_listing_owner($listing_id, $field = '')
    {
        //get listing owner id
        $get_owner_id = get_post_field('post_author', $listing_id);
        //get user data
        $user_info = get_userdata($get_owner_id);
        if ($user_info != "") {
            if ($field == 'id') {
                return $get_owner_id = $get_owner_id;
            }
            if ($field == 'dp') {
                return dwt_listing_get_user_dp($get_owner_id, 'dwt_listing_user-dp');
            }
            if ($field == 'name') {
                return $user_info->display_name;
            }
            if ($field == 'email') {
                return $user_info->user_email;
            }
            if ($field == 'location') {
                return $user_info->d_user_location;
            }
            if ($field == 'url') {
                $author_posts_url = '';
                //$author_posts_url = dwt_listing_set_url_param(get_author_posts_url($get_owner_id), 'type', 'listings');
                $author_posts_url = dwt_listing_set_url_params_multi(get_author_posts_url($get_owner_id), array('type' => 'listings'));
                return esc_url(dwt_listing_page_lang_url_callback($author_posts_url));
            }
            if ($field == 'contact') {
                return $user_info->d_user_contact;
            }
        } else {
            return '';
        }
    }

}

// Remove Ad
add_action('wp_ajax_remove_my_listing', 'dowtown_delete_my_listing');
if (!function_exists('dowtown_delete_my_listing')) {

    function dowtown_delete_my_listing()
    {
        //check user is logged in or not
        dwt_listing_authenticate_check();
        if (isset($_POST['listing_id']) && $_POST['listing_id'] != "") {
            $listing_id = $_POST['listing_id'];
            if (wp_trash_post($listing_id)) {
                echo '1|' . esc_html__("Listing removed successfully.", 'dwt-listing');
            } else {
                echo '0|' . esc_html__("There's some problem, please try again later.", 'dwt-listing');
            }
        }
        die();
    }

}

/* == remove timekit booking listing from dashboard  == */
add_action('wp_ajax_remove_my_listing_timekit', 'dowtown_delete_my_listing_timekit');
if (!function_exists('dowtown_delete_my_listing_timekit')) {

    function dowtown_delete_my_listing_timekit()
    {

        if (isset($_POST['listing_id']) && $_POST['listing_id'] != "") {
            if (dwt_listing_text('dwt_listing_disable_edit') == '1' && !is_super_admin(get_current_user_id())) {
                echo '0|' . __("Disable for Demo.", 'dwt-listing');
                die();
            }
            $listing_id = $_POST['listing_id'];
            update_post_meta($listing_id, 'dwt-listing-timekit-booking', '');
            update_post_meta($listing_id, 'dwt-listing-timekit-booking-status', '0');
            echo '1|' . esc_html__("Booking removed successfully.", 'dwt-listing');
        } else {
            echo '0|' . esc_html__("There's some problem, please try again later.", 'dwt-listing');
        }
        die();
    }

}

/* == Add timekit form with listing ID from admin dashboard == */
add_action('wp_ajax_add_timekit_form_with_listing', 'add_timekit_form_with_listing');
if (!function_exists('add_timekit_form_with_listing')) {

    function add_timekit_form_with_listing()
    {
        $select_list_id = $form_code = '';
        if (dwt_listing_text('dwt_listing_disable_edit') == '1' && !is_super_admin(get_current_user_id())) {
            echo '0|' . __("Disable for Demo.", 'dwt-listing');
            die();
        }
        if (isset($_POST['select_list_id']) && $_POST['select_list_id'] != "" && isset($_POST['form_code']) && $_POST['form_code'] != "") {
            $select_list_id = $_POST['select_list_id'];
            $form_code = $_POST['form_code'];
            if (get_post_meta($select_list_id, 'dwt-listing-timekit-booking-status', true) != '' && get_post_meta($select_list_id, 'dwt-listing-timekit-booking-status', true) != '0') {
                echo '2|' . esc_html__("You have already enabled booking on this listing.", 'dwt-listing');
                die();
            } else {
                $booking_status = '1';
                update_post_meta($select_list_id, 'dwt-listing-timekit-booking', htmlspecialchars($form_code));
                update_post_meta($select_list_id, 'dwt-listing-timekit-booking-status', $booking_status);
                echo '1|' . esc_html__("Booking added successfully.", 'dwt-listing');
                die();
            }
        }
        die();
    }

}

// Remove Submitted Reviews
add_action('wp_ajax_remove_my_submitted_reviews', 'dowtown_remove_my_submitted_reviews');
if (!function_exists('dowtown_remove_my_submitted_reviews')) {

    function dowtown_remove_my_submitted_reviews()
    {
        //check user is logged in or not
        dwt_listing_authenticate_check();
        if (isset($_POST['comment_id']) && $_POST['comment_id'] != "") {
            $comment_id = $_POST['comment_id'];
            if (isset($_POST['listing_id']) && $_POST['listing_id'] != "") {
                $listing_id = $_POST['listing_id'];
                delete_post_meta($listing_id, '_activity_comments_userid_' . get_current_user_id() . $comment_id);
                delete_post_meta($listing_id, '_activity_rating_userid_' . get_current_user_id() . $comment_id);
                wp_delete_comment($comment_id, true);
                echo '1|' . esc_html__("Submited review removed successfully.", 'dwt-listing');
            }
        }
        die();
    }

}

// User total Listings
if (!function_exists('dwt_listing_get_all_listing_count')) {

    function dwt_listing_get_all_listing_count($user_id)
    {
        global $wpdb;
        $listing_count = $wpdb->get_var("SELECT COUNT(*) AS total FROM  $wpdb->posts WHERE post_type = 'listing' AND post_author = '$user_id'");
        return dwt_listing_number_format_short($listing_count);
    }

}

// User Pendings Listings
if (!function_exists('dwt_listing_get_pending_listing_count')) {

    function dwt_listing_get_pending_listing_count($user_id)
    {
        global $wpdb;
        $listing_count = $wpdb->get_var("SELECT COUNT(*) AS total FROM  $wpdb->posts WHERE post_type = 'listing' AND post_status = 'pending' AND post_author = '$user_id'");
        return dwt_listing_number_format_short($listing_count);
    }

}

// User Featured Listings
if (!function_exists('dwt_listing_featured_listing_count')) {

    function dwt_listing_featured_listing_count($user_id)
    {
        global $wpdb;

        $rows = $wpdb->get_results("SELECT meta_value FROM $wpdb->usermeta WHERE user_id = '$user_id' AND meta_key LIKE 'dwt_listing_fav_listing_id%'");
        $listing_count = 0;
        foreach ($rows as $row) {
            if (get_post_status($row->meta_value) == 'publish') {
                $listing_count++;
            }
        }
        return dwt_listing_number_format_short($listing_count);
    }

}

// User Submitted Reviews
if (!function_exists('dwt_listing_submitted_reviews')) {

    function dwt_listing_submitted_reviews($user_id)
    {
        $param = array('status' => 'approve', 'post_type' => 'listing', 'author__in' => array($user_id), 'parent' => 0);
        $comments = get_comments($param);
        $total = count($comments);
        return dwt_listing_number_format_short($total);
    }

}

// User Received Reviews
if (!function_exists('dwt_listing_received_reviews')) {

    function dwt_listing_received_reviews($user_id)
    {
        $param = array('status' => 'approve', 'post_type' => 'listing', 'post_author__in' => array($user_id), 'parent' => 0);
        $comments = get_comments($param);
        $total = count($comments);
        return dwt_listing_number_format_short($total);
    }

}


// User Expired Listings
if (!function_exists('dwt_listing_get_listing_status_count')) {

    function dwt_listing_get_listing_status_count($user_id, $status)
    {
        $count = 0;
        $args = array(
            'post_type' => 'listing',
            'author' => $user_id,
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'dwt_listing_listing_status',
                    'value' => $status,
                    'compare' => '=',
                ),
            ),
        );
        $args = dwt_listing_wpml_show_all_posts_callback($args);
        $query = new WP_Query($args);
        if ($query->have_posts()) {
            return dwt_listing_number_format_short($query->found_posts);
        } else {
            return dwt_listing_number_format_short($count);
        }
    }

}

// User Trashed  Listings
if (!function_exists('dwt_listing_get_listing_status_count_trash')) {

    function dwt_listing_get_listing_status_count_trash($user_id)
    {
        $count = 0;
        $args = array('post_type' => 'listing', 'author' => $user_id, 'post_status' => 'trash',
            'meta_query' => array(
                array(
                    'key' => 'dwt_listing_listing_status',
                    'value' => 0,
                    'compare' => '=',
                ),
            ),
        );
        $args = dwt_listing_wpml_show_all_posts_callback($args);
        $query = new WP_Query($args);
        if ($query->have_posts()) {
            return dwt_listing_number_format_short($query->post_count);
        } else {
            return dwt_listing_number_format_short($count);
        }
    }

}


// Remove Fav bookmark listings 
add_action('wp_ajax_remove_fav_bookmark', 'dwt_listing_remove_fav_bookmarks');
if (!function_exists('dwt_listing_remove_fav_bookmarks')) {

    function dwt_listing_remove_fav_bookmarks()
    {
        //check user is logged in or not
        dwt_listing_authenticate_check();
        if (isset($_POST['listing_id']) && $_POST['listing_id'] != "") {
            $listing_id = $_POST['listing_id'];
            if (delete_user_meta(get_current_user_id(), 'dwt_listing_fav_listing_id' . $listing_id)) {
                echo '1|' . esc_html__("Listing removed successfully.", 'dwt-listing');
            } else {
                echo '0|' . esc_html__("There's some problem, please try again later.", 'dwt-listing');
            }
        }
        die();
    }

}


// Fetch users leads & activities
if (!function_exists('dwt_listing_fetch_leads_activities')) {

    function dwt_listing_fetch_leads_activities($user_id)
    {
        global $wpdb;
        global $dwt_listing_options;
        //thumbup
        $thumb_up = esc_url(trailingslashit(get_template_directory_uri()) . 'assets/images/ratings/like.png');
        $heart = esc_url(trailingslashit(get_template_directory_uri()) . 'assets/images/ratings/heart.png');
        $wow = esc_url(trailingslashit(get_template_directory_uri()) . 'assets/images/ratings/wow.png');
        $angry = esc_url(trailingslashit(get_template_directory_uri()) . 'assets/images/ratings/angry.png');
        $smartphone = esc_url(trailingslashit(get_template_directory_uri()) . 'assets/images/ratings/smartphone.png');
        $link = esc_url(trailingslashit(get_template_directory_uri()) . 'assets/images/ratings/link.png');

        $likes = array(1 => esc_html__('Like', 'dwt-listing'), 2 => esc_html__('Love', 'dwt-listing'), 3 => esc_html__('Wow', 'dwt-listing'), 4 => esc_html__('Angry', 'dwt-listing'));
        $emotion_icons = array(1 => $thumb_up, 2 => $heart, 3 => $wow, 4 => $angry);
        $leads = array('contact' => esc_html__('Contact No', 'dwt-listing'), 'web' => esc_html__('Website Link', 'dwt-listing'));
        $leads_icons = array('contact' => $smartphone, 'web' => $link);
        $query_args = array('author' => $user_id, 'post_type' => 'listing', 'post_status' => 'publish');
        $query = new WP_Query($query_args);
        $listing_id = '';
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $listing_id .= get_the_ID() . ',';
            }
        }
        $listing_id = rtrim($listing_id, ",");
        if (!empty($listing_id)) {
            $html = '';
            //pagination
            $per_page = 10;
            if (isset($dwt_listing_options['dwt_leads_per_page']) && $dwt_listing_options['dwt_leads_per_page'] != "") {
                $per_page = $dwt_listing_options['dwt_leads_per_page'];
            }
            $page = (get_query_var('page')) ? get_query_var('page') : 1;
            $offset = ($page - 1) * $per_page;

            $results = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE meta_key LIKE '_activity_%' AND post_id IN ($listing_id) ORDER BY meta_id DESC LIMIT " . $offset . ", " . $per_page . "");

            if (is_array($results) && count($results) > 0) {
                $html .= '<ul class="list-unstyled dwt-recent-notification">';
                foreach ($results as $result) {
                    $post_id = $result->post_id;
                    $user_name = '';
                    $icon = '';
                    $statement = '';
                    $profile_url = '';
                    $get_time = '';

                    $get_time = explode('_', $result->meta_value);
                    $activity_time = $get_time[0];
                    $activity_value = $get_time[1];
                    $activity_time = strtotime($activity_time);

                    $reaction = (isset($likes[$activity_value])) ? $likes[$activity_value] : '';
                    $get_uid = explode('_', $result->meta_key);
                    $get_uid[4];
                    $user = get_user_by('id', $get_uid[4]);
                    if (!empty($user)) {

                        $user_name = $user->display_name;

                        $profile_url = get_author_posts_url($get_uid[4]) . '?type=listings';
                    } else {
                        $user_name = esc_html__('Someone', 'dwt-listing');
                        $profile_url = 'javascript:void(0)';
                    }


                    $on = esc_html__(' on', 'dwt-listing');
                    $clicked = esc_html__('clicked your ', 'dwt-listing');
                    $reacted = esc_html__('reacted as ', 'dwt-listing');

                    $type = '';
                    if (strpos($result->meta_key, 'leads_userid_unknown')) {
                        $icon = esc_url(trailingslashit(get_template_directory_uri()) . 'assets/images/ratings/bell.png');
                        $reaction = (isset($leads[$activity_value])) ? $leads[$activity_value] : '';
                        $statement = $clicked . '<strong>' . $reaction . '</strong> ' . $on;
                    } else if (strpos($result->meta_key, 'rating')) {
                        $icon = esc_url(trailingslashit(get_template_directory_uri()) . 'assets/images/ratings/star.png');
                        $statement = __('posted a <strong>Rating</strong> on', 'dwt-listing');
                    } else if (strpos($result->meta_key, 'comments')) {
                        // $type = 'comments';
                        $icon = esc_url(trailingslashit(get_template_directory_uri()) . 'assets/images/ratings/comment.png');
                        $statement = __('post a <strong>Comment</strong> on', 'dwt-listing');
                    } else if (strpos($result->meta_key, 'like')) {
                        $icon = (isset($emotion_icons[$activity_value])) ? $emotion_icons[$activity_value] : '';
                        $statement = $reacted . '<strong>' . $reaction . '</strong> ' . $on;
                    } else if (strpos($result->meta_key, 'leads')) {
                        $icon = (isset($leads_icons[$activity_value])) ? $leads_icons[$activity_value] : '';
                        $reaction = (isset($leads[$activity_value])) ? $leads[$activity_value] : '';
                        $statement = $clicked . '  <strong>' . $reaction . '</strong>' . $on;
                    }
                    $html .= '<li>
						<div class="lead_icon"><img src="' . $icon . '" alt=""></div>
						<p><a href="' . esc_url($profile_url) . '">' . $user_name . '</a> ' . $statement . ' <a href="' . get_the_permalink($post_id) . '">' . get_the_title($post_id) . '</a> <span class="timestamp">' . human_time_diff($activity_time, current_time('timestamp')) . ' ' . esc_html__(' ago', 'dwt-listing') . '</span></p>
					</li>';
                }
                $html .= '</ul>';
                $html .= '<div class="admin-pagination">' . dwt_listing_activities_pagination($listing_id, $per_page, $page) . '</div>';
                wp_reset_postdata();
                return $html;
            } else {
                ?>
                <div class="alert custom-alert custom-alert--warning" role="alert">
                    <div class="custom-alert__top-side">
                        <span class="alert-icon custom-alert__icon  ti-info-alt "></span>
                        <div class="custom-alert__body">
                            <h6 class="custom-alert__heading"><?php echo esc_html__('No Notification !', 'dwt-listing'); ?></h6>
                            <div class="custom-alert__content"><?php echo esc_html__('Recent activities about your listings will be here!', 'dwt-listing'); ?></div>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            ?>
            <div class="alert custom-alert custom-alert--warning" role="alert">
                <div class="custom-alert__top-side">
                    <span class="alert-icon custom-alert__icon  ti-info-alt "></span>
                    <div class="custom-alert__body">
                        <h6 class="custom-alert__heading"><?php echo esc_html__('No Notification !', 'dwt-listing'); ?></h6>
                        <div class="custom-alert__content"><?php echo esc_html__('Recent activities about your listings will be here!', 'dwt-listing'); ?></div>
                    </div>
                </div>
            </div>
            <?php
        }
    }

}


// Remove Listing
add_action('wp_ajax_expire_my_listing', 'dwt_listing_expire_my_current_listing');
if (!function_exists('dwt_listing_expire_my_current_listing')) {

    function dwt_listing_expire_my_current_listing()
    {
        //check user is logged in or not
        dwt_listing_authenticate_check();
        if (isset($_POST['listing_id']) && $_POST['listing_id'] != "") {
            $listing_id = $_POST['listing_id'];
            //zero means its expire
            $status = '0';
            update_post_meta($listing_id, 'dwt_listing_listing_status', $status);
            echo '1|' . esc_html__("Updated successfully.", 'dwt-listing');
        }
        die();
    }

}


// Re Active My Current Listing
add_action('wp_ajax_reactive_my_listing', 'dwt_listing_reactive_my_current_listing');
if (!function_exists('dwt_listing_reactive_my_current_listing')) {

    function dwt_listing_reactive_my_current_listing()
    {
        //check user is logged in or not
        dwt_listing_authenticate_check();
        if (isset($_POST['listing_id']) && $_POST['listing_id'] != "") {
            $listing_id = $_POST['listing_id'];
            //one means its active
            $status = '1';
            update_post_meta($listing_id, 'dwt_listing_listing_status', $status);
            echo '1|' . esc_html__("Listing Active successfully.", 'dwt-listing');
        }
        die();
    }

}

/* Create Event By Title */
 add_action('wp_ajax_create_new_event', 'dwt_listing_create_new_event');
if (!function_exists('dwt_listing_create_new_event')) {

    function dwt_listing_create_new_event()
    {
        if (dwt_listing_text('dwt_listing_disable_edit') == '1' && !is_super_admin(get_current_user_id())) {
            return '';
            die();
        }

        if ($_POST['is_update'] != "") {
            die();
        }
        $event_title = sanitize_text_field($_POST['event_title']);
        if (get_current_user_id() == "")
            die();
        if (!isset($event_title))
            die();
        $event_id = get_user_meta(get_current_user_id(), 'event_in_progress', true);
        if (get_post_status($event_id) && $event_id != "") {
            $my_post = array('ID' => $event_id, 'post_title' => $event_title);
            wp_update_post($my_post);
            die();
        }
        // Gather post data.
        $my_post = array(
            'post_title' => $event_title,
            'post_status' => 'pending',
            'post_author' => get_current_user_id(),
            'post_type' => 'events'
        );
        
        //pasting for FAQs

        // Insert the post into the database.
        $id = wp_insert_post($my_post);
        if ($id) {
            update_user_meta(get_current_user_id(), 'event_in_progress', $id);
        }
        die();
    }

}

/* Create New Event... */
add_action('wp_ajax_my_new_event', 'dwt_listing_my_new_event');
if (!function_exists('dwt_listing_my_new_event')) {

    function dwt_listing_my_new_event()
    {
        global $dwt_listing_options;
        if (dwt_listing_text('dwt_listing_disable_edit') == '1' && !is_super_admin(get_current_user_id())) {
            return '';
            die();
        }
        if (get_current_user_id() == "") {
            echo "0";
            die();
        }

        // Getting values
        $params = array();
        parse_str($_POST['collect_data'], $params);
        $event_desc = ($params['event_desc']);
        $event_title = sanitize_text_field($params['event_title']);
        $event_tagline = sanitize_text_field($params['event_tagline']);
        $event_cat = sanitize_text_field($params['event_cat']);
        $event_number = sanitize_text_field($params['event_number']);
        $event_email = sanitize_text_field($params['event_email']);
        $event_date = sanitize_text_field($params['event_date']);
        $event_start_date = sanitize_text_field($params['event_start_date']);
        $event_end_date = sanitize_text_field($params['event_end_date']);
        $event_venue = sanitize_text_field($params['event_venue']);
        $event_lat = sanitize_text_field($params['event_lat']);
        $event_long = sanitize_text_field($params['event_long']);
        $event_parent_listing = sanitize_text_field($params['event_parent_listing']);
        $event_status = 'publish';
        if ($_POST['is_update'] != "") {
            $event_id = $_POST['is_update'];
            if ($dwt_listing_options['dwt_listing_event_up_approval'] == 'manual') {
                $event_status = 'pending';
            } else if (get_post_status($event_id) == 'pending') {
                $event_status = 'pending';
            }
        } else {
            if ($dwt_listing_options['dwt_listing_event_approval'] == '0') {
                $event_status = 'pending';
            } else {
                $event_status = 'publish';
            }
            $event_id = get_user_meta(get_current_user_id(), 'event_in_progress', true);
            // Now user can post new ad
            delete_user_meta(get_current_user_id(), 'event_in_progress');
            //send email on event creation
            dwt_listing_notify_on_new_event($event_id);
        }
        $my_post = array(
            'ID' => $event_id,
            'post_title' => $event_title,
            'post_status' => $event_status,
            'post_content' => $event_desc,
            'post_name' => $event_title
        );
        wp_update_post($my_post);
        update_post_meta($event_id, 'dwt_listing_event_status', '1');
        update_post_meta($event_id, 'dwt_listing_event_contact', $event_number);
        update_post_meta($event_id, 'dwt_listing_event_email', $event_email);
        update_post_meta($event_id, 'dwt_listing_event_start_date', $event_start_date);
        update_post_meta($event_id, 'dwt_listing_event_end_date', $event_end_date);
        update_post_meta($event_id, 'dwt_listing_event_venue', $event_venue);
        update_post_meta($event_id, 'dwt_listing_event_lat', $event_lat);
        update_post_meta($event_id, 'dwt_listing_event_long', $event_long);
        update_post_meta($event_id, 'dwt_listing_event_listing_id', $event_parent_listing);
        wp_set_post_terms($event_id, $event_cat, 'l_event_cat');


          $countNum   =   isset($params['event_question']['question']) ? count($params['event_question']['question']) : 0;     
          $arr  =  isset($params['event_question'])  ? $params['event_question'] : array();

          $questions   =  array();
        if($countNum  > 0 && !empty( $arr)){
        for ($i = 0; $i <= $countNum; $i++) {
                 
            if (isset($arr['question'][$i]) && $arr['question'][$i] != "") {
                $arrc['question'] = sanitize_text_field($arr['question'][$i]);
                $arrc['answer'] = sanitize_text_field($arr['answer'][$i]);
                $questions[] = $arrc;
            }
        } 
        
        if (!empty($questions)) {
               update_post_meta($event_id, 'event_question', $questions);
           
        }
    }

   
    //adding textarea
    $countNum1   =   isset($params['days_schedule']['newDays']) ? count($params['days_schedule']['newDays']) : 0;     
    $arr1  =  isset($params['days_schedule'])  ? $params['days_schedule'] : array();
    $new_dayss   =  array();
  if($countNum1  > 0 && !empty( $arr1)){
  for ($i = 0; $i <= $countNum; $i++) {
      if (isset($arr1['newDays'][$i]) && $arr1['newDays'][$i] != "") {
          $schedule_arr['newDays'] = sanitize_text_field($arr1['newDays'][$i]);
          $schedule_arr['schedule_description'] = sanitize_text_field($arr1['schedule_description'][$i]);
          $new_dayss[] = $schedule_arr;
      }
  } 
  if (!empty($new_dayss)) {
         update_post_meta($event_id, 'days_schedule', $new_dayss);      
     }
}
        /* == wpml duplicate post if switch on == */
        do_action('dwt_listing_duplicate_posts_lang_wpml', $event_id, 'events');
        $event_update_url = '';
        $event_update_url = dwt_listing_page_lang_url_callback(get_the_permalink($event_id));
        echo $event_update_url;
        die();
    }

}


// Event Images ...
add_action('wp_ajax_upload_dwt_listing_events_images', 'dwt_listing_event_gallery');
if (!function_exists('dwt_listing_event_gallery')) {

    function dwt_listing_event_gallery()
    {
        global $dwt_listing_options;
        if (dwt_listing_text('dwt_listing_disable_edit') == '1' && !is_super_admin(get_current_user_id())) {
            echo '0|' . __("Disable for Demo.", 'dwt-listing');
            die();
        }
        if (isset($dwt_listing_options['dwt_listing_standard_images_size']) && $dwt_listing_options['dwt_listing_standard_images_size']) {
            list($width, $height) = getimagesize($_FILES["my_file_upload"]["tmp_name"]);
            if ($width < 760) {
                echo '0|' . __("Minimum image dimension should be", 'dwt-listing') . ' 750x450';
                die();
            }

            if ($height < 410) {
                echo '0|' . __("Minimum image dimension should be", 'dwt-listing') . ' 750x450';
                die();
            }
        }
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        $size_arr = explode('-', $dwt_listing_options['dwt_listing_event_images_size']);
        $display_size = $size_arr[1];
        $actual_size = $size_arr[0];
        $imageFileType = strtolower(end(explode('.', $_FILES['my_file_upload']['name'])));
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo '0|' . esc_html__("Sorry, only JPG, JPEG, and PNG files are allowed", 'dwt-listing');
            die();
        }
        // Check file size
        if ($_FILES['my_file_upload']['size'] > $actual_size) {
            echo '0|' . esc_html__("Max allowed image size is", 'dwt-listing') . " " . $display_size;
            die();
        }
        if ($_GET['is_update'] != "") {
            $event_id = $_GET['is_update'];
        } else {
            $event_id = get_user_meta(get_current_user_id(), 'event_in_progress', true);
        }

        if ($event_id == "") {
            echo '0|' . __("Please enter event title first in order to create event.", 'dwt-listing');
            die();
        }
        // Check max image limit
        $media = get_attached_media('image', $event_id);
        if (count($media) >= $dwt_listing_options['dwt_listing_event_upload_limit']) {
            $msg = esc_html__("Sorry you cant upload more than ", 'dwt-listing');
            $images_l = esc_html__(" images ", 'dwt-listing');
            echo '0|' . $msg . $dwt_listing_options['dwt_listing_event_upload_limit'] . $images_l;
            die();
        }
        $attachment_id = media_handle_upload('my_file_upload', $event_id);
        if (!is_wp_error($attachment_id)) {
            $imgaes = get_post_meta($event_id, 'downotown_event_arrangement_', true);
            if ($imgaes != "") {
                $imgaes = $imgaes . ',' . $attachment_id;
                update_post_meta($event_id, 'downotown_event_arrangement_', $imgaes);
            } else {
                update_post_meta($event_id, 'downotown_event_arrangement_', $attachment_id);
            }
            echo '' . $attachment_id;
            die();
        } else {
            echo '0|' . esc_html__("Something went wrong please try later", 'dwt-listing');
            die();
        }
    }
}

// Fetch Event Images ...
add_action('wp_ajax_get_event_images', 'dwt_listing_get_uploaded_event_images');
if (!function_exists('dwt_listing_get_uploaded_event_images')) {

    function dwt_listing_get_uploaded_event_images()
    {
        if ($_POST['is_update'] != "") {
            $event_id = $_POST['is_update'];
        } else {
            $event_id = get_user_meta(get_current_user_id(), 'event_in_progress', true);
        }
        if ($event_id == "") {
            return '';
        }
        $path = '';
        $media = dwt_listing_fetch_event_gallery($event_id);
        $result = array();
        foreach ($media as $m) {
            $mid = '';
            $guid = '';
            if (isset($m->ID)) {
                $mid = $m->ID;
                $source = wp_get_attachment_image_src($mid, 'dwt_listing_user-dp');
                $path = $source[0];
            } else {
                $mid = $m;
                $source = wp_get_attachment_image_src($mid, 'dwt_listing_user-dp');
                $path = $source[0];
            }

            $obj = array();
            $obj['dispaly_name'] = basename(get_attached_file($mid));;
            $obj['name'] = $path;
            $obj['size'] = filesize(get_attached_file($mid));
            $obj['id'] = $mid;
            $result[] = $obj;
        }
        header('Content-type: text/json');
        header('Content-type: application/json');
        echo json_encode($result);
        die();
    }

}

// Delete Event Images ...
add_action('wp_ajax_delete_event_image', 'dwt_listing_delete_event_images');
if (!function_exists('dwt_listing_delete_event_images')) {

    function dwt_listing_delete_event_images()
    {
        if (dwt_listing_text('dwt_listing_disable_edit') == '1' && !is_super_admin(get_current_user_id())) {
            echo '0|' . __("Disable for Demo.", 'dwt-listing');
            die();
        }
        if (get_current_user_id() == "")
            die();

        if ($_POST['is_update'] != "") {
            $event_id = $_POST['is_update'];
        } else {
            $event_id = get_user_meta(get_current_user_id(), 'event_in_progress', true);
        }

        if (!is_super_admin(get_current_user_id()) && get_post_field('post_author', $event_id) != get_current_user_id())
            die();

        $attachmentid = $_POST['img'];
        wp_delete_attachment($attachmentid, true);

        if (get_post_meta($event_id, 'downotown_event_arrangement_', true) != "") {
            $ids = get_post_meta($event_id, 'downotown_event_arrangement_', true);
            $res = str_replace($attachmentid, "", $ids);
            $res = str_replace(',,', ",", $res);
            $img_ids = trim($res, ',');
            update_post_meta($event_id, 'downotown_event_arrangement_', $img_ids);
        }
        echo "1";
        die();
    }

}

// Get Event Media Images
if (!function_exists('dwt_listing_fetch_event_gallery')) {

    function dwt_listing_fetch_event_gallery($event_id)
    {
        global $dwt_listing_options;
        $re_order = get_post_meta($event_id, 'downotown_event_arrangement_', true);
        if ($re_order != "") {
            return explode(',', $re_order);
        } else {
            global $wpdb;
            $query = "SELECT ID FROM $wpdb->posts WHERE post_type = 'attachment' AND post_parent = '" . $event_id . "'";
            $results = $wpdb->get_results($query, OBJECT);
            return $results;
        }
    }

}

// Return Event Images media
if (!function_exists('dwt_listing_return_event_idz')) {

    function dwt_listing_return_event_idz($media, $thumbnail_size)
    {
        global $dwt_listing_options;
        if (count($media) > 0) {
            $i = 1;
            foreach ($media as $m) {
                if ($i > 1)
                    break;
                $mid = '';
                if (isset($m->ID)) {
                    $mid = $m->ID;
                } else {
                    $mid = $m;
                }
                if (wp_attachment_is_image($mid)) {
                    $image = wp_get_attachment_image_src($mid, $thumbnail_size);
                    return $image[0];
                } else {
                    return dwt_listing_defualt_img_url();
                }
            }
        } else {
            return $dwt_listing_options['dwt_listing_defualt_event_image']['url'];
        }
    }

}


// Permantely Delete Events
add_action('wp_ajax_remove_my_events', 'dowtown_delete_my_events');
if (!function_exists('dowtown_delete_my_events')) {

    function dowtown_delete_my_events()
    {
        //check user is logged in or not
        dwt_listing_authenticate_check();
        if (isset($_POST['event_id']) && $_POST['event_id'] != "") {
            $event_id = $_POST['event_id'];
            if (wp_trash_post($event_id)) {
                echo '1|' . esc_html__("Event removed successfully.", 'dwt-listing');
            } else {
                echo '0|' . esc_html__("There's some problem, please try again later.", 'dwt-listing');
            }
        }
        die();
    }

}


// Soft Expire Events
add_action('wp_ajax_expire_my_events', 'dwt_listing_expire_my_current_events');
if (!function_exists('dwt_listing_expire_my_current_events')) {

    function dwt_listing_expire_my_current_events()
    {
        //check user is logged in or not
        dwt_listing_authenticate_check();
        if (isset($_POST['event_id']) && $_POST['event_id'] != "") {
            $event_id = $_POST['event_id'];
            //zero means its expire
            $status = '0';
            update_post_meta($event_id, 'dwt_listing_event_status', $status);
            echo '1|' . esc_html__("Event Expired Successfully.", 'dwt-listing');
        }
        die();
    }

}


// Re Active My Current Listing
add_action('wp_ajax_reactive_my_events', 'dwt_listing_reactive_my_current_events');
if (!function_exists('dwt_listing_reactive_my_current_events')) {

    function dwt_listing_reactive_my_current_events()
    {
        //check user is logged in or not
        dwt_listing_authenticate_check();
        if (isset($_POST['event_id']) && $_POST['event_id'] != "") {
            $event_id = $_POST['event_id'];
            //one means its active
            $status = '1';
            update_post_meta($event_id, 'dwt_listing_event_status', $status);
            echo '1|' . esc_html__("Event activated successfully.", 'dwt-listing');
        }
        die();
    }

}


// User Total Events
if (!function_exists('dwt_listing_get_all_events_count')) {

    function dwt_listing_get_all_events_count($user_id)
    {
        global $wpdb;
        $listing_count = $wpdb->get_var("SELECT COUNT(*) AS total FROM  $wpdb->posts WHERE post_type = 'events' AND post_author = '$user_id'");

        return dwt_listing_number_format_short($listing_count);
    }

}

// User Expired Listings
if (!function_exists('dwt_listing_get_events_status_count')) {

    function dwt_listing_get_events_status_count($user_id, $status)
    {
        $count = 0;
        $args = array('post_type' => 'events', 'author' => $user_id, 'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => 'dwt_listing_event_status',
                    'value' => $status,
                    'compare' => '=',
                ),
            ),
        );
        $args = dwt_listing_wpml_show_all_posts_callback($args);
        $query = new WP_Query($args);
        if ($query->have_posts()) {
            return dwt_listing_number_format_short($query->post_count);
        } else {
            return dwt_listing_number_format_short($count);
        }
    }

}


//User Publish Profile & Listings
//Fetch all ads
if (!function_exists('dwt_listing_public_profile_listings')) {

    function dwt_listing_public_profile_listings($listing_status, $paged, $user_id)
    {
        $meta_query_args = array();
        $listing_status = 'publish';
        $meta_query_args = array(array('key' => 'dwt_listing_listing_status', 'value' => 1, 'compare' => '='));
        $args = array
        (
            'post_type' => 'listing',
            'author' => $user_id,
            'post_status' => $listing_status,
            'posts_per_page' => get_option('posts_per_page'),
            'paged' => $paged,
            'order' => 'DESC',
            'orderby' => 'date',
            'meta_query' => $meta_query_args
        );
        return $args;
    }

}


// Delete active user
add_action('wp_ajax_dwt_listing_delete_myaccount', 'wp_ajax_dwt_listing_delete_my_account');
if (!function_exists('wp_ajax_dwt_listing_delete_my_account')) {

    function wp_ajax_dwt_listing_delete_my_account()
    {
        //check user is logged in or not
        dwt_listing_authenticate_check();
        if (is_super_admin($user_id)) {
            echo '1';
            die();
        } else {
            $user_id = $_POST['user_id'];
            // delete comment with that user id
            $c_args = array('user_id' => $user_id, 'post_type' => 'any', 'status' => 'all');
            $comments = get_comments($c_args);
            if (count((array)$comments) > 0) {
                foreach ($comments as $comment) :
                    wp_delete_comment($comment->comment_ID, true);
                endforeach;
            }
            // delete user posts
            $args = array('numberposts' => -1, 'post_type' => 'any', 'author' => $user_id);
            $user_posts = get_posts($args);
            // delete all the user posts
            if (count((array)$user_posts) > 0) {
                foreach ($user_posts as $user_post) {
                    wp_delete_post($user_post->ID, true);
                }
            }
            //now delete actual user
            if (is_multisite()) {
                wpmu_delete_user($user_id);
            }
            wp_delete_user($user_id);
            echo get_home_url();
            die();
        }
    }

}


// Create New Event...
add_action('wp_ajax_my_new_menu', 'dwt_listing_my_new_menu_service');
if (!function_exists('dwt_listing_my_new_menu_service')) {

    function dwt_listing_my_new_menu_service()
    {
        global $dwt_listing_options;
        if (dwt_listing_text('dwt_listing_disable_edit') == '1' && !is_super_admin(get_current_user_id())) {
            return '';
            die();
        }

        if (get_current_user_id() == "") {
            echo "0";
            die();
        }

        // Getting values
        $params = array();
        parse_str($_POST['collect_data'], $params);
        $menu_title = ($params['l_menu_title']);
        $menu_price = ($params['l_menu_price']);
        $menu_desc = $params['l_menu_desc'];
        $menu_type = $params['l_menu_type'];
        $menu_arr = array();
        if (!empty($menu_title)) {
            //$menu_arr['l_menu_type'] =  $menu_type;
            for ($i = 0; $i < count($menu_title); $i++) {
                if (!empty($menu_title[$i])) {
                    $menu_arr[] = array(
                        'l_menu_title' => $menu_title[$i],
                        'l_menu_price' => $menu_price[$i],
                        'l_menu_desc' => $menu_desc[$i],
                    );
                }
            }
        }

        $menu_array = wp_json_encode($menu_arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $menu_parent_listing = sanitize_text_field($params['menu_parent_listing']);
        add_post_meta($menu_parent_listing, 'dwt_listing_menuget_', $menu_array);
        echo get_the_permalink($menu_parent_listing);
        die();
    }

}


// Create New Menu Type...
add_action('wp_ajax_dwt_create_menutype', 'dwt_listing_create_menutype');
if (!function_exists('dwt_listing_create_menutype')) {

    function dwt_listing_create_menutype()
    {
        global $dwt_listing_options;
        global $wpdb;
        if (dwt_listing_text('dwt_listing_disable_edit') == '1' && !is_super_admin(get_current_user_id())) {
            return '';
            die();
        }

        if (get_current_user_id() == "") {
            echo "0";
            die();
        }

        if (isset($_POST['listing_id']) && $_POST['listing_id'] != "") {
            $return_html = '';
            $listing_id = $_POST['listing_id'];
            // Getting values
            $params = array();
            parse_str($_POST['collect_data'], $params);
            $menu_type = $params['l_menu_type'];
            if (isset($menu_type) && $menu_type != "") {
                add_post_meta($listing_id, 'dwt_listing_menutype_' . time(), $menu_type);
            }
            // query to get records and send a variable
            $get_results = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE post_id = '" . $listing_id . "' AND meta_key LIKE 'dwt_listing_menutype_%' ORDER BY meta_id ASC");
            $count = 0;
            if (!empty($get_results) && count($get_results) > 0) {
                $return_html .= '<div class="table-responsive table_formenu"><table class="table table-hover text-nowrap "><thead><tr><th>' . esc_html__('Menu Type', 'dwt-listing') . '</th><th>' . esc_html__('Action', 'dwt-listing') . '</th><th>' . esc_html__('Add Menu Items', 'dwt-listing') . '</th></tr></thead><tbody>';


                foreach ($get_results as $results) {
                    $return_html .= '<tr id="' . $count . '">
						<td>
							<span class="menu_name">' . $results->meta_value . '</span>
						</td>
						 <td>  
						   <button type="button" class="btn btn-primary btn-sm edit-button-' . esc_attr($results->meta_key) . ' edit-lmenu" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i>" data-key="' . $results->meta_key . '" data-id="' . $results->post_id . '"   title="' . esc_html__('Edit', 'dwt-listing') . '"><i class="fa fa-edit"></i></button>                                          
							<button type="button" class="btn btn-danger btn-sm delete-lmenu delete-button-' . esc_attr($results->meta_key) . '"  data-key="' . $results->meta_key . '" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i>" data-id="' . $results->post_id . '"  title="' . esc_html__('Delete', 'dwt-listing') . '"><i class="fa fa-trash-o"></i></button>
						</td>
						<td>
						   <a href="javascript:void(0)" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i>" data-key="' . $results->meta_key . '" data-id="' . $results->post_id . '" class="btn btn-success btn-sm view-button-' . esc_attr($results->meta_key) . ' l_view_collection">' . esc_html__('View Items', 'dwt-listing') . '</a>
							<button type="button" data-key-id="' . $results->post_id . '" data-key-ref="' . $results->meta_key . '" class="btn btn-warning btn-sm menu_items_addition" title="' . esc_html__('Edit', 'dwt-listing') . '"><i class="fa fa-plus"></i></button>
						</td>                                   
					</tr>';
                    $count++;
                }
                $return_html .= '</tbody></table></div>';
                echo $return_html;
                die();
            }
        }
        die();
    }

}

// Edit Menu Types...
add_action('wp_ajax_dwt_edit_menutype', 'dwt_listing_edit_menutype');
if (!function_exists('dwt_listing_edit_menutype')) {

    function dwt_listing_edit_menutype()
    {
        global $dwt_listing_options;
        global $wpdb;
        if (dwt_listing_text('dwt_listing_disable_edit') == '1' && !is_super_admin(get_current_user_id())) {
            return '';
            die();
        }

        if (get_current_user_id() == "") {
            echo "0";
            die();
        }

        if (!empty($_POST['listing_id']) && !empty($_POST['key'])) {
            $listing_id = $_POST['listing_id'];
            $meta_key = $_POST['key'];
            $trid = $_POST['trid'];
            if (get_post_meta($listing_id, $meta_key, true) != "") {
                $result = get_post_meta($listing_id, $meta_key, true);
                echo '<div class="modal fade menu_modalz1 custom_modals" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="ModalLabel">' . esc_html__('Update Menu Type :', 'dwt-listing') . ' ' . $result . '</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
						<form id="dwt_update_menu" data-toggle="validator">
                        <div class="modal-body">
                          <div class="form-group">
                                    <label class="control-label">' . esc_html__('Menu Type', 'dwt-listing') . '<span>*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="ti-pencil"></i></span>
                                         <input type="text" class="form-control" name="l_menu_type" placeholder="' . esc_html__('Classic Burger', 'dwt-listing') . '" value="' . $result . '" required>
                                    </div>
                                </div>
                             <input type="hidden" name="parent_listing" value="' . $listing_id . '">
							 <input type="hidden" name="update_key" value="' . $meta_key . '">
							 <input type="hidden" name="trid" value="' . $trid . '">
                        </div>
                        <div class="modal-footer">
                       <button type="submit" class="btn btn-theme sonu-button update_menu"  data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> ' . esc_html__("Processing...", 'dwt-listing') . '">' . esc_html__("Update", 'dwt-listing') . '</button>
                              <button type="button" class="btn btn-light " data-dismiss="modal">' . esc_html__("Close", 'dwt-listing') . '</button>
                        </div>
						</form>
                      </div>
                    </div>
                  </div>';
            }
        }
        die();
    }

}

// Delete Menu Types...
add_action('wp_ajax_dwt_delete_menutype', 'dwt_listing_delete_menutype');
if (!function_exists('dwt_listing_delete_menutype')) {

    function dwt_listing_delete_menutype()
    {
        global $dwt_listing_options;
        global $wpdb;
        if (dwt_listing_text('dwt_listing_disable_edit') == '1' && !is_super_admin(get_current_user_id())) {
            return '';
            die();
        }

        if (get_current_user_id() == "") {
            echo "0";
            die();
        }

        if (!empty($_POST['listing_id']) && !empty($_POST['key'])) {
            $listing_id = $_POST['listing_id'];
            $meta_key = $_POST['key'];
           
            if (get_post_meta($listing_id, $meta_key, true) != "") {
                delete_post_meta($listing_id, $meta_key);
                //now delete relevent data
                $wpdb->query("DELETE FROM $wpdb->postmeta WHERE post_id = '" . $listing_id . "' AND meta_key LIKE 'menu_itemz_" . $meta_key . "%'");
            }
        }
        die();
    }

}


// Create New Menu Type...
add_action('wp_ajax_dwt_update_menutype', 'dwt_listing_update_menutype');
if (!function_exists('dwt_listing_update_menutype')) {

    function dwt_listing_update_menutype()
    {
        global $dwt_listing_options;
        global $wpdb;
        if (dwt_listing_text('dwt_listing_disable_edit') == '1' && !is_super_admin(get_current_user_id())) {
            return '';
            die();
        }

        if (get_current_user_id() == "") {
            echo "0";
            die();
        }

        // Getting values
        $params = array();
        parse_str($_POST['collect_data'], $params);
        $listing_id = $params['parent_listing'];
        $new_name = $params['l_menu_type'];
        $meta_key = $params['update_key'];
        $trid = $params['trid'];
        if (!empty($listing_id)) {
            if (get_post_meta($listing_id, $meta_key, true) != "") {
                update_post_meta($listing_id, $meta_key, $new_name);
                echo $new_name . '|' . $trid;
            }
        }
        die();
    }

}


// Create New Menu Type...
add_action('wp_ajax_dwt_ad_new_menu_listz', 'dwt_listing_create_new_menu_listz');
if (!function_exists('dwt_listing_create_new_menu_listz')) {

    function dwt_listing_create_new_menu_listz()
    {
        global $dwt_listing_options;
        global $wpdb;
        if (dwt_listing_text('dwt_listing_disable_edit') == '1' && !is_super_admin(get_current_user_id())) {
            return '';
            die();
        }
        if (get_current_user_id() == "") {
            echo "0";
            die();
        }

        $params = array();
        parse_str($_POST['collect_data'], $params);
        $reference_listing = $params['reference_listing'];
        $reference_key = $params['reference_key'];
        $menu_title = $params['dwt_l_menu_title'];
        $menu_price = $params['dwt_l_menu_price'];
        $menu_desc = $params['dwt_l_menu_desc'];
        $menu_arr = array();
        if (!empty($reference_listing) && !empty($reference_key)) {
            $menu_arr[] = array(
                'l_menu_title' => ($menu_title),
                'l_menu_price' => ($menu_price),
                'l_menu_desc' => ($menu_desc),
            );
            $menu_array = wp_json_encode($menu_arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            add_post_meta($reference_listing, 'menu_itemz_' . $reference_key . '_' . time(), $menu_array);
            $return_html = '';
            $get_results = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE post_id = '" . $reference_listing . "' AND meta_key LIKE 'menu_itemz_" . $reference_key . "%' ORDER BY meta_id ASC");
            if (!empty($get_results) && count($get_results) > 0) {
                $return_html .= ' <div class="modal menu_item_historyz custom_modals" tabindex="-1" role="dialog" aria-labelledby="ModalLabel_items_view" aria-hidden="true"><div class="modal-dialog modal-lg" role="document"> <div class="modal-content"><div class="modal-body no-padding"><div class="table-responsive table_formenu"><table class="table table-hover text-nowrap"><thead>
							<tr>
								<th>' . esc_html__('Menu Title', 'dwt-listing') . '</th>                                    
								<th>' . esc_html__('Menu price', 'dwt-listing') . '</th>
								<th>' . esc_html__('Actions', 'dwt-listing') . '</th>                                 
							</tr>
						</thead>
						<tbody>';
                $count = 0;
                foreach ($get_results as $results) {
                    $menu_inner_items = json_decode(stripslashes($results->meta_value));
                    if (!empty($menu_inner_items) && count($menu_inner_items) > 0) {
                        foreach ($menu_inner_items as $men) {
                            $return_html .= '<tr id="menu_' . $count . '">
								<td><span class="menu_name">' . $men->l_menu_title . '</span></td>
								 <td>  
								  <span class="menu_price">' . $men->l_menu_price . '</span>
								</td>
								<td>
								 <button type="button" class="btn btn-primary btn-sm inner-menu-edit lmenu-edit-' . esc_attr($results->meta_key) . '"  data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i>" data-listing_id="' . $results->post_id . '" data-refer_key="' . $results->meta_key . '" title="' . esc_html__('Edit', 'dwt-listing') . '"><i class="fa fa-edit"></i></button>                                          
								<button type="button" class="btn btn-danger btn-sm delete-inner-menu delete-buttonz-' . esc_attr($results->meta_key) . '"  data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i>" data-listing_id="' . $results->post_id . '" data-refer_key="' . $results->meta_key . '" title="' . esc_html__('Delete', 'dwt-listing') . '"><i class="fa fa-trash-o"></i></button>
								</td>                                   
							</tr>';
                        }
                    }
                    $count++;
                }
                $return_html .= '</tbody></table></div></div><div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">' . esc_html__('Close', 'dwt-listing') . '</button></div></div></div></div>';
                echo $return_html;
            }
        }
        die();
    }

}


// Delete Menu Types...
add_action('wp_ajax_dwt_listing_delete_inner_menutype', 'dwt_listing_delete_inner_menutype');
if (!function_exists('dwt_listing_delete_inner_menutype')) {

    function dwt_listing_delete_inner_menutype()
    {
        global $dwt_listing_options;
        if (dwt_listing_text('dwt_listing_disable_edit') == '1' && !is_super_admin(get_current_user_id())) {
            return '';
            die();
        }

        if (get_current_user_id() == "") {
            echo "0";
            die();
        }

        if (!empty($_POST['listing_id']) && !empty($_POST['key'])) {
            $listing_id = $_POST['listing_id'];
            $meta_key = $_POST['key'];
            if (get_post_meta($listing_id, $meta_key, true) != "") {
                delete_post_meta($listing_id, $meta_key);
            }
        }
        die();
    }

}


// Edit Inner Menu Group...
add_action('wp_ajax_dwt_edit_inner_menugroup', 'dwt_edit_inner_menugroup_update');
if (!function_exists('dwt_edit_inner_menugroup_update')) {

    function dwt_edit_inner_menugroup_update()
    {
        global $dwt_listing_options;
        global $wpdb;
        if (dwt_listing_text('dwt_listing_disable_edit') == '1' && !is_super_admin(get_current_user_id())) {
            return '';
            die();
        }

        if (get_current_user_id() == "") {
            echo "0";
            die();
        }

        if (!empty($_POST['listing_id']) && !empty($_POST['key'])) {
            $listing_id = $_POST['listing_id'];
            $meta_key = $_POST['key'];
            $trid = $_POST['trid'];
            $form_valus = '';
            if (!empty($listing_id) && !empty($meta_key)) {
                if (get_post_meta($listing_id, $meta_key, true) != "") {
                    $result = get_post_meta($listing_id, $meta_key, true);
                    $menu_inner_items = json_decode($result);
                    if (!empty($menu_inner_items) && is_array($menu_inner_items) && count($menu_inner_items) > 0) {
                        foreach ($menu_inner_items as $men) {
                            $form_valus .= ' <div class="form-group">
										<label class="control-label">' . esc_html__('Menu Title', 'dwt-listing') . '<span>*</span></label>
										<div class="input-group">
											<span class="input-group-addon"><i class="ti-pencil"></i></span>
											 <input type="text" class="form-control" name="dwt_l_menu_title" placeholder="' . esc_html__('Classic Burger', 'dwt-listing') . '" value="' . $men->l_menu_title . '" required>
										</div>
								</div>
											 <div class="form-group">
										 <label class="control-label">' . esc_html__('Price', 'dwt-listing') . '<span>*</span></label>
										<div class="input-group">
											<span class="input-group-addon"><i class="ti-money"></i></span>
											 <input type="text" class="form-control" name="dwt_l_menu_price" placeholder="' . esc_html__('$20', 'dwt-listing') . '" value="' . $men->l_menu_price . '" required>
										</div>
								</div>
								<div class="form-group">
									<label class="control-label">' . esc_html__('Description', 'dwt-listing') . '<span>*</span></label>
									<textarea class="form-control"  name="dwt_l_menu_desc" placeholder="' . esc_html__('Mexican style, chicken fajita, green pepper & onions', 'dwt-listing') . '" required>' . esc_textarea($men->l_menu_desc) . '</textarea>
								</div>';
                        }
                    }
                    echo '<div class="modal fetch_inner_form custom_modals" tabindex="-1" role="dialog" aria-labelledby="ModalLabel_items" aria-hidden="true"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="ModalLabel_items">' . esc_html__('Update Menu Item ', 'dwt-listing') . '</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
					      <form id="update_inner_itemz_menu" data-toggle="validator">
							<div class="modal-body">
							   ' . $form_valus . '
								<input type="hidden" name="trid" value="' . $trid . '">
								<input type="hidden" id="reference_key" name="reference_key" value="' . esc_attr($meta_key) . '">
								<input type="hidden" id="reference_listing" name="reference_listing" value="' . esc_attr($listing_id) . '">
							</div>
							<div class="modal-footer">
							  <button type="submit" class="btn btn-theme sonu-button l_update_inner_itemzz" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> ' . esc_html__("Processing...", 'dwt-listing') . '">' . esc_html__('Save', 'dwt-listing') . '</button>
							  <button type="button" class="btn btn-light" data-dismiss="modal">' . esc_html__('Close', 'dwt-listing') . '</button>
							</div>
							</form>
						  </div>
						</div>
					  </div>';
                }
            }
        }
        die();
    }

}


// Update New Menu Type...
add_action('wp_ajax_dwt_update_current_menu', 'dwt_listing_update_current_menu');
if (!function_exists('dwt_listing_update_current_menu')) {

    function dwt_listing_update_current_menu()
    {
        global $dwt_listing_options;
        if (dwt_listing_text('dwt_listing_disable_edit') == '1' && !is_super_admin(get_current_user_id())) {
            return '';
            die();
        }
        if (get_current_user_id() == "") {
            echo "0";
            die();
        }

        $params = array();
        parse_str($_POST['collect_data'], $params);

        $reference_listing = $params['reference_listing'];
        $reference_key = $params['reference_key'];
        $menu_title = $params['dwt_l_menu_title'];
        $menu_price = $params['dwt_l_menu_price'];
        $menu_desc = $params['dwt_l_menu_desc'];
        $trid = $params['trid'];
        $menu_arr = array();
        if (!empty($reference_listing) && !empty($reference_key)) {
            if (get_post_meta($reference_listing, $reference_key, true) != "") {
                $menu_arr[] = array(
                    'l_menu_title' => ($menu_title),
                    'l_menu_price' => ($menu_price),
                    'l_menu_desc' => ($menu_desc),
                );
                $menu_array = wp_json_encode($menu_arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                update_post_meta($reference_listing, $reference_key, $menu_array);
                echo $menu_title . '|' . $trid . '|' . $menu_price;
            }
        }
        die();
    }

}


// Fetch Inner Menus...
add_action('wp_ajax_dwt_fetch_inner_menugroupz', 'dwt_listing_fetch_innermenu_listz');
if (!function_exists('dwt_listing_fetch_innermenu_listz')) {

    function dwt_listing_fetch_innermenu_listz()
    {
        global $dwt_listing_options;
        global $wpdb;
        if (dwt_listing_text('dwt_listing_disable_edit') == '1' && !is_super_admin(get_current_user_id())) {
            return '';
            die();
        }
        if (get_current_user_id() == "") {
            echo "0";
            die();
        }

        if (!empty($_POST['listing_id'])) {
            $reference_listing = $_POST['listing_id'];
        }
        if (!empty($_POST['key'])) {
            $reference_key = $_POST['key'];
        }
        if (!empty($reference_listing) && !empty($reference_key)) {
            $return_html = '';
            $get_results = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE post_id = '" . $reference_listing . "' AND meta_key LIKE 'menu_itemz_" . $reference_key . "%' ORDER BY meta_id ASC");
            if (!empty($get_results) && count($get_results) > 0) {
                $return_html .= ' <div class="modal menu_item_historyz custom_modals" tabindex="-1" role="dialog" aria-labelledby="ModalLabel_items_view" aria-hidden="true"><div class="modal-dialog modal-lg" role="document"> <div class="modal-content"><div class="modal-body no-padding"><div class="table-responsive table_formenu"><table class="table table-hover text-nowrap"><thead>
							<tr>
								<th>' . esc_html__('Menu Title', 'dwt-listing') . '</th>                                    
								<th>' . esc_html__('Menu price', 'dwt-listing') . '</th>
								<th>' . esc_html__('Actions', 'dwt-listing') . '</th>                                 
							</tr>
						</thead>
						<tbody>';
                $count = 0;
                foreach ($get_results as $results) {
                    $menu_inner_items = json_decode(stripslashes($results->meta_value));
                    if (!empty($menu_inner_items) && count($menu_inner_items) > 0) {
                        foreach ($menu_inner_items as $men) {
                            $return_html .= '<tr id="menu_' . $count . '">
								<td><span class="menu_name">' . $men->l_menu_title . '</span></td>
								 <td>  
								  <span class="menu_price">' . $men->l_menu_price . '</span>
								</td>
								<td>
								 <button type="button" class="btn btn-primary btn-sm inner-menu-edit lmenu-edit-' . esc_attr($results->meta_key) . '"  data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i>" data-listing_id="' . $results->post_id . '" data-refer_key="' . $results->meta_key . '" title="' . esc_html__('Edit', 'dwt-listing') . '"><i class="fa fa-edit"></i></button>                                          
								<button type="button" class="btn btn-danger btn-sm delete-inner-menu delete-buttonz-' . esc_attr($results->meta_key) . '"  data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i>" data-listing_id="' . $results->post_id . '" data-refer_key="' . $results->meta_key . '" title="' . esc_html__('Delete', 'dwt-listing') . '"><i class="fa fa-trash-o"></i></button>
								</td>                                   
							</tr>';
                        }
                    }
                    $count++;
                }
                $return_html .= '</tbody></table></div></div><div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">' . esc_html__('Close', 'dwt-listing') . '</button></div></div></div></div>';
                echo $return_html;
            } else {
                echo '<div class="modal menu_item_historyz custom_modals" tabindex="-1" role="dialog" aria-labelledby="ModalLabel_items_view" aria-hidden="true"><div class="modal-dialog modal-lg" role="document"> <div class="modal-content"><div class="modal-body no-padding"><div class="table-responsive table_formenu"><table class="table table-hover text-nowrap"><thead>
							<tr>
								<th>' . esc_html__('Menu Title', 'dwt-listing') . '</th>                                    
								<th>' . esc_html__('Menu price', 'dwt-listing') . '</th>
								<th>' . esc_html__('Actions', 'dwt-listing') . '</th>                                 
							</tr>
						</thead>
						<tbody>
						<tr><td colspan="3"><div  class="alert custom-alert custom-alert--warning" role="alert">
          			<div class="custom-alert__top-side"><span class="alert-icon custom-alert__icon  ti-info-alt  "></span><div class="custom-alert__body"><h6 class="custom-alert__heading">' . esc_html__('No Result Found!', 'dwt-listing') . '</h6><div class="custom-alert__content"> ' . esc_html__("We couldn't find any results for this action.", 'dwt-listing') . '</div></div></div>
        		</div></td></tr>
						</tbody></table></div></div><div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">' . esc_html__('Close', 'dwt-listing') . '</button></div></div></div></div>';
            }
        }
        die();
    }

}


// Fetch Against Listing When Get Method...
add_action('wp_ajax_dwt_fetchmenu_against_listing', 'dwt_listing_fetchmenu_against_listing');
if (!function_exists('dwt_listing_fetchmenu_against_listing')) {

    function dwt_listing_fetchmenu_against_listing()
    {
        global $dwt_listing_options;
        global $wpdb;
        if (dwt_listing_text('dwt_listing_disable_edit') == '1' && !is_super_admin(get_current_user_id())) {
            return '';
            die();
        }

        if (get_current_user_id() == "") {
            echo "0";
            die();
        }

        if (isset($_POST['listing_id']) && $_POST['listing_id'] != "") {
            $return_html = '';
            $listing_id = $_POST['listing_id'];
            // query to get records and send a variable
            $get_results = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE post_id = '" . $listing_id . "' AND meta_key LIKE 'dwt_listing_menutype_%' ORDER BY meta_id ASC");
            $count = 0;
            if (!empty($get_results) && count($get_results) > 0) {
                $return_html .= '<div class="table-responsive table_formenu"><table class="table table-hover text-nowrap "><thead><tr><th>' . esc_html__('Menu Type', 'dwt-listing') . '</th><th>' . esc_html__('Action', 'dwt-listing') . '</th><th>' . esc_html__('Add Menu Items', 'dwt-listing') . '</th></tr></thead><tbody>';
                foreach ($get_results as $results) {
                    $return_html .= '<tr id="' . $count . '">
						<td>
							<span class="menu_name">' . $results->meta_value . '</span>
						</td>
						 <td>  
						   <button type="button" class="btn btn-primary btn-sm edit-button-' . esc_attr($results->meta_key) . ' edit-lmenu" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i>" data-key="' . $results->meta_key . '" data-id="' . $results->post_id . '"   title="' . esc_html__('Edit', 'dwt-listing') . '"><i class="fa fa-edit"></i></button>                                          
							<button type="button" class="btn btn-danger btn-sm delete-lmenu delete-button-' . esc_attr($results->meta_key) . '"  data-key="' . $results->meta_key . '" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i>" data-id="' . $results->post_id . '"  title="' . esc_html__('Delete', 'dwt-listing') . '"><i class="fa fa-trash-o"></i></button>
						</td>
						<td>
						   <a href="javascript:void(0)" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i>" data-key="' . $results->meta_key . '" data-id="' . $results->post_id . '" class="btn btn-success btn-sm view-button-' . esc_attr($results->meta_key) . ' l_view_collection">' . esc_html__('View Items', 'dwt-listing') . '</a>
							<button type="button" data-key-id="' . $results->post_id . '" data-key-ref="' . $results->meta_key . '" class="btn btn-warning btn-sm menu_items_addition" title="' . esc_html__('Edit', 'dwt-listing') . '"><i class="fa fa-plus"></i></button>
						</td>                                   
					</tr>';
                    $count++;
                }
                $return_html .= '</tbody></table></div>';
                echo $return_html;
                die();
            } else {
                echo '<div class="alert custom-alert custom-alert--warning" role="alert">
          			<div class="custom-alert__top-side"><span class="alert-icon custom-alert__icon  ti-info-alt  "></span><div class="custom-alert__body"><h6 class="custom-alert__heading">' . esc_html__('No Result Found!', 'dwt-listing') . '</h6><div class="custom-alert__content"> ' . esc_html__("We couldn't find any results for this action.", 'dwt-listing') . '</div></div></div>
        		</div>';
            }
        }
        die();
    }

}

/* DWT Listing Ajax Based Events Search */
add_action('wp_ajax_dwt_ajax_search_events', 'dwt_listing_ajax_search_events');
add_action('wp_ajax_nopriv_dwt_ajax_search_events', 'dwt_listing_ajax_search_events');
if (!function_exists('dwt_listing_ajax_search_events')) {

    function dwt_listing_ajax_search_events()
    {
        $params = array();
        $lat_lng_meta_query = array();
        parse_str($_POST['collect_data'], $params);

        /* Listing Title */
        $event_title = '';
        if (isset($params['by_title']) && $params['by_title'] != "") {
            $event_title = $params['by_title'];
        }

        /* Categories */
        $category = '';
        if (isset($params['event_cat']) && $params['event_cat'] != "") {
            $category = array(
                array(
                    'taxonomy' => 'l_event_cat',
                    'field' => 'term_id',
                    'terms' => dwt_listing_show_taxonomy_all($params['event_cat'], 'l_event_cat'),
                ),
            );
        }
        /* Listing Street Address */
        $street_address = '';
        if (isset($params['by_location']) && $params['by_location'] != "") {
            $street_address = array(
                'key' => 'dwt_listing_event_venue',
                'value' => trim($params['by_location']),
                'compare' => 'LIKE',
            );
        }

        /* Get start Event Date */
        $event_start_date = '';
        if (isset($params['by_date_start_filter']) && $params['by_date_start_filter'] != "") {
            $event_start_date = ($params['by_date_start_filter']);
        }
        /* Get End Event Date */
        $event_end_date = '';
        if (isset($params['by_date_end_filter']) && $params['by_date_end_filter'] != "") {
            $event_end_date = ($params['by_date_end_filter']);
        }

        $event_date_query = '';
        if ($event_start_date != '' && $event_end_date != '') {
            $event_date_query = array(
                'relation' => 'AND',
                array(
                    'key' => 'dwt_listing_event_start_date',
                    'value' => $event_start_date,
                    'compare' => '>=',
                ),
                array(
                    'key' => 'dwt_listing_event_start_date',
                    'value' => $event_end_date,
                    'compare' => '<=',
                ),
            );
        }

        /* only active events */
        $active_events = array(
            'key' => 'dwt_listing_event_status',
            'value' => '1',
            'compare' => '='
        );
        $order = 'DESC';
        $order_by = 'date';
        if (isset($params['sort_by']) && $params['sort_by'] != "") {
            $orde_arr = explode('-', $params['sort_by']);
            $order = isset($orde_arr[1]) ? $orde_arr[1] : 'desc';
            {
                $orderBy = isset($orde_arr[0]) ? $orde_arr[0] : 'ID';
            }
        }

        $page_no = '';
        if (isset($_POST['page_no'])) {
            $page_no = $_POST['page_no'];
        } else {
            $page_no = 1;
        }

        /* Query */
        $args = array
        (
            's' => $event_title,
            'post_type' => 'events',
            'post_status' => 'publish',
            'tax_query' => array(
                $category,
            ),
            'meta_query' => array(
                $active_events,
                $street_address,
                $event_date_query
            ),
            'order' => $order,
            'orderby' => $order_by,
            'paged' => $page_no,
        );

        $args = dwt_listing_wpml_show_all_posts_callback($args);
        $results = new WP_Query($args);

        if ($results->have_posts()) {
            if (dwt_listing_text("dwt_listing_event_layout") == "map") {
                if (isset($params['layout_type']) && $params['layout_type'] == "list") {
                    require trailingslashit(get_template_directory()) . 'template-parts/events/event-type-list.php';
                } else {
                    require trailingslashit(get_template_directory()) . "template-parts/events/event-with-ajax.php";
                }
                echo $results->found_posts . '|' . $fetch_output . '|' . '<a class="main-listing__clear" href="javascript:void(0)" id="reset_ajax_reslut">' . esc_html__('Reset All Filters', 'dwt-listing') . '</a>' . '|' . dwt_listing_ajax_pagination_search($results, $page_no);
            } else {
                if (isset($params['layout_type']) && $params['layout_type'] == "list") {
                    require trailingslashit(get_template_directory()) . 'template-parts/events/event-type-list.php';
                } else {
                    require trailingslashit(get_template_directory()) . "template-parts/events/event-type-grid.php";
                }
                echo $results->found_posts . '|' . $fetch_output . '|' . '<a class="main-listing__clear" href="javascript:void(0)" id="reset_ajax_reslut">' . esc_html__('Reset All Filters', 'dwt-listing') . '</a>' . '|' . dwt_listing_ajax_pagination_search($results, $page_no);
            }
        } else {
            echo '0|' . dwt_listing_ajax_no_result() . '|' . '<a class="main-listing__clear" href="javascript:void(0)" id="reset_ajax_reslut">' . esc_html__('Reset All Filters', 'dwt-listing') . '</a>';
            echo '<script>var event_markers_ajax = [];</script>';
        }
        die();
    }

}

/* Save Profile Settings... */
add_action('wp_ajax_save_profile_settings', 'dwt_listing_save_profile_settings');
if (!function_exists('dwt_listing_save_profile_settings')) {

    function dwt_listing_save_profile_settings()
    {
        if (dwt_listing_text('dwt_listing_disable_edit') == '1' && !is_super_admin(get_current_user_id())) {
            echo __("Disable for Demo.", 'dwt-listing');
            die();
        }
        // Getting values
        $params = array();
        parse_str($_POST['collect_data'], $params);
        $logo_type = sanitize_text_field($params['my_logo_type']);
        $hours_type = sanitize_text_field($params['my_hours_type']);
        $user_id = sanitize_text_field($params['current_user_id']);
        if ($logo_type == '2') {
            update_user_meta($user_id, 'dwt_listing_user_logo_type', '2');
        } else {
            update_user_meta($user_id, 'dwt_listing_user_logo_type', '1');
        }
        if ($hours_type == '24') {
            update_user_meta($user_id, 'dwt_listing_user_hours_type', '24');
        } else {
            update_user_meta($user_id, 'dwt_listing_user_hours_type', '12');
        }
        echo __("Settings saved successfully.", 'dwt-listing');
        die();
    }

}


/* adding for my booking reservation  */


add_action('wp_ajax_sb_get_calender_time', 'dwt_listing_sb_get_calender_time');
if (!function_exists('dwt_listing_sb_get_calender_time')) {

    function dwt_listing_sb_get_calender_time()
    {     
        $date  =   isset($_POST['date'])  ? $_POST['date']  : ""; 
        // print_r($date);
        $listing_id   =   isset($_POST['ad_id'])  ? $_POST['ad_id']  : "";
        if($date != ""){
            $week_days  =  lcfirst(date('l',strtotime($date)));
           
        }

    $startTime = date('g:i a', strtotime(get_post_meta($listing_id, '_timingz_' . $week_days . '_from', true)));
    $endTime = date('g:i a', strtotime(get_post_meta($listing_id, '_timingz_' . $week_days . '_to', true)));
       
        $startTime =strtotime($startTime);
         $endTime = strtotime($endTime);

                                $interval = '30 mins';
                                $current = time();
                                $addTime = strtotime('+' . $interval, $current);
                                $diff = $addTime - $current;
                                $intervalEnd = $startTime + $diff;
                                  $count = 1;
                                  $html   =  '';
                            while ( $startTime <  $endTime) {

                                $startTime += $diff;
                                $intervalEnd += $diff;
                                $count++;

                                 $str  =  date('g:i a',$startTime);
                                 $end =  date('g:i a',$intervalEnd);

                                $html  .=   '<option value= "'.$str.'  -  '.$end.'"> '.$str.'  -  '.$end.'  </option>';
                            }


                            if (get_post_meta($listing_id, 'dwt_listing_business_hours', true) == '1') {

                                $startingTime=mktime(00, 00);
                                $start_time =  date('g:i a', $startingTime);
                                $StartsTime = strtotime($start_time);


                                $endingTime=mktime(23, 59);
                                $end_time =  date('g:i a', $endingTime);
                                $endsTime = strtotime($end_time);


                                $interval = '30 mins';
                                $current = time();
                                $addTime = strtotime('+' . $interval, $current);
                                $diff = $addTime - $current;
                                $intervalEnd = $StartsTime + $diff;
                                $count = 1;
                                $html   =  '';

                                while ( $StartsTime <  $endsTime) {

                                    $StartsTime += $diff;
                                    $intervalEnd += $diff;
                                    $count++;

                                    $strs  =  date('g:i a',$StartsTime);
                                   //    print_r(123 . "</br>" . $strs );
                                    $ends =  date('g:i a',$intervalEnd);

                                    $html  .=   '<option value= "'.$strs.'  -  '.$ends.'"> '.$strs.'  -  '.$ends.'  </option>' ;
                                }


                            }



            wp_send_json_success( array(
                    'options' =>  $html,   'message' => 'hi dear user',
                  
                ), 200 );    
    }
}

//adding action for add_reservation_form of adding forms booking days

/* Create New reservation... */
add_action('wp_ajax_my_new_reservation', 'dwt_listing_my_new_reservation');
if (!function_exists('dwt_listing_my_new_reservation')) {

    function dwt_listing_my_new_reservation()
    {
        global $dwt_listing_options;
       if (get_current_user_id() == "") {
            echo "0";
            die();
        }
        
        // Getting values
        $params = array();
        parse_str($_POST['collect_data'], $params);
        $reserver_ID = sanitize_text_field($params['reserverID']);
        $reserver_listing_title = sanitize_text_field($params['listing_title']);
        $listing_title = get_the_title( $reserver_ID );
        $reserver_name =    sanitize_text_field($params['reserver_name']);
        $reserver_number = sanitize_text_field($params['reserver_number']);
        $reserver_email = sanitize_text_field($params['reserver_email']);
        $reservervation_day = sanitize_text_field($params['booking-date']); 
        $reservation_time = sanitize_text_field( $params['booking-slot'] );

        
        $my_reservation_post = array(
  
            'post_title' => $reserver_name     . '(' . $listing_title . ')',
            'post_status' => 'publish',
            'post_author' => get_current_user_id(),
            'post_type' => 'reservation',

        );
      
        
               // Insert the post into the database.
        $booking_id = wp_insert_post($my_reservation_post);

        $author_id = get_post_field ('post_author', $reserver_ID);
       
        if(!is_wp_error( $booking_id )){
            update_post_meta($booking_id, 'dwt_listing_listing_status', '1');
            update_post_meta($booking_id, 'dwt_listing_reserver_number', $reserver_number);
            update_post_meta($booking_id, 'dwt_listing_reserver_email', $reserver_email);
            update_post_meta($booking_id, 'dwt_listing_reserver_name', $reserver_name);
            update_post_meta($booking_id, 'dwt_listing_reservation_day', $reservervation_day);
            update_post_meta($booking_id, 'dwt_listing_reservation_time', $reservation_time);
            update_post_meta($booking_id, 'dwt_listing_reservation_id', $reserver_ID);
            update_post_meta($booking_id, 'dwt_listing_author', $author_id);
            update_post_meta($booking_id, 'dwt_listing_reservation_title', $reserver_listing_title);


         wp_send_json_success();
         die();
        }
        else {
            wp_send_json_error();
        }
    }   
}

//adding action for enable reservation

add_action('wp_ajax_my_reservations', 'dwt_listing_my_reservations_service');
if (!function_exists('dwt_listing_my_reservations_service')) {
    function dwt_listing_my_reservations_service()
    {
        $params = array();  
        parse_str($_POST['collect_data'], $params);
        $restricted_days = $_POST['restricted_days'];   
        $listing_id  =   $_POST['listing_id'];
        if(isset( $params['menu_parent_listing']) &&  $params['menu_parent_listing']!= ""){
           update_post_meta( $params['menu_parent_listing'], 'listings_reservations', '1' );
            update_post_meta( $listing_id, 'dwt_listing_reservation_day', '1' );
            update_post_meta( $listing_id, 'restricted_days',  $restricted_days);              
            echo esc_html__( 'Booking enabled succesfully','dwt-listing' );
        }
    }
}



add_action('wp_ajax_listing_reservations_remove', 'dwt_listing_reservations_remove');
if (!function_exists('dwt_listing_reservations_remove')) {
    function dwt_listing_reservations_remove()
    {
       
        $listing_id  =   isset($_POST['listing_id'])  ? $_POST['listing_id']  : "";        
        if($listing_id  != ""){
            update_post_meta( $listing_id , 'listings_reservations', '0' );
            echo esc_html__( 'Booking removed succesfully','dwt-listing' );
        }
    }
}

/* == remove reservation booking listing from dashboard  == */

add_action('wp_ajax_remove_my_reservation_listing', 'dowtown_delete_my_booked_listing');
if (!function_exists('dowtown_delete_my_booked_listing')) {

    function dowtown_delete_my_booked_listing()
    {
        //check user is logged in or not
        dwt_listing_authenticate_check();
        if (isset($_POST['listing_id']) && $_POST['listing_id'] != "") {
            $listing_id = $_POST['listing_id'];
           
             if (wp_trash_post($listing_id)) {
                 echo '1|' . esc_html__("Listing removed successfully.", 'dwt-listing');
             } else {
                 echo '0|' . esc_html__("There's some problem, please try again later.", 'dwt-listing');
             }
        }
        die();
    }

}

/*  Booked listing status...*/

add_action('wp_ajax_booked_listing_status', 'dwt_listing_booked_listing_status');
if (!function_exists('dwt_listing_booked_listing_status')) {

    function dwt_listing_booked_listing_status()
    {
        global $dwt_listing_options;
       if (get_current_user_id() == "") {
            echo "0";
            die();
        }
        $booking_id =	$_POST['booking_id'];
        $status = $_POST['staus'];
        $author_id = get_post_field( 'post_author', $booking_id );
        $user_info = get_userdata($author_id);
        $user_email = $user_info->user_email;
        update_post_meta( $booking_id , 'booked_listings_reservations',   $status  );
          if(  $status  ==  'Approved'){
          invio_mail_approved($user_email);
        
          }
          else  if(  $status  ==  'rejected'){
            invio_mail($user_email);
          }
    }
}

//Booking rejected email
function invio_mail($user_email) {
    
    global $dwt_listing_api, $dwt_listing_options;
        $to = $user_email;
        $subject = __('Your Booking has been Approved', 'redux-framework');
       // $from =  get_option('admin_email');
       $headers = //array('Content-Type: text/html; charset=UTF-8', //"From: $from");
            $subject_keywords = array('%site_name%');
            $subject_replaces = array(get_bloginfo('name'));
            // $subject = str_replace($subject_keywords, $subject_replaces, $dwt_listing_options['dwt_listing_booking_rejected_subjects']);
        
            $user = get_user_by('email', $email);
            $msg_keywords = array('%site_name%', '%user%');
            $msg_replaces = array(get_bloginfo('name'), $user->display_name);
            $body = str_replace($msg_keywords, $msg_replaces, $dwt_listing_options['dwt_listing_booking_status_rejected_message']);
        
            wp_mail($to, $subject, $body, $headers);  
          
}

//booking accepted email
function invio_mail_approved($user_email) {
    
   global $dwt_listing_api, $dwt_listing_options;
        $to = $user_email;
        $subject = __('Your Booking has been rejected', 'redux-framework');
        $from =  get_option('admin_email');
       $headers = array('Content-Type: text/html; charset=UTF-8', "From: $from");
           $subject_keywords = array('%site_name%');
           $subject_replaces = array(get_bloginfo('name'));
           $subject = str_replace($subject_keywords, $subject_replaces, $dwt_listing_options['dwt_listing_booking_approved_subject']);
       
          
            $user = get_user_by('email', $email);
            $msg_keywords = array('%site_name%', '%user%');
            $msg_replaces = array(get_bloginfo('name'), $user->display_name);
            $body = str_replace($msg_keywords, $msg_replaces, $dwt_listing_options['dwt_listing_booking_status_approved_message']);

            wp_mail($to, $subject, $body, $headers);  
            print_r($body);
           
}

//adding ajax for events attendees
add_action('wp_ajax_event_attendees_status', 'dwt_listing_event_attendees_status');
if (!function_exists('dwt_listing_event_attendees_status')) {

    function dwt_listing_event_attendees_status()
    {
        global $dwt_listing_options;
       if (get_current_user_id() == "") {
            echo "0";
            die();
        }   
       
        $event_id =	$_POST['event_id'];
        $current_user = get_current_user_id();
        $events= get_post_meta($event_id, 'events_attendes' ,true);

        if(is_array($events) && !empty($events) ){
            $new_arr  = array_push($events, $current_user);
        }
        else{
            $events   =  array();
            $new_arr   = array_push($events, $current_user);
        }
      update_post_meta($event_id, 'events_attendes', $events);
            $return = array(  'message' => esc_html__('attending the event.', 'dwt-listing'));
			wp_send_json_success($return);
    }
}


//deleting post meta of not attending the event

add_action('wp_ajax_dwt_delete_event_attendance', 'dwt_delete_event_attendance');
if (!function_exists('dwt_delete_event_attendance')) {
    
    function dwt_delete_event_attendance()
    {
        global $dwt_listing_options;
        {    
            $event_id =$_POST['event_id'];   
            $current_user = get_current_user_id();
            $events = get_post_meta($event_id, 'events_attendes',true);

          //  print_r( $events);
            if (($key = array_search($current_user , $events)) !== false) {
                unset($events[$key]);      
            }

            update_post_meta($event_id, 'events_attendes', $events);
               
            }
                  
    }
}
