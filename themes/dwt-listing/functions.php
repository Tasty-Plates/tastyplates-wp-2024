<?php

/**
* dwt listing functions and definitions.
*
* @link https://developer.wordpress.org/themes/basics/theme-functions/
*
* @package dwt listing
*/
add_action( 'after_setup_theme', 'dwt_listing_theme_setup' );
if ( !function_exists( 'dwt_listing_theme_setup' ) ) {

    /* dwt listing Theme Settings */

    function dwt_listing_theme_setup()
 {
        /* Theme Settings */
        require trailingslashit( get_template_directory() ) . 'inc/theme_settings.php';
        /* Register Custom Navigation Walker */
        require trailingslashit( get_template_directory() ) . 'inc/nav.php';
        /* Load Essential Functions */
        require trailingslashit( get_template_directory() ) . 'inc/theme-functions.php';
        /* Load Theme Functions */
        require trailingslashit( get_template_directory() ) . 'inc/utilities.php';
        /* Load Theme Widgets */
        require trailingslashit( get_template_directory() ) . 'inc/widgets.php';
        /* Theme Classes */
        require trailingslashit( get_template_directory() ) . 'inc/classes/index.php';
        /* Theme Shortcodes */
        require trailingslashit( get_template_directory() ) . 'inc/theme_shortcodes/shortcodes.php';
        /* Load Redux Options */
        require trailingslashit( get_template_directory() ) . 'inc/options.php';
        /* Load Theme Widgets */
        require trailingslashit( get_template_directory() ) . 'inc/listing-widgets.php';
        /* TGM */
        require trailingslashit( get_template_directory() ) . 'tgm/tgm-init.php';
    }

}

/**
* Register custom fonts.
*/

function dwt_listing_fonts_url( $font_call = '' )
 {
    $fonts_url = '';
    if ( is_rtl() ) {
        if ( $font_call == 'xb-niloofar' ) {
            $font_families = 'xb-niloofar';
        }
        if ( $font_call == 'droid-arabic-naskh' ) {
            $font_families = 'droid-arabic-naskh';
        }
        if ( $font_call == 'popins' ) {
            $fonts_url = 'https://fonts.googleapis.com/css?family=Open+Sans:400,600';
        } else {
            $fonts_url = 'https://fontlibrary.org/face/' . $font_families;
        }
    }
    return esc_url_raw( $fonts_url );
}

/* ------------------------------------------------ */
/* Enqueue scripts and styles. */
/* ------------------------------------------------ */
add_action( 'wp_enqueue_scripts', 'dwt_listing_scripts' );

