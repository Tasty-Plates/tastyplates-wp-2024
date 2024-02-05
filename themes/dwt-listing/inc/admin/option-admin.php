<?php
if (!defined('ABSPATH'))
    exit;
/* ------------------Blog Settings ----------------------- */
Redux::set_section($opt_name, array(
    'title' => esc_html__('Admin Options', 'dwt-listing'),
    'id' => 'dwt_listing_slugs',
    'desc' => '',
    'icon' => 'el el-asl',
    'fields' => array(
        array(
            'id' => 'dwt_listing_admin_bar_switch',
            'type' => 'switch',
            'title' => esc_html__('Hide/Show Admin Bar', 'dwt-listing'),
            'default' => true,
        ),
        array(
            'id' => 'dwt_listing_enable_packages',
            'type' => 'switch',
            'title' => esc_html__('Assign Free Package On Registration', 'dwt-listing'),
            'default' => true,
        ),
        array(
            'id' => 'dwt_listing_bult_renew_date',
            'type' => 'switch',
            'title' => esc_html__('Renew listings in bulk', 'dwt-listing'),
            'default' => false,
        ),
        array(
            'id' => 'dwt_listing_mov_trash_to_expire',
            'type' => 'switch',
            'title' => esc_html__('Move Trash to Expire', 'dwt-listing'),
            'default' => false,
        ),
        array(
            'id' => 'dwt_listing_notification_packages',
            'type' => 'info',
            'title' => esc_html__('Package Alert', 'dwt-listing'),
            'style' => 'critical',
            'desc' => esc_html__('This option will only work when ( WooCommerce Plugin ) is activeted & ( Downtown Packages ) are created.', 'dwt-listing'),
            'required' => array('dwt_listing_enable_packages', '=', true),
        ),
        array(
            'id' => 'dwt_listing_package_type',
            'type' => 'select',
            'data' => 'posts',
            'args' => array(
                'post_type' => array('product'),
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'order' => 'DESC',
                'orderby' => 'date',
            ),
            'title' => __('Select Package Only', 'dwt-listing'),
            'required' => array('dwt_listing_enable_packages', '=', true),
            'desc' => __('Only select package not shop products.', 'dwt-listing'),
        ),
        array(
            'id' => 'dwt_listing_disable_menu',
            'type' => 'switch',
            'title' => esc_html__('Enable Menu', 'dwt-listing'),
            'default' => true,
        ),
        array(
            'id' => 'dwt_listing_reserve_table',
            'type' => 'switch',
            'title' => esc_html__('Enable Reservation', 'dwt-listing'),
            'default' => true,
        ),
        array(
            'id' => 'dwt_listing_brand_default_image',
            'type' => 'media',
            'url' => true,
            'title' => __('Default Brand Image', 'dwt-listing'),
            'compiler' => 'true',
            'desc' => __('Select brand default image here', 'dwt-listing'),
            'default' => array('url' => trailingslashit(get_template_directory_uri()) . 'assets/images/brand.png'),
        ),
    )
));
