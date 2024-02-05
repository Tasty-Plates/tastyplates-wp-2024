<?php
// Register post  type and taxonomy
add_action('init', 'dwt_listing_listing_custom_types', 1);

function dwt_listing_listing_custom_types() {
    // Register Post type Listing
    $listing_slug = 'listing';
    if (class_exists('Redux')) {
        $data = $listing_slug_val = '';
        $data = get_option('dwt_listing_options');
        if (!empty($data)) {
            if (isset($data['dwt_listing_listing_slug']) && $data['dwt_listing_listing_slug'] != "") {
                $listing_slug_val = $data['dwt_listing_listing_slug'];
                if (!empty($listing_slug_val)) { 
                    $listing_slug = trim($listing_slug_val);
                }
            }
        }
    }
    $args = array(
        'public' => true,
        'label' => __('Listings', 'dwt-listing-framework'),
        'supports' => array('title', 'editor'),
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => true,
        'has_archive' => true,
        'rewrite' => array('with_front' => false, 'slug' => $listing_slug)
    );
    register_post_type('listing', $args);
    //Categories
    $final_slug = 'categories';
    if (class_exists('Redux')) {
        $data = $cat_slug = '';
        $data = get_option('dwt_listing_options');
        if (!empty($data)) {
            if (isset($data['dwt_listing_cat_slug']) && $data['dwt_listing_cat_slug'] != "") {
                $cat_slug = $data['dwt_listing_cat_slug'];
                if (!empty($cat_slug)) {
                    $final_slug = trim($cat_slug);
                }
            }
        }
    }
    register_taxonomy('l_category', array('listing'), array(
        'hierarchical' => true,
        'show_ui' => true,
        'label' => __('Categories / Features', 'dwt-listing-framework'),
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => $final_slug),
    ));
    //Tags
    $tags_slug = 'tags';
    if (class_exists('Redux')) {
        $data = $tags_slug_val = '';
        $data = get_option('dwt_listing_options');
        if (!empty($data)) {
            if (isset($data['dwt_listing_tags_slug']) && $data['dwt_listing_tags_slug'] != "") {
                $tags_slug_val = $data['dwt_listing_tags_slug'];
                if (!empty($tags_slug_val)) {
                    $tags_slug = trim($tags_slug_val);
                }
            }
        }
    }
    register_taxonomy('l_tags', array('listing'), array(
        'hierarchical' => true,
        'label' => __('Tags', 'dwt-listing-framework'),
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => $tags_slug),
    ));
    //Features 
    register_taxonomy('l_price_type', array('listing'), array(
        'hierarchical' => true,
        'label' => __('Price Type', 'dwt-listing-framework'),
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'l_price_type'),
    ));
    //Listing Currency
    register_taxonomy('l_currency', array('listing'), array(
        'hierarchical' => true,
        'label' => __('Currency', 'dwt-listing-framework'),
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'l_currency'),
    ));
    //Country
    $loc_slug = 'location';
    if (class_exists('Redux')) {
        $data = $loc_slug_val = '';
        $data = get_option('dwt_listing_options');
        if (!empty($data)) {
            if (isset($data['dwt_listing_loc_slug']) && $data['dwt_listing_loc_slug'] != "") {
                $loc_slug_val = $data['dwt_listing_loc_slug'];
                if (!empty($loc_slug_val)) {
                    $loc_slug = trim($loc_slug_val);
                }
            }
        }
    }
    register_taxonomy('l_location', array('listing'), array(
        'hierarchical' => true,
        'show_ui' => true,
        'label' => __('Location', 'dwt-listing-framework'),
        'query_var' => true,
        'rewrite' => array('slug' => $loc_slug),
    ));
    //Form Fields
}

// Email on Listing publish
add_action('transition_post_status', 'dwt_listing_send_mails_on_publish_listing', 10, 3);

function dwt_listing_send_mails_on_publish_listing($new_status, $old_status, $post) {
    if ('publish' !== $new_status or 'publish' === $old_status
            or 'listing' !== get_post_type($post))
        return;
    global $dwt_listing_options;
    if (isset($dwt_listing_options['email_on_ad_approval']) && $dwt_listing_options['email_on_ad_approval']) {
        dwt_listing_notify_on_listing_approval($post);
    }
}
