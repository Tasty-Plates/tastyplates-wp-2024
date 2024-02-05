<?php

global $dwt_listing_options;
$event_id = get_the_ID();
$user_id = get_post_field('post_author', $event_id);
$event_start_date = get_post_meta($event_id, 'dwt_listing_event_start_date', true);
$event_end_dateTime = (get_post_meta($event_id, 'dwt_listing_event_end_date', true));
$listing_features = wp_get_object_terms($event_id, array('l_event_cat'), array('orderby' => 'name', 'order' => 'ASC'));
$status_event = get_post_meta($event_id, 'dwt_listing_event_status', true);
//user info
$get_user_dp = $get_user_url = $get_user_name = $get_loc = $contact_num = $get_profile_contact = $get_event_contact = $get_profile_email = $get_email = '';
$get_user_dp = dwt_listing_listing_owner($event_id, 'dp');
$get_user_url = dwt_listing_listing_owner($event_id, 'url');
$get_user_name = dwt_listing_listing_owner($event_id, 'name');
$get_loc = dwt_listing_listing_owner($event_id, 'location');
$get_profile_contact = dwt_listing_listing_owner($event_id, 'contact');
$get_event_contact = get_post_meta($event_id, 'dwt_listing_event_contact', true);
$get_profile_email = dwt_listing_listing_owner($event_id, 'email');
$get_event_email = get_post_meta($event_id, 'dwt_listing_event_email', true);
$get_event_question = get_post_meta( $event_id, 'event_question', true );
// print_r($get_event_question);

//display contact number when profile contact not set
if (!empty($get_event_contact)) {
    $contact_num = $get_event_contact;
} else {
    $contact_num = $get_profile_contact;
}
//display email when profile email not set
if (!empty($get_event_email)) {
    $get_email = $get_event_email;
} else {
    $get_email = $get_profile_email;
}


// adding date and time format for google calendar

$start_date = date("Ymd", strtotime($event_start_date));
$start_time =   date("His", strtotime($event_start_date));
$start_format  =   $start_date  . "T" . $start_time;

$end_date = date("Ymd", strtotime($event_end_dateTime));
$end_time =   date("His", strtotime($event_end_dateTime));
$end_format  =   $end_date  . "T" . $end_time;

$google_date_url   =  $start_format . "/" .$end_format ;

$location_event = get_post_meta($event_id, 'dwt_listing_event_venue', true);
$my_content = get_the_content();

// adding date and time format for outlook calendar

$start_date_outlook_format = $event_start_date;
$start_date_outlook = date("Y-m-d", strtotime($event_start_date));
$start_time_outlook =   date("H:i:s\Z", strtotime($event_start_date));
$start_format_outlook  =   $start_date_outlook  . "T" . $start_time_outlook;

$end_date_outlooks_format = $event_end_dateTime;
$end_date_outlooks = date("Y-m-d", strtotime($event_end_dateTime));
$end_time_outlooks =   date("H:i:s\Z", strtotime($event_end_dateTime));
$end_format_outlooks  =   $end_date_outlooks  . "T" . $end_time_outlooks;


$outlook_date_url   =  $start_format_outlook . "/" . $end_format_outlooks ;

//google calendar event
$google_calendar_url = esc_url( 'http://www.google.com/calendar/render?action=TEMPLATE&text=' .
get_the_title( esc_attr( $event_id ) ) .
( !empty( $event_start_date ) ? '&dates='  . $google_date_url. '' : '') .
( !empty( $get_loc ) ? '&location=' . esc_attr( $location_event ) . '' : '' ) .
( !empty( $my_content ) ? '&details=' . esc_attr( $my_content ) . '' : '' ) . '' );


