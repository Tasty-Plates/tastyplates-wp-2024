<?php
global $dwt_listing_options;

$profile = new dwt_listing_profile();
$user_id = $profile->user_info->ID;
$my_events = '';
if (dwt_listing_text('dwt_map_selection') == "google_map" && dwt_listing_text('gmap_api_key') != "" && dwt_listing_text('dwt_listing_enable_map') == "1") {
    dwt_listing_google_locations(true);
    wp_enqueue_script("google-map-callback");
}
$location_icon = $check_class = '';
if (dwt_listing_text('dwt_listing_enable_geo') == '1' && dwt_listing_text('dwt_map_selection') == 'open_street') {
    $check_class = 'get-loc';
    $location_icon = '<i class="detect-me fa fa-crosshairs"></i>';
}
$event_cat = $event_id = '';
$cat_name = $link = $to = $from = $cat_name = $link = $term_list = $event_start = '';
$event_venue_loc = $event_end = '';
$listing_lattitude = dwt_listing_text('dwt_listing_default_lat');
$listing_longitide = dwt_listing_text('dwt_listing_default_long');
$event_end_date = $event_start_date = $event_cat = $selected = $parent_listing = $event_venue = $event_email = $event_contact = $event_tagline = $event_desc = $event_title = $is_update = '';
if (isset($_GET['edit_event']) && $_GET['edit_event'] != "") {
    $event_id = $_GET['edit_event'];
    $is_update = $event_id;
    $post = get_post($event_id);
    $event_title = $post->post_title;
    $event_desc = $post->post_content;
    $event_contact = get_post_meta($event_id, 'dwt_listing_event_contact', true);
    $event_email = get_post_meta($event_id, 'dwt_listing_event_email', true);
    $event_start_date = get_post_meta($event_id, 'dwt_listing_event_start_date', true);
    $event_end_date = get_post_meta($event_id, 'dwt_listing_event_end_date', true);
    $event_venue = get_post_meta($event_id, 'dwt_listing_event_venue', true);
    $listing_lattitude = get_post_meta($event_id, 'dwt_listing_event_lat', true);
    $listing_longitide = get_post_meta($event_id, 'dwt_listing_event_long', true);
    $parent_listing = get_post_meta($event_id, 'dwt_listing_event_listing_id', true);
    $event_question= get_post_meta( $event_id, 'dwt_listing_event_question', true);
    $event_answer= get_post_meta( $event_id, 'dwt_listing_event_answer', true);
    $event_days= get_post_meta( $event_id, 'dwt_listing_event_days', true);
    $event_textareas= get_post_meta( $event_id, 'dwt_listing_event_textareas', true);
    
    
    //event term
    $term_list = wp_get_post_terms($event_id, 'l_event_cat', array("fields" => "ids"));
    if (!empty($term_list)) {
        $event_cat = $term_list[0];
    }
}
$get_argsz = $profile->dwt_listing_fetch_owner_events_admin();
?>
<div class="container-fluid">
    <?php get_template_part('template-parts/profile/author-stats/event', 'stats'); ?>
    <div class="row">
        <div class="col-md-8 col-lg-8 col-xs-12 col-sm-12">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php echo esc_html__('Create Event', 'dwt-listing'); ?></h3>
                </div>
                <div class="panel-body">
                    <form id="my-events">
                        <div class="preloading" id="dwt_listing_loading"></div>
                        <div class="submit-listing-section">
                            <div class="row">
                                <div class="col-md-12 col-xs-12 col-sm-12">
                                    <div class="form-group has-feedback">
                                        <label class="control-label"><?php echo esc_html__('Event Title', 'dwt-listing'); ?><span>*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="ti-pencil"></i></span>
                                            <?php
                                            if (dwt_listing_text('dwt_listing_disable_edit') == '1' && !is_super_admin(get_current_user_id())) {
                                                echo '<input disabled type="text" class="form-control" placeholder="' . esc_html__('Event Title', 'dwt-listing') . '" value="' . esc_attr($event_title) . '">';
                                            } else {
                                                ?>
                                                <input id="event_title" type="text" class="form-control" name="event_title" placeholder="<?php echo esc_html__('Event Title', 'dwt-listing'); ?>" value="<?php echo esc_attr($event_title); ?>" required >
                                            <?php } ?>
                                            <div id="show-me" class="loader-field"></div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                /* Event categories */
                                $event_cats = dwt_listing_categories_fetch('l_event_cat', 0);
                                if (!empty($event_cats) && is_array($event_cats) && count($event_cats) > 0) {
                                    ?>
                                    <div class="col-md-12 col-xs-12 col-sm-12">
                                        <div class="form-group has-feedback">
                                            <label class="control-label"><?php echo esc_html__('Select Category', 'dwt-listing'); ?> <span>*</span></label>
                                            <select data-placeholder="<?php echo esc_html__('Select Event Category', 'dwt-listing'); ?>" name="event_cat" class="custom-select" required>
                                                <option value=""><?php echo esc_html__('Select an option', 'dwt-listing'); ?></option>
                                                <?php
                                                foreach ($event_cats as $eventz) {
                                                    ?>
                                                    <option value="<?php echo esc_attr($eventz->term_id) ?>" <?php if ($eventz->term_id == $event_cat) { ?>selected="selected"<?php } ?>><?php echo esc_attr($eventz->name) ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                                <div class="col-md-6 col-xs-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label"><?php echo esc_html__('Phone Number', 'dwt-listing'); ?></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="ti-mobile"></i></span>
                                            <input type="text" class="form-control" name="event_number" placeholder="<?php echo esc_html__('+99 3331 234567', 'dwt-listing'); ?>" value="<?php echo esc_attr($event_contact); ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label"><?php echo esc_html__('Contact Email', 'dwt-listing'); ?><span>*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="ti-world"></i></span>
                                            <input type="email" required class="form-control" name="event_email" placeholder="<?php echo esc_html__('abc@xyz.com', 'dwt-listing'); ?>" value="<?php echo esc_attr($event_email); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 col-xs-12 col-sm-12">
                                    <div class="form-group has-feedback">
                                        <label class="control-label"><?php echo esc_html__('Description', 'dwt-listing'); ?><span>*</span></label>
                                        <textarea name="event_desc" class="jqte-test" required><?php echo '' . $event_desc; ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label"> <?php echo esc_html__('Event Start Date', 'dwt-listing'); ?> <span>*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="ti-time"></i></span>
                                            <input class="form-control" name="event_start_date" type="text" id="event_start" data-time-format='hh:ii aa' autocomplete="off" value="<?php echo esc_attr($event_start_date); ?>" required/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label"> <?php echo esc_html__('Event End Date', 'dwt-listing'); ?> <span>*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="ti-time"></i></span>
                                            <input class="form-control" name="event_end_date" type="text" id="event_end" data-time-format='hh:ii aa' autocomplete="off" value="<?php echo esc_attr($event_end_date); ?>" required/>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-12 col-xs-12 col-sm-12 ">
                                    <div class="form-group">
                                        <label class="control-label"><?php echo dwt_listing_text('dwt_listing_list_gallery'); ?></label>
                                        <div id="event_dropzone" class="dropzone upload-ad-images event_zone"><div class="dz-message needsclick">
                                                <?php echo esc_html__('Event Gallery Images', 'dwt-listing'); ?>
                                                <br />
                                                <span class="note needsclick"><?php echo esc_html__('Drop files here or click to upload', 'dwt-listing'); ?> </span>
                                            </div></div>
                                    </div>

                                    <div class="clearfix"></div>
                                    <div id="listing_msgz" class="alert custom-alert custom-alert--warning none" role="alert">
                                        <div class="custom-alert__top-side">
                                            <span class="alert-icon custom-alert__icon  ti-face-sad "></span>
                                            <div class="custom-alert__body">
                                                <h6 class="custom-alert__heading">
                                                    <?php echo esc_html__('Whoops.....!', 'dwt-listing'); ?>
                                                </h6>
                                                <div class="custom-alert__content"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 col-xs-12 col-sm-12">
                                    <div class="form-group <?php echo esc_attr($check_class); ?>">
                                        <label class="control-label"><?php echo esc_html__('Event Location', 'dwt-listing'); ?><span>*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="ti-location-pin"></i></span>
                                            <input type="text" class="form-control" id="address_location" name="event_venue"  placeholder="<?php echo dwt_listing_text('dwt_listing_list_google_loc_place'); ?>" value="<?php echo esc_attr($event_venue); ?>" required>
                                            <?php echo $location_icon; ?>
                                        </div>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <?php
                                if (dwt_listing_text('dwt_listing_enable_map') == "1") {
                                    $map_id = 'submit-map-open';
                                    if (dwt_listing_text('dwt_map_selection') == "google_map" && dwt_listing_text('gmap_api_key') != "") {
                                        $map_id = 'submit-listing-map';
                                    }
                                    ?>	
                                    <div class="col-md-6 col-xs-12 col-sm-6">
                                        <div class="form-group">
                                            <label class="control-label"><?php echo esc_html__('Latitude', 'dwt-listing'); ?></label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="ti-map-alt"></i></span>
                                                <input type="text" class="form-control" id="d_latt" name="event_lat" placeholder="<?php echo dwt_listing_text('dwt_listing_list_lati_place'); ?>" value="<?php echo esc_attr($listing_lattitude); ?>">
                                            </div>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xs-12 col-sm-6">
                                        <div class="form-group">
                                            <label class="control-label"><?php echo esc_html__('Longitude', 'dwt-listing'); ?></label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="ti-map-alt"></i></span>
                                                <input type="text" class="form-control" id="d_long" name="event_long" placeholder="<?php echo dwt_listing_text('dwt_listing_list_longi_place'); ?>" value="<?php echo esc_attr($listing_longitide); ?>">
                                            </div>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-xs-12 col-sm-12">
                                        <div class="form-group">
                                            <div class="submit-post-img-container">
                                                <div id="<?php echo esc_attr($map_id); ?>"></div>
                                            </div>
                                        </div>
                                    </div>

                                

                           
                            
                                      
<!--adding questions and answers section-->

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="event-quereis-forms">
                <div class="query-input">
                                
                    <?php
                    if(isset ($dwt_listing_options['event_question']) && $dwt_listing_options['event_question'] == 1){
                    ?>
                    <div class="faq_forms">
                            <h3>Add Some Questions</h3>
                        <div  id="event_questionaire" >
                        
                            <?php    
                        
                            $questions = get_post_meta($event_id, 'event_question', true);   
                        
                            if(is_array($questions)  && !empty($questions)) {
                                foreach($questions as $ques){?>
                                <input type="text" id="question" name='event_question[question][]' value='<?php if(isset($ques['question']) != '') echo $ques['question']; ?>' placeholder="Your Question Here">
                                <input type="text" id="answer" name='event_question[answer][]' value='<?php if(isset($ques['answer']) != "") echo $ques ['answer']; ?>' placeholder="Your Answer Here">
                                <button class="btn remove_added_question" id="addNameButton" type="button">Add Question</button>
                                <?php }  ?>     
                                <div class="add_new_questions">  
                                <button class="btn" id="addNameButton" type="button">Add Question</button>
                                </div>
                        
                                <?php     
                                }
                                else {   ?>
                                <input type="text" id="question" name='event_question[question][]' value='<?php if(isset($ques['question']) != '') echo $ques['question']; ?>' placeholder="Your Question Here">
                                <input type="text" id="answer" name='event_question[answer][]' value='<?php if(isset($ques['answer']) != "") echo $ques ['answer']; ?>' placeholder="Your Answer Here">
                                <button class="btn" id="addNameButton" type="button">Add Question</button>

                                <?php  }


                                ?>
                        </div>
                        <div class="query-input-lists">
                        <ul id="listOfQuestions"></ul>
                        <!-- <ul id="listOfAnswers"></ul> -->
                    </div>
                    </div>
                    <?php }?>

                   
                    <?php
                    if(isset ($dwt_listing_options['days_schedule']) && $dwt_listing_options['days_schedule'] == 1){
                    ?>
                    <!--fields for textareas-->
                    <div class="add_textarea">
                       
                        <div class="">
                            <h3>Add A New Day</h3>
                                <div class="adding_new_days_schedule">
                                    <?php
                                    $days_schedules = get_post_meta($event_id, 'days_schedule', true);
                                    // if(is_array($days_schedules)  && !empty($days_schedules)) {
                                    //     foreach($days_schedules as $days){  
                                                    
                                    if(is_array($days_schedules)  &&  !empty($days_schedules)){
                                    foreach($days_schedules as $days){ ?>
                                    
                                    <input type="text" id="newDays" name='days_schedule[newDays][]' value='<?php if(isset($days['newDays']) != '') echo $days['newDays']; ?>'  placeholder="Day Name Here......">
                                    <textarea id="schedule_description" class="jqte-primary" name='days_schedule[schedule_description][]' value='' placeholder="Add Some Description Here"><?php if(isset($days['schedule_description']) != "") echo $days['schedule_description']; ?></textarea>
                                    
                                    <button class="btn" id="addDaysButton" class="addDaysScheduleButton" type="button">Add Schedule</button>
                                    <?php }  ?>
                                
                            <?php      }
                                    
                                    else {  ?>

                                    <input type="text" id="newDays" name='days_schedule[newDays][]' value='<?php if(isset($days['newDays']) != '') echo $days['newDays']; ?>'  placeholder="Day Name Here......">
                                    <textarea  type="text" class="jqte-primary" id="schedule_description" name='days_schedule[schedule_description][]' placeholder="Add Some Text Here"><?php if(isset($days['schedule_description']) != "") echo $days['schedule_description']; ?></textarea>
                                    <button class="btn" id="addDaysButton" class="addDaysScheduleButton" type="button">Add Schedule</button>

                                    <?php }
                                    
                                    ?>
                                </div>
                        </div>
                        <div class="events_days_schedule">
                            <ul id="listOfDaysName"></ul>
                        </div>
                    </div>
                    <?php }?>

                </div>
               
        </div>
    </div> 
</div>

                                    <?php
                                }
                                ?>
                                <?php
                                /* Author Listings */
                                $get_args = $profile->dwt_listing_fetch_my_listings();
                                $my_listings = new WP_Query($get_args);
                                if ($my_listings->have_posts()) {
                                    ?>
                                    <div class="col-md-12 col-xs-12 col-sm-12">
                                        <div class="form-group has-feedback">
                                            <label class="control-label"><?php echo esc_html__('Related Listing', 'dwt-listing'); ?> <small><?php echo esc_html__('(optional)', 'dwt-listing') ?></small></label>
                                            <select data-placeholder="<?php echo esc_html__('Select Your Listing', 'dwt-listing'); ?>" name="event_parent_listing" class="custom-select">
                                                <option value=""><?php echo esc_html__('Select an option', 'dwt-listing'); ?></option>
                                                <?php
                                                while ($my_listings->have_posts()) {
                                                    $my_listings->the_post();
                                                    $listing_id = get_the_ID();
                                                    ?>
                                                    <option <?php if ($listing_id == $parent_listing) { ?>selected="selected"<?php } ?> value="<?php echo esc_attr($listing_id) ?>"><?php echo esc_attr(get_the_title($listing_id)); ?></option>
                                                    <?php
                                                }
                                                wp_reset_postdata();
                                                ?>
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                                <input type="hidden" id="is_update" name="is_update" value="<?php echo esc_attr($is_update); ?>">
                                <div  class="col-md-12 col-xs-12 col-sm-12">
                                    <div class="submit-post-button">
                                        <?php
                                        if (dwt_listing_text('dwt_listing_disable_edit') == '1' && !is_super_admin(get_current_user_id())) {
                                            echo '<button type="button" class="btn btn-admin tool-tip" title="' . esc_html__('Disable for Demo', 'dwt-listing') . '" disabled> ' . esc_html__('Save & preview', 'dwt-listing') . ' </button>';
                                        } else {
                                            ?>
                                            <button type="submit" class="btn btn-admin sonu-button"  data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo esc_html__("Processing...", 'dwt-listing'); ?>"><?php echo esc_html__("Save & preview", 'dwt-listing'); ?></button>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" id="event_upload_limit" value="<?php echo esc_attr($dwt_listing_options['dwt_listing_event_upload_limit']); ?>" />
                        <input type="hidden" id="event_img_size" value="<?php echo esc_attr($dwt_listing_options['dwt_listing_event_images_size']); ?>" />
                        <input type="hidden" id="max_upload_reach" value="<?php echo __('Maximum upload limit reached', 'dwt-listing'); ?>" />
                        <input type="hidden" id="dictDefaultMessage" value="<?php echo dwt_listing_text('dwt_listing_list_gallery_desc'); ?>" />
                        <input type="hidden" id="dictFallbackMessage" value="<?php echo esc_html__('Your browser does not support drag\'n\'drop file uploads', 'dwt-listing'); ?> "/>
                        <input type="hidden" id="dictFallbackText" value="<?php echo esc_html__('Please use the fallback form below to upload your files like in the olden days', 'dwt-listing'); ?> "/>
                        <input type="hidden" id="dictFileTooBig" value="<?php echo esc_html__('File is too big ({{filesize}}MiB). Max filesize: {{maxFilesize}}MiB', 'dwt-listing'); ?>" />
                        <input type="hidden" id="dictInvalidFileType" value="<?php echo esc_html__('You can\'t upload files of this type', 'dwt-listing'); ?>" />
                        <input type="hidden" id="dictResponseError" value="<?php echo esc_html__('Server responded with {{statusCode}} code', 'dwt-listing'); ?>" />
                        <input type="hidden" id="dictCancelUpload" value="<?php echo esc_html__('Cancel upload', 'dwt-listing'); ?>" />
                        <input type="hidden" id="dictCancelUploadConfirmation" value="<?php echo esc_html__('Are you sure you want to cancel this upload?', 'dwt-listing'); ?>" />
                        <input type="hidden" id="dictRemoveFile" value="<?php echo esc_html__('Remove file', 'dwt-listing'); ?>" />
                        <input type="hidden" id="dictMaxFilesExceeded" value="<?php echo esc_html__('You can not upload any more files', 'dwt-listing'); ?>" />
                        <?php dwt_listing_form_lang_field_callback(true); ?>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-xs-12 col-sm-12">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php echo esc_html__('Latest Events', 'dwt-listing'); ?></h3>
                </div>
                <div class="panel-body">
                    <?php
                    $my_events = new WP_Query($get_argsz);
                    if ($my_events->have_posts()) {
                        while ($my_events->have_posts()) {

                            $my_events->the_post();
                            $event_id = get_the_ID();
                            //get media
                            $media = dwt_listing_fetch_event_gallery($event_id);
                            $event_start_date = get_post_meta($event_id, 'dwt_listing_event_start_date', true);
                            $event_end_date = get_post_meta($event_id, 'dwt_listing_event_end_date', true);
                            $event_venue = get_post_meta($event_id, 'dwt_listing_event_venue', true);
                            $term_list = wp_get_post_terms($event_id, 'l_event_cat', array("fields" => "all"));
                            if (!empty($term_list)) {
                                $link = get_term_link($term_list[0]->term_id);
                                $cat_name = $term_list[0]->name;
                            }
                            if ($event_start_date != "") {
                                $from = date_i18n(get_option('time_format'), strtotime($event_start_date));
                                $event_start = date_i18n(get_option('date_format'), strtotime($event_start_date));
                            }
                            if ($event_end_date != "") {
                                $to = date_i18n(get_option('time_format'), strtotime($event_end_date));
                                $event_end = date_i18n(get_option('date_format'), strtotime($event_end_date));
                            }
                            ?>	
                            <div class="schedule-info">
                                <div class="event_thumb_admin">
                                    <a href="<?php echo get_the_permalink($event_id); ?>"><img class="img-responsive" src="<?php echo dwt_listing_return_event_idz($media, 'dwt_listing_user-dp'); ?>" alt="<?php echo get_the_title($event_id); ?>"></a>
                                </div>
                                <div class="event_admin_detial">
                                    <h4 class="time"><?php echo ($from); ?> - <?php echo ($to); ?></h4>
                                    <h3 class="title"><a href="<?php echo get_the_permalink($event_id); ?>"><?php echo get_the_title($event_id); ?></a></h3>
                                    <p class="author-info"><i class="lnr lnr-calendar-full"></i> <span><?php echo ($event_start); ?> - <?php echo ($event_end); ?></span></p>
                                </div>
                            </div>
                            <?php
                        }
                        wp_reset_postdata();
                        ?>
                        <?php
                    } else {
                        get_template_part('template-parts/profile/events/content', 'none');
                    }
                    ?>
                </div>
            </div>
        </div>

    </div>
</div>
<?php
if (dwt_listing_text('dwt_listing_enable_map') == "1" && dwt_listing_text('dwt_map_selection') == "google_map" && dwt_listing_text('gmap_api_key') != "") {
    echo '<script type="text/javascript">
    var markers = [
        {
			
            "title": "",
            "lat": "' . $listing_lattitude . '",
            "lng": "' . $listing_longitide . '",
        },
    ];
    window.onload = function () {
        	my_g_map(markers);
        }
		function my_g_map(markers1)
		{
			var mapOptions = {
            center: new google.maps.LatLng(markers1[0].lat, markers1[0].lng),
            zoom: 12,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var infoWindow = new google.maps.InfoWindow();
        var latlngbounds = new google.maps.LatLngBounds();
        var geocoder = geocoder = new google.maps.Geocoder();
        var map = new google.maps.Map(document.getElementById("submit-listing-map"), mapOptions);
            var data = markers1[0]
            var myLatlng = new google.maps.LatLng(data.lat, data.lng);
            var marker = new google.maps.Marker({
                position: myLatlng,
                map: map,
                title: data.title,
                draggable: true,
                animation: google.maps.Animation.DROP
            });
            (function (marker, data) {
                google.maps.event.addListener(marker, "click", function (e) {
                    infoWindow.setContent(data.description);
                    infoWindow.open(map, marker);
                });
                google.maps.event.addListener(marker, "dragend", function (e) {
					document.getElementById("dwt_listing_loading").style.display	= "block";
                    var lat, lng, address;
                    geocoder.geocode({ "latLng": marker.getPosition() }, function (results, status) {
						
                        if (status == google.maps.GeocoderStatus.OK) {
                            lat = marker.getPosition().lat();
                            lng = marker.getPosition().lng();
                            address = results[0].formatted_address;
							document.getElementById("d_latt").value = lat;
							document.getElementById("d_long").value = lng;
							document.getElementById("address_location").value = address;
							document.getElementById("dwt_listing_loading").style.display	= "none";
                            //alert("Latitude: " + lat + "\nLongitude: " + lng + "\nAddress: " + address);
                        }
                    });
                });
            })(marker, data);
            latlngbounds.extend(marker.position);
		}
</script>';
} else {
    dwt_listing_valuesz($event_id, 'dwt-events');
}


            ?>            