function dwt_listing_scripts()
 {
    global $dwt_listing_options;
    $assets_dir = trailingslashit( get_template_directory_uri() ) . 'assets/';
    /* Enqueue scripts. */
    wp_enqueue_script( 'bootstrap', $assets_dir . 'js/bootstrap.min.js', false, false, true );
    wp_enqueue_script( 'dropzone', $assets_dir . 'js/dropzone.js', false, false, true );
    wp_enqueue_script( 'dwt-listing-plugins', $assets_dir . 'js/plugins.js', false, false, true );
    wp_enqueue_script( 'dwt-register-hello', $assets_dir . 'js/hello.js', false, false, true );
    wp_enqueue_script( 'validator', $assets_dir . 'js/validator.min.js', false, false, true );
    wp_enqueue_script( 'dwt-listing-registration', $assets_dir . 'js/registration.js', false, false, true );
    if ( is_page_template( 'page-profile.php' ) ) {
        wp_enqueue_script( 'slimscroll', $assets_dir . 'js/jquery.slimscroll.min.js', false, false, true );
    }
    wp_enqueue_script( 'dwt-listing-profile', $assets_dir . 'js/profile.js', false, false, true );
    wp_enqueue_script( 'dwt-listing-listing', $assets_dir . 'js/listing.js', false, false, true );
    wp_enqueue_script( 'jquery-confirm', $assets_dir . 'js/jquery-confirm.js', false, false, true );
    wp_enqueue_script( 'typeahead-adv', $assets_dir . 'js/typeahead.adv.js', false, false, true );
    wp_enqueue_script( 'leaflet', $assets_dir . 'js/map/leaflet.js', false, false, true );
    wp_enqueue_script( 'leaflet-search', $assets_dir . 'js/map/leaflet-search.min.js', false, false, true );
    if ( dwt_listing_text( 'dwt_listing_recaptcha' ) == 1 ) {
        wp_enqueue_script( 'recaptcha', '//www.google.com/recaptcha/api.js', false, false, true );
    }
    if ( $dwt_listing_options[ 'dwt_listing_enable_map' ] == '1' && $dwt_listing_options[ 'dwt_map_selection' ] == 'google_map' && $dwt_listing_options[ 'gmap_api_key' ] != '' ) {
        $map_lang = 'en';
        if ( isset( $dwt_listing_options[ 'dwt_listing_gmap_lang' ] ) && $dwt_listing_options[ 'dwt_listing_gmap_lang' ] != '' ) {
            $map_lang = $dwt_listing_options[ 'dwt_listing_gmap_lang' ];
        }
        wp_enqueue_script( 'google-map', '//maps.googleapis.com/maps/api/js?key=' . $dwt_listing_options[ 'gmap_api_key' ] . '&language=' . $map_lang, false, false, true );
        /* call back */
        wp_register_script( 'google-map-callback', '//maps.googleapis.com/maps/api/js?key=' . $dwt_listing_options[ 'gmap_api_key' ] . '&libraries=geometry,places&language=' . $map_lang . '&callback=' . 'dwt_listing_location', false, false, true );
    }
    if ( is_singular() )
    wp_enqueue_script( 'comment-reply', '', true );
    if ( isset( $dwt_listing_options[ 'dwt_listing_enable_video_option' ] ) && $dwt_listing_options[ 'dwt_listing_enable_video_option' ] != '' ) {
        wp_enqueue_style( 'popup-video-iframe-style', $assets_dir . 'css/video_player.css' );
        wp_enqueue_script( 'popup-video-iframe', $assets_dir . 'js/video_player.js', false, false, true );
    }
    wp_enqueue_script( 'theia-sticky-sidebar', $assets_dir . 'js/theia-sticky-sidebar.js', false, false, true );
    wp_enqueue_script( 'jquery-ui-sortable' );
    wp_enqueue_script( 'imagesloaded' );
    //only for search page
    if ( isset( $dwt_listing_options[ 'dwt_listing_seacrh_page' ] ) && $dwt_listing_options[ 'dwt_listing_seacrh_page' ] != '' && is_page() || is_tax( array( 'l_category', 'l_tags', 'l_location' ) ) ) {
        if ( get_page_template_slug( $dwt_listing_options[ 'dwt_listing_seacrh_page' ] ) == get_page_template_slug( get_the_ID() ) || is_tax( array( 'l_category', 'l_tags', 'l_location' ) ) ) {
            //leaflet
            if ( dwt_listing_text( 'dwt_listing_seacrh_layout' ) == 'map' ) {
                wp_enqueue_script( 'leaflet-loader', $assets_dir . 'js/map/leafloader.js', false, false, true );
                wp_enqueue_script( 'leaflet-cluster', $assets_dir . 'js/map/leaflet.markercluster.js', false, false, true );
                wp_enqueue_script( 'dwt-listing-search', $assets_dir . 'js/listing-search/search_map.js', false, false, true );
            } else if ( dwt_listing_text( 'dwt_listing_seacrh_layout' ) == 'topbar' ) {
                //for sidebar
                wp_enqueue_script( 'dwt-listing-search', $assets_dir . 'js/listing-search/search_topbar.js', false, false, true );
            } else {
                //for sidebar
                wp_enqueue_script( 'dwt-listing-search', $assets_dir . 'js/listing-search/search_sidebar.js', false, false, true );
            }
        }
    }
    if ( is_page_template( 'page-events.php' ) && is_page() || is_tax( array( 'l_event_cat', 'l_event_tags' ) ) ) {
        if ( dwt_listing_text( 'dwt_listing_event_layout' ) == 'map' ) {
            wp_enqueue_script( 'leaflet-loader', $assets_dir . 'js/map/leafloader.js', false, false, true );
            wp_enqueue_script( 'leaflet-cluster', $assets_dir . 'js/map/leaflet.markercluster.js', false, false, true );
            wp_enqueue_script( 'dwt-listing-search', $assets_dir . 'js/event-search/events_map.js', false, false, true );
        } else {
            wp_enqueue_script( 'dwt-listing-search', $assets_dir . 'js/event-search/events_simple.js', false, false, true );
        }

    }

    if ( is_page_template( 'page-listing.php' ) && is_page() ) {
        wp_enqueue_script( 'jquery-ui-autocomplete' );
        wp_enqueue_script( 'date-en', $assets_dir . 'js/submit-listing/date-en-US.js', array( 'jquery' ), false, true );
        wp_enqueue_script( 'timeselect', $assets_dir . 'js/submit-listing/jquery.ui.timeselect.js', array( 'jquery' ), false, true );
    }

    if ( isset( $dwt_listing_options[ 'dwt_listing_lp_style' ] ) && $dwt_listing_options[ 'dwt_listing_lp_style' ] != '' && $dwt_listing_options[ 'dwt_listing_lp_style' ] == 'minimal' && is_page_template( 'page-listing.php' ) ) {
        wp_enqueue_script( 'bootstrap-fileinput', $assets_dir . 'js/bootstrap-imageupload.min.js', array( 'jquery' ), false, true );
    }
    if ( !empty( $dwt_listing_options[ 'dwt_listing_locationz' ] ) && $dwt_listing_options[ 'dwt_listing_locationz' ] == '1' ) {
        wp_enqueue_script( 'cookies', $assets_dir . 'js/js.cookie.js', array( 'jquery' ), false, true );
    }
    if ( is_singular( 'listing' ) ) {
        /* == enqueue timekit js for fast load == */
        if ( isset( $dwt_listing_options[ 'dwt_listing_booking_time_kit' ] ) && ( $dwt_listing_options[ 'dwt_listing_booking_time_kit' ] != 0 || $dwt_listing_options[ 'dwt_listing_booking_time_kit' ] != false ) ) {
            wp_enqueue_script( 'timekit', $assets_dir . 'js/booking.min.js', array( 'jquery' ), false, true );
        }
    }

    wp_enqueue_script( 'dwt-listing-custom', $assets_dir . 'js/custom.js', array( 'jquery' ), false, true );
    /* Load the stylesheets. */
    wp_enqueue_style( 'dwt-listing-style', get_stylesheet_uri() );
    wp_enqueue_style( 'bootstrap_min', $assets_dir . 'css/bootstrap.min.css' );
    if ( is_rtl() ) {
        wp_enqueue_style( 'bootstrap-rtl', $assets_dir . 'css/rtl/bootstrap-rtl.min.css' );
        wp_enqueue_style( 'dwt-listing-mega-menu', $assets_dir . 'css/rtl/mega-menu-rtl.css' );
    } else {
        wp_enqueue_style( 'dwt-listing-mega-menu', $assets_dir . 'css/mega-menu.css' );
    }
    wp_enqueue_style( 'dwt_listing_plugins_styles', $assets_dir . 'css/plugins.css' );
    if ( is_rtl() ) {
        wp_enqueue_style( 'dwt-listing-fonts-xb-niloofar', dwt_listing_fonts_url( 'xb-niloofar' ), array(), null );
        wp_enqueue_style( 'dwt-listing-fonts-droid-arabic-naskh', dwt_listing_fonts_url( 'droid-arabic-naskh' ), array(), null );
        wp_enqueue_style( 'dwt-listing-fonts-popins', dwt_listing_fonts_url( 'popins' ), array(), null );
        wp_enqueue_style( 'dwt-listing-woo', $assets_dir . 'css/rtl/woocommerce-rtl.css' );
    } else {
        wp_enqueue_style( 'dwt-listing-woo', $assets_dir . 'css/woocommerce.css' );
    }
    if ( is_page_template( 'page-profile.php' ) ) {
        wp_enqueue_style( 'dwt-listing-admin-styling', $assets_dir . 'css/admin/dwt-admin.css' );
        wp_enqueue_style( 'dwt-listing-admin-icons', $assets_dir . 'css/admin/admin-fonts.css' );
        if ( is_rtl() ) {
            wp_enqueue_style( 'dwt-listing-admin-rtl', $assets_dir . 'css/admin/rtl/admin-rtl.css' );
        }
    } else {
        wp_enqueue_style( 'dwt-listing-styling', $assets_dir . 'css/dwt-listing.css' );
    }
    if ( is_rtl() ) {
        wp_enqueue_style( 'leaflet', $assets_dir . 'css/map/leaflet-rtl.css' );
        wp_enqueue_style( 'leaflet-search', $assets_dir . 'css/map/leaflet-search-rtl.min.css' );
        wp_enqueue_style( 'dwt-listing-style-rtl', $assets_dir . 'css/rtl/dwt-listing-rtl.css' );
        wp_enqueue_style( 'dwt-listing-custom', $assets_dir . 'css/rtl/custom-rtl.css' );
        wp_enqueue_style( 'dwt-listing-responsive', $assets_dir . 'css/rtl/responsive-rtl.css' );
    } else {
        wp_enqueue_style( 'leaflet', $assets_dir . 'css/map/leaflet.css' );
        wp_enqueue_style( 'leaflet-search', $assets_dir . 'css/map/leaflet-search.min.css' );
        wp_enqueue_style( 'dwt-listing-custom', $assets_dir . 'css/custom.css' );
        wp_enqueue_style( 'dwt-listing-responsive', $assets_dir . 'css/responsive.css' );
    }

    wp_enqueue_style( 'dwt-listing-icons', $assets_dir . 'css/all-icons.css' );
    wp_enqueue_style( 'dwt-flat-icons', $assets_dir . 'css/flaticons.css' );
}