//outlook calendar
$outlook_calendar_url = esc_url('https://outlook.live.com/owa/?path=/calendar/view/Month&rru=addevent&subject='.
get_the_title( esc_attr( $event_id ) ) .
( !empty( $event_start_date ) ? '&startdt=' . esc_attr( $start_format_outlook): '') .
( !empty( $event_start_date ) ? '&dtend='   . esc_attr( $end_format_outlooks): '') .
( !empty( $get_loc ) ? '&location=' . esc_attr( $location_event ) . '' : '' ) .
( !empty( $my_content ) ? '&body=' . esc_attr( $my_content ) . '' : '' ) . '');


//Yahoo calendar event
$yahoo_calendar_url = esc_url( 'https://calendar.yahoo.com/?v=60&amp;&title=' .
get_the_title( esc_attr( $event_id ) ) .
( !empty( $event_start_date ) ? '&st='  . $start_format  : '') .
( !empty( $event_start_date ) ? '&et='  . $end_format  : '') .
( !empty( $get_loc ) ? '&in_loc=' . esc_attr( $location_event ) . '' : '' ) .
( !empty( $my_content ) ? '&desc=' . esc_attr( $my_content ) . '' : '' ) . '' );



$questions = get_post_meta($event_id, 'event_question', true);
$events_days = get_post_meta($event_id, 'days_schedule', true);

