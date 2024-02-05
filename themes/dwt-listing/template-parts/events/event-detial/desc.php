<?php
global $dwt_listing_options;
	$event_id	=	get_the_ID();
	//user dp
	$get_user_dp = dwt_listing_listing_owner($event_id,'dp');
	//user dp
	$get_user_url = dwt_listing_listing_owner($event_id,'url');
	//
	$get_user_name = dwt_listing_listing_owner($event_id,'name');



    $get_user_name = dwt_listing_listing_owner($event_id,'name');

?>
<div class="col-md-8 col-sm-7 col-xs-12">
<div class="entry-content">
        <div class="event-title-zone">
            <h3><?php echo get_the_title($event_id); ?> </h3>
            <div class="modern-version-block-info">
                <div class="post-author">
                    <a href="<?php echo esc_url($get_user_url); ?>"><img src="<?php echo esc_url($get_user_dp); ?>" alt="<?php echo get_the_title($event_id); ?>"></a> <?php echo esc_html__('By','dwt-listing'); ?> <a href="<?php echo esc_url($get_user_url); ?>"><?php echo esc_attr($get_user_name); ?></a>
                    <span class="spliator">ــ</span><?php echo esc_html__('Last Update on ','dwt-listing'); ?> <?php the_modified_date(get_option( 'date_format'), '<a href="javascript:void(0)">', '</a>'); ?>
                    <?php
					if( function_exists('pvc_get_post_views') )
					{
					 echo '<span class="spliator">ــ</span>'.esc_html__("Views ", 'dwt-listing').'  '.dwt_listing_number_format_short(pvc_get_post_views( $event_id)) .'';
					}
					?>
                </div>
            </div>
        </div>
        <h4><?php echo esc_html__('Description','dwt-listing'); ?></h4>
        <?php the_content(); ?>
    </div>
</div>