/* ==  get current page url == */
if ( !function_exists( 'dwt_listing_get_current_url' ) ) {

    function dwt_listing_get_current_url()
 {
        return $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

}

function dwt_listing_custom_theme_setup()
 {
    add_theme_support( 'html5', array( 'comment-list' ) );
}

add_action( 'after_setup_theme', 'dwt_listing_custom_theme_setup' );
add_action( 'after_setup_theme', 'dwt_listing_hide_adminbar' );
if ( !function_exists( 'dwt_listing_hide_adminbar' ) ) {

    function dwt_listing_hide_adminbar()
 {
        if ( is_user_logged_in() && !is_admin() && !( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
            $user = wp_get_current_user();
            if ( in_array( 'subscriber', $user->roles ) ) {
                // user has subscriber role
                show_admin_bar( false );
            }
        }
    }

}
add_action( 'get_header', 'dwt_listing_remove_admin_login_header' );

function dwt_listing_remove_admin_login_header()
 {
    remove_action( 'wp_head', '_admin_bar_bump_cb' );
}

/* = Cron Jobs = */
$my_url = '';
$my_url = dwt_listing_get_current_url();
if ( strpos( $my_url, 'listing.downtown-directory.com' ) !== false ) {

} else {
    // delete_post_revisions will be call when the Cron is executed
    add_action( 'dwt_listing_event_expire', 'dwt_listing_event_status_expire' );
}
// This function will run once the 'dwt_listing_event_status_expire' is called

function dwt_listing_event_status_expire()
 {
    $args = array(
        'post_type' => 'events',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'dwt_listing_event_status',
                'value' => 1,
                'compare' => '=',
            ),
        ),
    );
    $result = new WP_Query( $args );
    while ( $result->have_posts() ) {
        $result->the_post();
        $eventID = get_the_ID();
        //call the function for change event status if event has been expire.
        dwt_listing_check_event_ending( $eventID );
    }
}

