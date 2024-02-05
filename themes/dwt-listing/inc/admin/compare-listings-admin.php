<?php 
if (!defined('ABSPATH'))
exit;
// Options for Listing comparison 
Redux::set_section( $opt_name, array(
    'title'  => esc_html__( 'Compare Listings', 'maxwheels' ),
    'id'     => 'mw_compare',
    'desc'   => '',
    'subsection' => false,
    'fields' => array(
        array(
            'id'       => 'mw_compare_tagline',
            'type'     => 'text',
            'title'    => esc_html__( 'Tagline', 'maxwheels' ),
            'default' => esc_html__('Spot the difference', 'maxwheels'),
        ),
		array(
            'id'       => 'mw_compare_heading',
            'type'     => 'text',
            'title'    => esc_html__( 'Heading', 'maxwheels' ),
            'default' => esc_html__('Compare Listing', 'maxwheels'),
        ),
        array(
            'id'       => 'mw_empty_list',
            'type'     => 'text',
            'title'    => esc_html__( 'Empty List Message', 'maxwheels' ),
            'default' => esc_html__('Add listings to compare.', 'maxwheels'),
        ),
        array(
            'id'       => 'mw_empty_msg',
            'type'     => 'text',
            'title'    => esc_html__( 'Empty Page Message', 'maxwheels' ),
            'default' => esc_html__('Please select more than one listing to compare.', 'maxwheels'),
        ),
    )
));