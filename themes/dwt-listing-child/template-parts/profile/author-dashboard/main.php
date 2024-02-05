<?php

global $dwt_listing_options;
if (isset($_GET['listing-type']) && $_GET['listing-type'] != "") {
    $listing_type = $_GET['listing-type'];
    if ($listing_type == 'publish') {
        get_template_part('template-parts/profile/my-listings/publish');
    } else if ($listing_type == 'pending') {
        get_template_part('template-parts/profile/my-listings/pending');
    } else if ($listing_type == 'featured') {
        get_template_part('template-parts/profile/my-listings/featured');
    } else if ($listing_type == 'favourite') {
        get_template_part('template-parts/profile/my-listings/favourite');
    } else if ($listing_type == 'expired') {
        get_template_part('template-parts/profile/my-listings/expired');
    } 
    else if ($listing_type == 'trashed') {
        get_template_part('template-parts/profile/my-listings/trashed');
    } else if ($listing_type == 'profile') {
        get_template_part('template-parts/profile/update-profile/update');
    } else if ($listing_type == 'profile-update') {
        get_template_part('template-parts/profile/update-profile/update');
    } else if ($listing_type == 'submitted-reviews') {
        get_template_part('template-parts/profile/reviews/submitted');
    } else if ($listing_type == 'received-reviews') {
        get_template_part('template-parts/profile/reviews/received');
    } else if ($listing_type == 'create-events') {
        get_template_part('template-parts/profile/events/create');
    } else if ($listing_type == 'publish-events') {
        get_template_part('template-parts/profile/events/publish');
    } else if ($listing_type == 'pending-events') {
        get_template_part('template-parts/profile/events/pending');
    } else if ($listing_type == 'expired-events') {
        get_template_part('template-parts/profile/events/expired');
    } else if ($listing_type == 'create-menu') {
        get_template_part('template-parts/profile/menu/create');
    }//pasting
    else if ($listing_type == 'reservation') {
        get_template_part('template-parts/profile/reservation/reservation');
    }
    
    else if ($listing_type == 'booking-recieved') {
        get_template_part('template-parts/profile/reservation/booking-recieved');
    }
    else if ($listing_type == 'booking-sent') {
        get_template_part('template-parts/profile/reservation/booking-sent');
    }
    //
     else if ($listing_type == 'dashboard') {
        get_template_part('template-parts/profile/author-dashboard/dashboard');
    } else if ($listing_type == 'invoices') {
        if (!empty($_GET['order_id'])) {
            get_template_part('template-parts/profile/invoices/detail');
        } else {
            get_template_part('template-parts/profile/invoices/invoices');
        }
    } else if ($listing_type == 'booking-timekit') {
        get_template_part('template-parts/profile/booking/booking-timekit');
    } else {
        get_template_part('template-parts/profile/author-dashboard/dashboard');
    }
} else {
    get_template_part('template-parts/profile/author-dashboard/dashboard');
}