global $dwt_listing_options;
$cron_switch = $cron_interval = '';
$dwt_listing_options = get_option( 'dwt_listing_options' );
if ( is_array( $dwt_listing_options ) && !empty( $dwt_listing_options ) ) {
    if ( isset( $dwt_listing_options[ 'dwt_listing_event_cron_switch' ] ) && $dwt_listing_options[ 'dwt_listing_event_cron_switch' ] != '' ) {
        $cron_switch = $dwt_listing_options[ 'dwt_listing_event_cron_switch' ];
        if ( $dwt_listing_options[ 'dwt_listing_events_cron_interval' ] != '' ) {
            $cron_interval = $dwt_listing_options[ 'dwt_listing_events_cron_interval' ];
        } else {
            $cron_interval = 'daily';
        }
        if ( isset( $cron_switch ) && $cron_switch ) {
            // Make sure this event hasn't been scheduled
            if (!wp_next_scheduled('dwt_listing_event_expire')) {
                // Schedule the event
                wp_schedule_event(time(), $cron_interval, 'dwt_listing_event_expire');
            }
        }
        /* =  Un-scheduling Events = */
        if (isset($cron_switch) && !$cron_switch) {
            // Get the timestamp of the next scheduled run
            $timestamp = wp_next_scheduled('dwt_listing_event_expire');
            // Un-schedule the event
            wp_unschedule_event($timestamp, 'dwt_listing_event_expire');
        }
    }
}
/* == hide admin bar  == */
if (is_array($dwt_listing_options) && !empty($dwt_listing_options)) {
    if (isset($dwt_listing_options['dwt_listing_admin_bar_switch']) && $dwt_listing_options['dwt_listing_admin_bar_switch'] == false) {
        add_filter('show_admin_bar', '__return_false');
    }
}