?>
<section class="single-post single-detail-page">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-xs-12 col-md-8 col-sm-8">
                <div class="list-detail">

                    <?php
                    /* Message for event has expired */
                    if (get_post_meta($event_id, 'dwt_listing_event_status', true) == '0') {
                        echo dwt_listing_event_expired_notification();
                    }
                    ?>
                    <div class="event-title-box">
                        <div class="list-heading">
                            <h2><?php echo get_the_title($event_id); ?></h2>
                        </div>
                        <div class="list-meta">
                            <ul>
                                <li>
                                    <i class="ti-calendar"></i> <?php echo esc_html__('Last Update on ', 'dwt-listing'); ?> <?php the_modified_date(get_option('date_format'), '<a href="javascript:void(0)">', '</a>'); ?>
                                    <?php
                                    if (function_exists('pvc_get_post_views')) {
                                        echo '<span class="spliator">ــ</span> <i class=" ti-eye "></i> ' . esc_html__(" Views ", 'dwt-listing') . '  ' . dwt_listing_number_format_short(pvc_get_post_views($event_id)) . '';
                                    }
                                    ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <?php get_template_part('template-parts/events/event-detial/modern/slider'); ?>
                    <div class="panel-group" id="accordion_listing_detial" role="tablist" aria-multiselectable="true">
                        <div class="panel panel-default panel-event-desc">
                            <div class="panel-heading">
                                <h4 class="panel-title"> <a href="javascript:void(0)"> <i class=" ti-file "></i><?php echo esc_html__('Description', 'dwt-listing'); ?>  </a> </h4>
                            </div>
                            <div class="panel-collapse">
                                <div class="panel-body">
                                    <?php the_content(); ?>
                                    <?php
                                    //related
                                    if (dwt_listing_text('dwt_listing_on_related') == '1') {
                                        $category_id = wp_get_object_terms($event_id, 'l_event_cat', array('fields' => 'ids'));
                                        $args = array
                                            (
                                            'post_type' => 'events',
                                            'post_status' => 'publish',
                                            'posts_per_page' => dwt_listing_text('app_event_related_nums'),
                                            'post__not_in' => array($event_id),
                                            'no_found_rows' => true,
                                            'meta_query' => array(
                                                array(
                                                    'key' => 'dwt_listing_event_status',
                                                    'value' => '1',
                                                    'compare' => '='
                                                )
                                            ),
                                            'tax_query' => array(
                                                array(
                                                    'taxonomy' => 'l_event_cat',
                                                    'field' => 'id',
                                                    'terms' => $category_id
                                                )
                                            ),
                                            'order' => 'desc',
                                            'orderby' => 'date',
                                        );
                                        $results = new WP_Query($args);
                                        if ($results->have_posts()) {
                                            ?>
                                            <div class="related-event-section">
                                                <h3><?php echo dwt_listing_text('dwt_related_section'); ?></h3>
                                                <div class="row">
                                                    <?php
                                                    while ($results->have_posts()) {
                                                        $results->the_post();
                                                        $related_id = get_the_ID();
                                                        $media = dwt_listing_fetch_event_gallery($related_id);
                                                        ?>
                                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                            <div class="e-related-grid">
                                                                <div class="related-event-img">
                                                                    <a href="<?php echo esc_url(get_the_permalink($related_id)); ?>"><img src="<?php echo dwt_listing_return_event_idz($media, 'dwt_listing_related_imgz'); ?>"  class="img-responsive"  alt="<?php echo get_the_title($related_id); ?>"></a>
                                                                </div>
                                                                <h3>
                                                                    <a href="<?php echo esc_url(get_the_permalink($related_id)); ?>"><?php echo get_the_title($related_id); ?></a>
                                                                </h3>
                                                                <span class="date"><i class="fa fa-calendar-o" aria-hidden="true"></i> <?php echo get_the_date(get_option('date_format'), $related_id); ?></span>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        wp_reset_postdata();
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php if (is_singular('events')) { ?>
                            <div class="events-post-navigation">
                                <div class="nav-links">
                                    <div class="nav-previous"><?php previous_post_link('%link', esc_html__('Previous Event', 'dwt-listing')); ?></div>
                                    <div class="nav-next"><?php next_post_link('%link', esc_html__('Next Event', 'dwt-listing')); ?></div>
                                </div>
                            </div>
                        <?php } ?>
                        <?php
                        if (get_post_meta($event_id, 'dwt_listing_event_status', true) != '0') {
                            ?>
                            <div class="panel panel-default eventz-comments">
                                <div class="panel-heading">
                                    <h4 class="panel-title"> <a  href="javascript:void(0)"> <i class="ti-comment-alt"></i><?php echo esc_html__('Discussion', 'dwt-listing'); ?> </a> </h4>
                                </div>
                                <div class="panel-collapse">
                                    <div class="panel-body">
                                        <div class="single-blog blog-detial">
                                            <div class="blog-post">
                                                <?php comments_template('', true); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <!--displaying event attendees-->
                        <div class="panel panel-default eventz-comments">
                        <?php
                            if(isset ($dwt_listing_options['event_attendees']) && $dwt_listing_options['event_attendees'] == 1){
                        ?>
                          <?php
                            $current_user = get_current_user_id();
                         if (is_user_logged_in() && $current_user == $post->post_author)  {


                         }else{
                        ?>
                                <div class="panel-heading">
                                    <h4 class="panel-title"> <a  href="javascript:void(0)"> <i class="ti-comment-alt"></i><?php echo esc_html__('Attendees', 'dwt-listing'); ?> </a> </h4>
                                </div>
                                <div class="panel-collapse">
                                    <div class="panel-body">
                                        <div class="single-blog blog-detial">
                                            <div class="events_attenders">
                                                <div class="blog-post">
                                                    <?php
                                                    $event_attendees= get_post_meta($event_id, 'events_attendes', true);

                                                    if (is_array($event_attendees) || is_object($event_attendees))
                                                    {
                                                        foreach($event_attendees as $attendee_id){
                                                            $attendee_data = get_userdata($attendee_id);
                                                            $attendee_name  =  $attendee_data->display_name;
                                                            $dp =  dwt_listing_get_user_dp($attendee_id, 'dwt_listing_user-dp');
                                                            ?>
                                                                <div class="attendees_prfile">
                                                                <?php  echo  '<img src="'.$dp.'"/>'  ."</br>". $attendee_name;
                                                                    ?>
                                                                </div>
                                                                <?php
                                                            }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php }}?>
                            </div>
                        <!--displaying event attendees end-->



                            <!--adding accordion for question and answer section -->
                            <div class="panel panel-default eventz-comments">
                                <?php
                                    if(isset ($dwt_listing_options['event_question']) && $dwt_listing_options['event_question'] == 1){
                                ?>
                                <div class="panel-heading">
                                    <h4 class="panel-title"> <a href="javascript:void(0)"> <i class="ti-comment-alt"></i>Frequently Asked Questions </a> </h4>
                                </div>
                                <div class="panel-collapse">
                                    <div class="panel-body">
                                        <div class="single-blog blog-detial">
                                            <div class="accordion_for_question">
                                                <!--adding questions and answers sections--->

                                                    <div class="accordion accordion-flush" id="accordionFlushExample">

                                                        <div class="accordion-item">
                                                            <?php
                                                            $count  = 1;
                                                            if (is_array($questions) || is_object($questions))
                                                            {


                                                            foreach($questions as $ques){?>

                                                                <?php $count ++;  ?>

                                                                <h6 class="accordion-header" id="flush-headingOne">
                                                                <button class="accordion-button collapsed" type="button" data-toggle="collapse" data-target="#flush-collapseOne<?php echo $count; ?>" aria-expanded="false" aria-controls="flush-headingOne">
                                                                    <b>Question: </b><?php echo $ques ['question']; ?><i class="ti-arrow-circle-down"></i>
                                                                </button>
                                                                </h6>
                                                                <div id="flush-collapseOne<?php echo $count; ?>" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-parent="#accordionFlushExample">
                                                                    <div class="accordion-body"><b>Answer:</b> <?php echo $ques ['answer']; ?>
                                                                    </div>
                                                                </div>
                                                            <?php } }?>
                                                        </div>

                                                    </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>


                             <!--adding accordion for days  and schedule section -->

                            <div class="days_schedule_accordion">
                                <div class="panel panel-default eventz-comments">

                                    <?php
                                        if(isset ($dwt_listing_options['days_schedule']) && $dwt_listing_options['days_schedule'] == 1){
                                    ?>
                                    <div class="panel-heading">
                                        <h4 class="panel-title"> <a href="javascript:void(0)"> <i class="ti-comment-alt"></i>Day Schedule</a> </h4>
                                    </div>
                                    <div class="panel-collapse">
                                        <div class="panel-body">
                                            <div class="single-blog blog-detial">

                                            <div class="accordion accordion-flush" id="accordionFlushExampleSection">

                                                            <div class="accordion-item">

                                                                <?php
                                                                $counting  = 1;

                                                                 if (isset($events_days) && ($events_days) != ''){
                                                                foreach($events_days as $scheduled_days){

                                                                    ?>

                                                                    <?php $counting++;  ?>

                                                                    <h6 class="accordion-header" id="flush-headingOne_Schedules">
                                                                    <button class="accordion-button collapsed" type="button" data-toggle="collapse" data-target="#flush-collapseOneSchedule<?php echo $counting; ?>" aria-expanded="false" aria-controls="flush-headingOne_Schedules">
                                                                     <b>Question : </b><?php echo $scheduled_days ['newDays']; ?> <i class="ti-arrow-circle-down"></i><br>

                                                                    </button>
                                                                    </h6>
                                                                    <div id="flush-collapseOneSchedule<?php echo $counting; ?>" class="accordion-collapse collapse" aria-labelledby="flush-headingOne_Schedules" data-parent="#accordionFlushExampleSection">
                                                                        <div class="accordion-body"><b>Answer:</b> <?php echo $scheduled_days ['schedule_description']; ?>
                                                                    </div>
                                                                    </div>
                                                                <?php }}?>
                                                            </div>

                                            </div>
                                        </div>
                                    </div>
                                </div><?php }?>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <div class="sidebar-panels">
                    <div class="panel panel-default side-author">
                        <div class="panel-heading">
                            <h4 class="panel-title"> <a href="javascript:void(0)"> <i class=" ti-user "></i><?php echo esc_html__('Author', 'dwt-listing'); ?>  </a> </h4>
                        </div>
                        <div class="panel-collapse">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="user-photo col-lg-4 col-md-4 col-sm-3  col-xs-12">
                                        <a target="_blank" href="<?php echo esc_url($get_user_url); ?>">
                                            <img src="<?php echo esc_url($get_user_dp); ?>" class="img-circle center-block img-responsive" alt="<?php __('not found', 'dwt-listing'); ?>">
                                        </a>
                                    </div>
                                    <div class="user-information col-lg-8 col-md-8 col-sm-9 col-xs-12 no-left-pad">
                                        <span class="user-name"><a class="hover-color" href="<?php echo esc_url($get_user_url); ?>"><?php echo esc_attr($get_user_name); ?></a></span>
                                        <?php if (!empty($get_loc)) { ?>
                                            <div class="item-date">
                                                <p class="street-adr"><i class="ti-location-pin"></i> <?php echo esc_attr($get_loc); ?></p>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <ul class="widget-listing-details">
                                    <?php if (!empty($get_profile_contact)) { ?>
                                        <li> <span> <img src="<?php echo esc_url(trailingslashit(get_template_directory_uri()) . 'assets/images/icons/phone.png'); ?>" alt="<?php echo esc_html__('image not found', 'dwt-listing'); ?>"></span> <span> <a href="tel:<?php echo esc_attr($get_profile_contact); ?>"><?php echo esc_attr($get_profile_contact); ?></a></span> </li>
                                    <?php } ?>
                                    <li> <span> <img src="<?php echo esc_url(trailingslashit(get_template_directory_uri()) . 'assets/images/icons/gmail.png'); ?>" alt="<?php echo esc_html__('image not found', 'dwt-listing'); ?>"></span> <span> <a href="mailto:<?php echo ($get_profile_email); ?>"><?php echo esc_attr($get_profile_email); ?></a></span> </li>
                                </ul>
                                <ul class="social-media-event">
                                    <?php if (get_user_meta($user_id, 'd_fb_link', true) != '') { ?>
                                        <li><a href="<?php echo esc_url(get_user_meta($user_id, 'd_fb_link', true)); ?>"><i class="ti-facebook"></i></a></li>
                                    <?php } ?>
                                    <?php if (get_user_meta($user_id, 'd_twitter_link', true) != '') { ?>
                                        <li><a href="<?php echo esc_url(get_user_meta($user_id, 'd_twitter_link', true)); ?>"><i class="ti-twitter"></i></a></li>
                                    <?php } ?>
                                    <?php if (get_user_meta($user_id, 'd_google_link', true) != '') { ?>
                                        <li><a href="<?php echo esc_url(get_user_meta($user_id, 'd_google_link', true)); ?>"><i class="ti-google"></i></a></li>
                                    <?php } ?>
                                    <?php if (get_user_meta($user_id, 'd_linked_link', true) != '') { ?>
                                        <li><a href="<?php echo esc_url(get_user_meta($user_id, 'd_linked_link', true)); ?>"><i class="ti-linkedin"></i></a></li>
                                    <?php } ?>
                                    <?php if (get_user_meta($user_id, 'd_youtube_link', true) != '') { ?>
                                        <li><a href="<?php echo esc_url(get_user_meta($user_id, 'd_youtube_link', true)); ?>"><i class=" ti-youtube "></i></a></li>
                                    <?php } ?>
                                    <?php if (get_user_meta($user_id, 'd_insta_link', true) != '') { ?>
                                        <li><a href="<?php echo esc_url(get_user_meta($user_id, 'd_insta_link', true)); ?>"><i class=" ti-instagram "></i></a></li>
                                    <?php } ?>

                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default side-event">
                        <div class="panel-heading">
                            <h4 class="panel-title"> <a href="javascript:void(0)"> <i class="ti-server"></i> <?php echo esc_html__('Event Information', 'dwt-listing'); ?> </a> </h4>
                        </div>
                        <div class="panel-collapse">
                            <div class="panel-body">
                                <ul class="widget-listing-details">
                                    <?php if ($status_event != '0') { ?>
                                        <li data-toggle="tooltip" data-placement="top" title="<?php echo esc_html__('Event start date', 'dwt-listing'); ?>"> <span> <img src="<?php echo esc_url(trailingslashit(get_template_directory_uri()) . 'assets/images/icons/timer.png'); ?>" alt="<?php echo esc_html__('icon', 'dwt-listing'); ?>"></span> <span class="sidebar_clock" data-countdown-time="<?php echo esc_attr($event_start_date); ?>"></span> </li>
                                    <?php } ?>
                                    <?php if (get_post_meta($event_id, 'dwt_listing_event_start_date', true) != "") { ?>
                                        <li data-toggle="tooltip" data-placement="top" title="<?php echo esc_html__('Event start date', 'dwt-listing'); ?>"> <span> <img src="<?php echo esc_url(trailingslashit(get_template_directory_uri()) . 'assets/images/icons/calendar.png'); ?>" alt="<?php echo esc_html__('icon', 'dwt-listing'); ?>"></span> <span> <?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime(get_post_meta($event_id, 'dwt_listing_event_start_date', true))); ?></span> </li>
                                    <?php } ?>
                                    <?php if (get_post_meta($event_id, 'dwt_listing_event_end_date', true) != "") { ?>
                                        <li data-toggle="tooltip" data-placement="top" title="<?php echo esc_html__('Event end date', 'dwt-listing'); ?>"> <span> <img src="<?php echo esc_url(trailingslashit(get_template_directory_uri()) . 'assets/images/icons/calendar-end.png'); ?>" alt="<?php echo esc_html__('icon', 'dwt-listing'); ?>"></span> <span><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime(get_post_meta($event_id, 'dwt_listing_event_end_date', true))); ?></span> </li>
                                    <?php } ?>
                                    <?php
                                    if (isset($listing_features) && $listing_features != "") {
                                        if (!is_wp_error($listing_features)) {
                                            foreach ($listing_features as $term) {
                                                $link = dwt_listing_pagelink('dwt_listing_event_page') . '?event_cat=' . $term->slug;
                                                ?>
                                                <li data-toggle="tooltip" data-placement="top" title="<?php echo esc_html__('Event category', 'dwt-listing'); ?>"> <span> <img src="<?php echo esc_url(trailingslashit(get_template_directory_uri()) . 'assets/images/icons/database.png'); ?>" alt="<?php echo esc_html__('icon', 'dwt-listing'); ?>"></span> <span> <a href="<?php echo esc_url($link); ?>"><?php echo esc_attr($term->name); ?></a></span> </li>
                                                <?php
                                                break;
                                            }
                                        }
                                    }
                                    ?>
                                    <?php if (get_post_meta($event_id, 'dwt_listing_event_listing_id', true) != "") { ?>
                                        <li data-toggle="tooltip" data-placement="top" title="<?php echo esc_html__('Related listings', 'dwt-listing'); ?>"> <span> <img src="<?php echo esc_url(trailingslashit(get_template_directory_uri()) . 'assets/images/icons/click.png'); ?>" alt="<?php echo esc_html__('icon', 'dwt-listing'); ?>"></span> <span> <a href="<?php echo get_permalink(get_post_meta($event_id, 'dwt_listing_event_listing_id', true)); ?>" target="_blank"><?php echo esc_html__('View Listing', 'dwt-listing'); ?></a></span> </li>
                                    <?php } ?>
                                    <?php if (get_post_meta($event_id, 'dwt_listing_event_contact', true) != "") { ?>
                                        <li> <span> <img src="<?php echo esc_url(trailingslashit(get_template_directory_uri()) . 'assets/images/icons/phone.png'); ?>" alt="<?php echo esc_html__('icon', 'dwt-listing'); ?>"></span> <span><a href="tel:<?php echo get_post_meta($event_id, 'dwt_listing_event_contact', true); ?>"><?php echo get_post_meta($event_id, 'dwt_listing_event_contact', true); ?></a></span> </li>
                                    <?php } ?>
                                    <?php if (get_post_meta($event_id, 'dwt_listing_event_email', true) != "") { ?>
                                        <li> <span> <img src="<?php echo esc_url(trailingslashit(get_template_directory_uri()) . 'assets/images/icons/email.png'); ?>" alt="<?php echo esc_html__('icon', 'dwt-listing'); ?>"></span> <span> <a href="mailto:<?php echo (get_post_meta($event_id, 'dwt_listing_event_email', true)); ?>"><?php echo esc_html__('Contact Email', 'dwt-listing'); ?></a></span> </li>
                                    <?php } ?>
                                </ul>
                                <?php
                                if ((get_post_meta($event_id, 'dwt_event_fb', true) != "") || (get_post_meta($event_id, 'dwt_event_tw', true) != "") || (get_post_meta($event_id, 'dwt_event_google', true) != "") || (get_post_meta($event_id, 'dwt_event_in', true) != "") || (get_post_meta($event_id, 'dwt_event_youtube', true) != "") || (get_post_meta($event_id, 'dwt_event_insta', true) != "") || (get_post_meta($event_id, 'dwt_event_whatsapp', true) != "")) {
                                    ?>
                                    <ul class="social-media-event">
                                        <?php if (get_post_meta($event_id, 'dwt_event_fb', true) != "") { ?>
                                            <li><a href="<?php echo esc_url(get_post_meta($event_id, 'dwt_event_fb', true)); ?>"><i class="ti-facebook"></i></a></li>
                                        <?php } ?>
                                        <?php if (get_post_meta($event_id, 'dwt_event_tw', true) != "") { ?>
                                            <li><a href="<?php echo esc_url(get_post_meta($event_id, 'dwt_event_tw', true)); ?>"><i class="ti-twitter"></i></a></li>
                                        <?php } ?>
                                        <?php if (get_post_meta($event_id, 'dwt_event_google', true) != "") { ?>
                                            <li><a href="<?php echo esc_url(get_post_meta($event_id, 'dwt_event_google', true)); ?>"><i class="ti-google"></i></a></li>
                                        <?php } ?>
                                        <?php if (get_post_meta($event_id, 'dwt_event_in', true) != "") { ?>
                                            <li><a href="<?php echo esc_url(get_post_meta($event_id, 'dwt_event_in', true)); ?>"><i class="ti-linkedin"></i></a></li>
                                        <?php } ?>
                                        <?php if (get_post_meta($event_id, 'dwt_event_youtube', true) != "") { ?>
                                            <li><a href="<?php echo esc_url(get_post_meta($event_id, 'dwt_event_youtube', true)); ?>"><i class="ti-youtube"></i></a></li>
                                        <?php } ?>
                                        <?php if (get_post_meta($event_id, 'dwt_event_insta', true) != "") { ?>
                                            <li><a href="<?php echo esc_url(get_post_meta($event_id, 'dwt_event_insta', true)); ?>"><i class=" ti-instagram"></i></a></li>
                                        <?php } ?>
                                        <?php if (get_post_meta($event_id, 'dwt_event_whatsapp', true) != "") { ?>
                                            <li><a target="_blank" href="https://api.whatsapp.com/send?phone=<?php echo (get_post_meta($event_id, 'dwt_event_whatsapp', true)); ?>"><i class="fa fa-whatsapp" aria-hidden="true"></i></a></li>
                                        <?php } ?>
                                    </ul>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default side-map">
                        <div class="panel-heading">
                            <h4 class="panel-title"> <a href="javascript:void(0)"> <i class="ti-location-pin"></i> <?php echo esc_html__('Location', 'dwt-listing'); ?> </a> </h4>
                        </div>
                        <div class="panel-collapse">
                            <div class="panel-body">
                                <?php get_template_part('template-parts/events/event-detial/modern/map'); ?>
                            </div>
                        </div>
                    </div>
                                <!--button for add events to calendars-->
                    <div class="panel panel-default side-map">
                        <div class="panel-heading">
                            <h4 class="panel-title"> <a href="javascript:void(0)"> <i class="ti-location-pin"></i> Add Event To Calendar </a> </h4>
                        </div>
                        <div class="panel panel-default add-to-calendar">
                            <!-- Trigger/Open The Modal -->
                            <button id="myBtn">Add To Calendar</button>
                            <!-- The Modal -->
                            <div id="myModal" class="modal">
                            <!-- Modal content -->
                                <div class="modal-content">
                                    <div class="modal-top">
                                        <span class="add-to-calendar">Add To Calendar</span>
                                        <span class="close">&times;</span>
                                    </div>
                                    <div class="all-calendars">
                                        <a href="<?php echo $google_calendar_url;?>" target="_blank">
                                            <div class="google-calendar">
                                                <div class="row">
                                                    <div class="col-lg-8 m-auto">
                                                        <div class="calendar-icon">
                                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                                            <span>Google Calendar</span>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </a>

                                            <!--adding outlook calendar from here-->
                                            <a href="<?php echo $outlook_calendar_url;?>" target="_blank">
                                                <div class="outlook-calendars">
                                                    <div class="row">
                                                        <div class="col-lg-8 m-auto">
                                                            <div class="calendar-icon">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-calendar3" viewBox="0 0 16 16">
                                                                    <path d="M14 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zM1 3.857C1 3.384 1.448 3 2 3h12c.552 0 1 .384 1 .857v10.286c0 .473-.448.857-1 .857H2c-.552 0-1-.384-1-.857V3.857z"/>
                                                                    <path d="M6.5 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                                                                </svg>
                                                                <span>Outlook Calendar </span>
                                                            </div>
                                                        </div>

                                                    </div>

                                                </div>
                                            </a>


                                            <!--adding yahoo calendar from here-->
                                            <a href="<?php echo $yahoo_calendar_url;?>" target="_blank">
                                                <div class="outlook-calendars yahoo-calendar">
                                                    <div class="row">
                                                        <div class="col-lg-8 m-auto">
                                                            <div class="calendar-icon">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-calendar2-event" viewBox="0 0 16 16">
                                                                <path d="M11 7.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1z"/>
                                                                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H2z"/>
                                                                <path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5V4z"/>
                                                            </svg>
                                                                <span>Yahoo  Calendar</span>
                                                            </div>
                                                        </div>

                                                    </div>

                                                </div>
                                            </a>



                                    </div>

                                </div>


                            </div>
                        </div>

                    </div>



                    <div class="panel panel-default side-map">
                        <?php
                            $current_user = get_current_user_id();
                         if (is_user_logged_in() && $current_user == $post->post_author)  {


                         }else{
                        ?>
                        <div class="panel-heading">
                            <h4 class="panel-title"> <a href="javascript:void(0)"> <i class="ti-user"></i> Event Attendies </a> </h4>
                        </div>
                        <div class="panel panel-default events-attending">
                            <?php
                            $user_id = get_current_user_id();
                            $event_id = get_the_ID();

                            $colors = get_post_meta($event_id, 'events_attendes',true);

                            if(is_array($colors) && !empty($colors)  && in_array($user_id , $colors)){

                                ?>
                                <button type="button" class="btn btn-primary" event-data-delete-id="<?php echo $event_id;  ?>" id="cancel_attendance">Not Going?</button>
                          <?php   }
                            else {
                          ?>
                            <button type="button" class="btn btn-primary" event-data-id="<?php echo $event_id;  ?>" id="attending_event">Going</button>
                                <?php
                            }
                            ?>
                        </div>
                        <?php }?>
                    </div>
                    <!---attending schedule or not? --->





                </div>
            </div>
        </div>
    </div>
</section>