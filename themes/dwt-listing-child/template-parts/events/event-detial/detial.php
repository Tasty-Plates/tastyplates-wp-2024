<?php

global $dwt_listing_options;
$event_id = get_the_ID();
$event_start_date = get_post_meta($event_id, 'dwt_listing_event_start_date', true);
$event_end_dateTime = (get_post_meta($event_id, 'dwt_listing_event_end_date', true));
$status_event = get_post_meta($event_id, 'dwt_listing_event_status', true);
?>
<section class="dwt_listing_single-event">
    <div class="container">
        <?php
        //notification for event has expired
        if (get_post_meta($event_id, 'dwt_listing_event_status', true) == '0') {
            echo dwt_listing_event_expired_notification();
        }
        ?>
        <?php get_template_part('template-parts/events/event-detial/slider'); ?>
        <div class="clearfix"></div>
        <div class="row dwt_listing_single-detial">
            <div class="col-md-8 col-xs-12 col-sm-12">
                <?php get_template_part('template-parts/admin', 'approval'); ?>
                <div class="row">
                    <?php get_template_part('template-parts/events/event-detial/short', 'info'); ?>
                    <?php get_template_part('template-parts/events/event-detial/desc'); ?>
                </div>
                <?php get_template_part('template-parts/events/event-detial/map'); ?>

                <?php
                if (get_post_meta($event_id, 'dwt_listing_event_status', true) != '0') {
                    ?>
                    <div class="single-blog blog-detial">
                        <div class="blog-post">
                            <?php comments_template('', true); ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="col-md-4 col-xs-12 col-sm-12">
                <div class="blog-sidebar">
                    <?php
                    $custom_color = '';
                    $clock_icon = '<div class="dwt_listing_timer-icon"><i class="tool-tip fa fa-clock-o"  title="' . esc_html__('Time Left In Event', 'dwt-listing') . '"></i></div>';
                    //if event is started
                    if (dwt_listing_check_event_starting($event_id) == '0') {
                        $custom_color = 'eventz-statred';
                        $clock_icon = '<div class="dwt_listing_timer-icon green-clock"><i class="tool-tip fa fa-clock-o"  title="' . esc_html__('Event Started', 'dwt-listing') . '"></i></div>';
                    }
                    ?>
                    <?php if($status_event != '0'){  ?>
                    <div class="list-bottom-area <?php echo esc_attr($custom_color); ?>">
                        <?php echo '' . ($clock_icon); ?>
                        <div class="dwt_listing_timer-count">
                            <div class="dwt_listing_countdown-timer">
                                <div class="timer-countdown-box">
                                    <div class="countdown dwt_listing_custom-timer" data-countdown-time="<?php echo esc_attr($event_start_date); ?>"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php }  ?>
                    <?php if (is_active_sidebar('dwt_listing_events-sidebar')) { ?>
                        <?php dynamic_sidebar('dwt_listing_events-sidebar'); ?>
                    <?php } ?>    
                </div>
            </div>
        </div>
    </div>
</section>