/* check page build with elementor */
if (!function_exists('dwt_check_is_elementor')) {

    function dwt_check_is_elementor($page_id)
    {
        if (class_exists('Elementor\Plugin')) {
            return \Elementor\Plugin::$instance->db->is_built_with_elementor($page_id);
        } else {
            return false;
        }
    }

}
/*
 * Woocommerce template overwride
 */
/* Remove breadcrumbs from shop & categories */
add_filter('woocommerce_before_main_content', 'remove_breadcrumbs');
function remove_breadcrumbs()
{
    if (is_product()) {
        remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
    }
}

remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);

add_action('send_headers', 'dwt_block_iframes');
if (!function_exists('dwt_block_iframes')) {
    function dwt_block_iframes()
    {

        header('X-FRAME-OPTIONS: SAMEORIGIN');
    }
}

/*
 * Elementor Header and Footer
 */
if (in_array('elementor-pro/elementor-pro.php', apply_filters('active_plugins', get_option('active_plugins')))){
    add_action( 'elementor/theme/register_locations', 'theme_prefix_register_elementor_locations' );
    function theme_prefix_register_elementor_locations( $elementor_theme_manager ) {

        $elementor_theme_manager->register_location( 'header' );
        $elementor_theme_manager->register_location( 'footer' );
        }
    }

    //Get Template Slug
if( !function_exists('dwt_framework_get_options') )
{
	function dwt_framework_get_options($get_text)
	{
		global $maxwheels_options;
		if(isset($maxwheels_options[$get_text]) &&  $maxwheels_options[$get_text] !=""):
			return $maxwheels_options[$get_text];
		else:
			return false;
		endif;
	}
}

// Get Specific Page Link
if (!function_exists('dwt_framwork_get_link'))
{
    function dwt_framwork_get_link($page_name)
	{
		if(!empty($page_name))
		{
			$archive_id = $archive_page = '';
			$archive_page = get_pages(
				array(
					'meta_key' => '_wp_page_template',
					'meta_value' => $page_name
				)
			);
			if(!empty($archive_page))
			{
				$archive_id = $archive_page[0]->ID;
				return  get_permalink( $archive_id );
			}
		}
	}
}

//Return dynamic data
if (!function_exists('dwt_params'))
{
    function dwt_params($param)
    {
        if(!empty($param))
        {
          return $param;  
        }
    }
}


// Our custom post type function
function create_posttype() {
  
    register_post_type( 'reservation',
    // CPT Options
        array(
            'labels' => array(
                'name' => __( 'Reservations' ),
                'singular_name' => __( 'Reservation' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'reservations'),
            'show_in_rest' => true,
  
        ),
    );
}
// Hooking up our function to theme setup
add_action( 'init', 'create_posttype' );




function create_reservation_posttype( $post_id ) {
    global $post; 
    

    if(isset($_POST['reserver_name']) ){
        update_post_meta( $post_id, 'reserver_name' ,$_POST['reserver_name']);
    }      
    if(isset($_POST['reserver_number']) ){
        update_post_meta( $post_id, 'reserver_number' ,$_POST['reserver_number']);
    }      
    if(isset($_POST['reserver_email']) ){
        update_post_meta( $post_id, 'reserver_email' ,$_POST['reserver_email']);
    }      
    if(isset($_POST['reservation_day']) ){
        update_post_meta( $post_id, 'reservation_day' ,$_POST['reservation_day']);
    }    
    if(isset($_POST['reserved_listing']) ){
        update_post_meta( $post_id, 'reserved_listing' ,$_POST['reserved_listing']);
    }      
    if(isset($_POST['reserved_day']) ){
        update_post_meta( $post_id, 'reserved_day' ,$_POST['reserved_day']);
    }     

}
add_action( 'save_post', 'create_reservation_posttype' );





add_filter('manage_reservation_posts_columns', function($columns) {
     
    $columns['name']    =   __('Name', 'dwt-listing');
    $columns['mobnum']  =   __('Mobile Number', 'dwt-listing');
    $columns['email']   =   __('Email Address', 'dwt-listing');
    $columns['day_name'] =   __('Date', 'dwt-listing');
    $columns['day_time'] =   __('Selected Time', 'dwt-listing');
    $columns['listing_title'] =   __('Selected Listing', 'dwt-listing');
    $columns['listing_id'] =   __('ID', 'dwt-listing');
    $columns['booking_status'] =   __('Booking Status', 'dwt-listing');
  
    return $columns;
    $date  =   isset($_POST['date'])  ? $_POST['date']  : ""; 
        $listing_id   =   isset($_POST['ad_id'])  ? $_POST['ad_id']  : ""; 
        if($date != ""){
           $week_days  =  lcfirst(date('l',strtotime($date)));
             //print_r($week_days);
        }
});
add_action('manage_reservation_posts_custom_column', function($column_key, $my_reservation_post) {
   

    if ($column_key == 'name') {
        echo get_post_meta( $my_reservation_post, 'dwt_listing_reserver_name', true );
    }
    if($column_key == 'mobnum'){
        echo get_post_meta( $my_reservation_post, 'dwt_listing_reserver_number', true );
    }
    if ($column_key == 'day_name') {
        echo get_post_meta($my_reservation_post, 'dwt_listing_reservation_day', true);
    }
    if ($column_key == 'listing_id') {
        echo get_post_meta($my_reservation_post, 'dwt_listing_reservation_id', true);
    }
    if ($column_key == 'email'){
        echo get_post_meta( $my_reservation_post, 'dwt_listing_reserver_email', true );
    }
    if ($column_key == 'day_time'){
        echo get_post_meta( $my_reservation_post, 'dwt_listing_reservation_time', true );
    }
    if ($column_key == 'listing_title'){
        echo get_post_meta( $my_reservation_post, 'dwt_listing_reservation_title', true );
    }
    if ($column_key == 'booking_status'){
        echo get_post_meta( $my_reservation_post, 'booked_listings_reservations', true );   
     
        
    
    }
    
   
}, 10, 2,);
