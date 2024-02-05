<?php
	add_action( 'init', 'stop_heartbeat', 1 );
	function stop_heartbeat() {
	wp_deregister_script('heartbeat');
}

function cs_recent_reviews_shortcode($atts){
global $wpdb;
global $dwt_listing_options;
global $post;

$results = $wpdb->get_results("    SELECT p.*,
    (CASE WHEN c.comment_date IS NULL THEN p.`post_date` ELSE c.comment_date END) order_column
     FROM `2n0d1dm_posts` p
    LEFT  JOIN `2n0d1dm_comments` c  ON (p.ID = c.`comment_post_ID` ) WHERE p.post_type='listing' AND p.post_status='publish'
    GROUP BY p.ID
     ORDER BY order_column   DESC Limit 4");

	foreach($results as $result){
		$comment_args = array(
			'post_id' => $result->ID,
			'number' => 1,
		);
		$latest_comment = get_comments($comment_args);
		$terms = get_the_terms( $result->ID , 'l_category' );
		if ( $terms != null ){
			foreach ($terms as $term) {

				echo '<a href="'.get_term_link($term).'">'.$term->name.'</a>';

			}
		}
		$usertaste_pallate = get_field('taste_palette', 'user_' . $latest_comment[0]->user_id);
		$user_pic = trailingslashit(get_template_directory_uri()) . 'assets/images/users/defualt.jpg';
		if(esc_url($latest_comment[0]->commenter_dp)!=null){
			$user_pic = esc_url($latest_comment[0]->commenter_dp);
		}
		$rated = get_comment_meta($latest_comment[0]->comment_ID, 'review_stars', true);
		if( dwt_listing_text('dwt_listing_review_enable_stars') == '1')
		{
			$get_percentage = dwt_listing_fetch_reviews_average($result->ID);
			if(isset($get_percentage) && count((array)$get_percentage['ratings']) > 0 && count((array)$get_percentage['rated_no_of_times']) > 0)
			{
			echo '<div> <span class="ratings">'.$get_percentage['total_stars'].'<i class="rating-counter"> ('.esc_attr($get_percentage['rated_no_of_times']).esc_html__('ratings','dwt-listing').')</i> </span> </div>';
			}
		}
		echo '<div>title: '.$result->post_title.'</div>';
		echo get_post_meta($result->ID, 'dwt_listing_listing_street', true);
		echo '<div class="comments">comment author: '.$latest_comment[0]->comment_author.'</div>';
		echo '<div>commentor profile: <img style="width:50px; display:inline-flex" src="'.$user_pic.'" class="" alt="'.esc_html__('no image', 'dwt-listing').'"></div>';
		'<div class="comments">comment content: '.$latest_comment[0]->comment_content.'</div>';
		if ($usertaste_pallate) :
			echo'<div class="cs-palate">commentor Palate Group: '.implode(', ', $usertaste_pallate).'</div>';
		endif;
		if ($rated != "") {
			echo '<span class="ratings"> rating:';
			for ($i = 1; $i <= 5; $i++) {
				if ($i <= $rated) {
					echo '<i class="fa fa-star color"></i>';
				} else {
					echo '<i class="fa fa-star"></i>';
				}
			}
			echo '</span>';
		}

		$samecat_rating =[];
		$differentcat_rating =[];
		$stars =  $wpdb->get_results("SELECT * from 2n0d1dm_posts
		LEFT JOIN 2n0d1dm_comments ON 2n0d1dm_comments.comment_post_ID=2n0d1dm_posts.ID
		LEFT JOIN 2n0d1dm_commentmeta on 2n0d1dm_commentmeta.comment_id=2n0d1dm_comments.comment_ID
		LEFT JOIN 2n0d1dm_users on 2n0d1dm_users.ID=2n0d1dm_comments.user_id
		where 2n0d1dm_posts.ID=$result->ID and 2n0d1dm_commentmeta.meta_key='review_stars' ");

		foreach($stars as $star){
			$current_taste_pallete = get_user_meta( $star->user_id,'taste_palette', true );
			echo '<div>star rating:'.$current_taste_pallete[0].$star->meta_value.'</div>';


			if (in_array($current_taste_pallete[0], wp_list_pluck( $terms, 'name' ))) {
				echo 'yes';
				array_push($samecat_rating, $star->meta_value);
			}
			else{
				echo 'no';
				array_push($differentcat_rating, $star->meta_value);
			}


		};
		if(array_sum($samecat_rating)>0){
			$samecatpercentageArray = array_sum($samecat_rating)/(count($samecat_rating)*5)*100;
			echo 'samecat rating: '.$samecatpercentageArray.'%';

		}
		if($differentcat_rating!=null){
			$diffcatpercentageArray = array_sum($differentcat_rating)/(count($differentcat_rating)*5)*100;
			echo 'diffcat rating: '.$diffcatpercentageArray.'%';
		}
		echo '<div style="margin-bottom:50px"></div>';
	}

}
add_shortcode('cs_recent_reviews', 'cs_recent_reviews_shortcode');

function get_top_rated_post_of_the_day() {
    // Get the current date in WordPress format (YYYY-MM-DD).
    $current_date = current_time('Y-m-d');


	//top comment
/* SELECT p.*,
    (CASE WHEN c.comment_date IS NULL THEN p.`post_date` ELSE c.comment_date END) order_column
     FROM `2n0d1dm_posts` p
    LEFT  JOIN `2n0d1dm_comments` c  ON (p.ID = c.`comment_post_ID` ) WHERE p.post_type='listing' AND p.post_status='publish'
    GROUP BY p.ID
     ORDER BY comment_count   DESC Limit 1*/
    // Create a custom query to get the top-rated post of the day.
    $args = array(
        'post_type' => 'listing', // Adjust post type if needed.
        'posts_per_page' => 1,
        'meta_query' => array(
            array(
                'key' => 'rating', // Replace with the actual meta key for ratings.
                'compare' => 'EXISTS', // Check if the post has ratings.
            ),
        ),
        'orderby' => 'comment_count', // Order by the rating value.
        'meta_key' => 'rating', // Replace with the actual meta key for ratings.
        'order' => 'DESC',
        'date_query' => array(
            array(
                'year' => get_the_time('Y'),
                'month' => get_the_time('m'),
                'day' => get_the_time('d'),
            ),
        ),
    );

    $top_rated_query = new WP_Query($args);

    // Initialize an output variable.
    $output = '';

    // Loop through the query results.
    if ($top_rated_query->have_posts()) {
        while ($top_rated_query->have_posts()) {
            $top_rated_query->the_post();
            // Build the output with post title and content.
            $output .= '<h2>' . get_the_title() . '</h2>';
            $output .= '<div>' . get_the_content() . '</div>';
        }
    } else {
        $output .= '<p>No top-rated post found.</p>';
    }

    // Restore original post data.
    wp_reset_postdata();

    return $output;
}
add_shortcode('top_rated_post', 'get_top_rated_post_of_the_day');


function display_custom_post_listing_top_review_shortcode($atts) {
    $atts = shortcode_atts(array(
        'post_type' => 'listing', // Adjust post type if needed.
        'posts_per_page' => 1,
    ), $atts);

	$current_date = date('Y-m-d');

	$args = array(
        'post_type' => $atts['post_type'],
        'posts_per_page' => $atts['posts_per_page'],
		'orderby' => 'comment_count',
		'order' => DESC,
    );


    $query = new WP_Query($args);

    // Initialize an output variable.
    $output = '';

    // Loop through the query results.
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
			$listing_id = get_the_ID();
			 $thumb_size = 'dwt_listing_list-view1';
            $business_hours = $status_type = $location = $business_hours_status = $ratings = '';
            $reveal = 'foo';
            if ($animation == 'no') {
                $reveal = '';
            }
            //get media
            $media = dwt_listing_fetch_listing_gallery($listing_id);
            //listing category
            $categories = dwt_listing_listing_assigned_cats($listing_id, 'list1');
            //cleantitle
            $limited_title = dwt_listing_words_count(get_the_title($listing_id), dwt_listing_text('grid_title_limit'));
            $final_title = stripslashes_deep(wp_strip_all_tags(str_replace("|", " ", get_the_title($listing_id))));
            //Ratings
            if (dwt_listing_text('dwt_listing_review_enable_stars') == '1') {
                $get_percentage = dwt_listing_fetch_reviews_average($listing_id);
                if (isset($get_percentage) && count((array)$get_percentage['ratings']) > 0 && count((array)$get_percentage['rated_no_of_times']) > 0) {
                    $ratings = '<div class="ratings elegent">' . $get_percentage['total_stars'] . ' <i class="rating-counter">' . esc_attr($get_percentage['rated_no_of_times']) . '&nbsp;' . esc_html__('Reviews', 'dwt-listing') . '</i></div>';
                }
            }

            //recent review
            $comment_args = array(
			'post_id' => $listing_id,
			'number' => 1,
		    'orderby' => 'comment_date',
		    'order'   => 'DESC',
			);
			$latest_comment = get_comments($comment_args);



            //for thumbs// Get all comments for the current post
			$all_comments_args = array(
			    'post_id' => $current_post_id,
			    'status'  => 'approve', // Only approved comments
			    'number'  => -1,        // Retrieve all comments
			);

			$all_comments = get_comments($all_comments_args);

            $small_thumb = $large_img = $related_img = '';
            if (count((array)$media) > 0) {
                $count = 1;
                foreach ($media as $thumb) {
                    if ($count > 4) {
                        break;
                    }
                    //if (wp_attachment_is_image($listing_id)) {
                    if (wp_get_attachment_image_src($thumb, 'full') != false) {
                        $class = '';
                        $full_img = wp_get_attachment_image_src($thumb, 'full');
                        $imgthumb = wp_get_attachment_image_src($thumb, 'dwt_listing_listing_thumb');
                        $large_img = $full_img[0];
                        $small_thumb = $imgthumb[0];
                    } else {
                        $large_img = trailingslashit(get_template_directory_uri()) . 'assets/images/small-thumb.png';
                        $small_thumb = trailingslashit(get_template_directory_uri()) . 'assets/images/small-thumb.png';
                    }
                    $related_img .= '<li><a href="' . esc_url($large_img) . '" data-fancybox="images-preview-' . esc_attr($listing_id) . '"><img src="' . esc_attr($small_thumb) . '" class="img-responsive" alt="' . $final_title . '"></a></li>';
                    $count++;
                }
            }
            //location
            if (get_post_meta($listing_id, 'dwt_listing_listing_street', true) != "") {
                $streent_location = get_post_meta($listing_id, 'dwt_listing_listing_street', true);
                $location = '<a href="javascript:void(0)"> ' . $streent_location . ' </a>';
            }
            //Business Hours
            $business_hours_status = '';
            if (dwt_listing_business_hours_status($listing_id) != "") {
                $status_type = dwt_listing_business_hours_status($listing_id);
                if ($status_type == 0) {
                    $business_hours_status .= '<a class="closed">' . esc_html__('Closed', 'dwt-listing') . '</a>';
                } else if ($status_type == 2) {
                    $business_hours_status .= '<a class="open24">' . esc_html__('Always Open', 'dwt-listing') . '</a>';
                } else {
                    $business_hours_status .= '<a class="open-now">' . esc_html__('Open Now', 'dwt-listing') . '</a>';
                }
            }
            //coupon
            $coupon_tag = '';
            if (dwt_listing_check_coupon_expiry($listing_id) == '1') {
                if (get_post_meta($listing_id, 'dwt_listing_coupon_title', true) != "") {
                    $discount = get_post_meta($listing_id, 'dwt_listing_coupon_title', true);
                    $coupon_tag = '<span class="coupon-sale">' . $discount . '</span>';
                }
            }


			$usertaste_pallate = get_field('taste_palette', 'user_' . $latest_comment[0]->user_id);
			$usertaste_pallate_group = "";
			 if ($usertaste_pallate) :
				$usertaste_pallate_group = implode(', ', $usertaste_pallate);
			endif;

            $user_pic = trailingslashit(get_template_directory_uri()) . 'assets/images/users/defualt.jpg';
			if(esc_url($latest_comment[0]->commenter_dp)!=null){
				$user_pic = esc_url($latest_comment[0]->commenter_dp);
			}

            $output .= '<div class="ads-list-archive masonery_item">


				<div class="' . esc_attr($reveal) . '">
						  <div class="col-md-3 col-sm-5 col-xs-12  nopadding">
							 <div class="ad-archive-img">
									<a href="' . get_the_permalink($listing_id) . '">
										<img class="img-responsive" src="' . dwt_listing_return_listing_idz($media, $thumb_size) . '" alt="' . $final_title . '">
									</a>
									 ' . $business_hours_status . '
									 ' . $coupon_tag . '
							 </div>
						  </div>
						  <div class="col-md-9 col-sm-7 col-xs-12">
							 <div class="ad-archive-desc">
								<h3><a href="' . get_the_permalink($listing_id) . '"> ' . $final_title . ' </a>' . dwt_listing_is_listing_featured($listing_id) . '
								   <div class="last-updated">
										' . $ratings . '
								   </div>
							   </h3>
								<div class="category-title">
									<span>
									<span class="cs-cat">
										' . $categories . '
										</span>
										<span class="cs-loc">
										' . $location . '
										</span>
									</span>
								</div>
								<div class="clearfix  visible-xs-block"></div>
								<div class="cs-reviewtitle">Recent Review:</div>
								<div class="cs-commenterprofile">
									<img style="display:inline-flex" src="'.$user_pic.'" class="" alt="'.esc_html__('no image', 'dwt-listing').'">
									<div>
										<div class="cs-commenter">
											<div class="cs-commentername">'.$latest_comment[0]->comment_author.'</div>
											<div class="cs-palate">'.$usertaste_pallate_group.'</div>
										</div>
											'.esc_html(wp_trim_words(esc_html($latest_comment[0]->comment_content), 50)).'
											<a class="cs-seemore" href="' . get_the_permalink($listing_id) . '"> see more</a>
									</div>
								</div>
							 </div>
						  </div>
						  </div>
					   </div>';
        }
    } else {
        $output .= '<p>No items found.</p>';
    }


    // Restore original post data.
    wp_reset_postdata();
    return $output;
}
add_shortcode('custom_post_listing_with_top_review', 'display_custom_post_listing_top_review_shortcode');



function display_custom_post_listing_with__recent_reviews_shortcode($atts) {
    $atts = shortcode_atts(array(
        'post_type' => 'listing', // Adjust post type if needed.
        'posts_per_page' => 1,
    ), $atts);


    $page = $_POST['page'];

    $args = array(
        'post_type' => $atts['post_type'],
        'posts_per_page' => $atts['posts_per_page'],
		'orderby' => 'comment_count',
		'order' => DESC,    // Adjust the number of posts to load
        'paged'          => $page,
	);

    $query = new WP_Query($args);

    // Initialize an output variable.
    $output = '<div class="flex cs-recentreviews-container" id="cs-recentreviews-container">';

    // Loop through the query results.
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
			$listing_id = get_the_ID();
			 $thumb_size = 'dwt_listing_list-view1';
            $business_hours = $status_type = $location = $business_hours_status = $ratings = '';
            $reveal = 'foo';
            if ($animation == 'no') {
                $reveal = '';
            }
            //get media
            $media = dwt_listing_fetch_listing_gallery($listing_id);
            //listing category
            $categories = dwt_listing_listing_assigned_cats($listing_id, 'list1');
            //cleantitle
            $limited_title = dwt_listing_words_count(get_the_title($listing_id), dwt_listing_text('grid_title_limit'));
            $final_title = stripslashes_deep(wp_strip_all_tags(str_replace("|", " ", $limited_title)));
            //Ratings
            if (dwt_listing_text('dwt_listing_review_enable_stars') == '1') {
                $get_percentage = dwt_listing_fetch_reviews_average($listing_id);
                if (isset($get_percentage) && count((array)$get_percentage['ratings']) > 0 && count((array)$get_percentage['rated_no_of_times']) > 0) {
                    $ratings = '<div class="ratings elegent">' . $get_percentage['total_stars'] . ' <i class="rating-counter">' . esc_attr($get_percentage['rated_no_of_times']) . '&nbsp;' . esc_html__('Reviews', 'dwt-listing') . '</i></div>';
                }
            }

            //recent review
            $comment_args = array(
			'post_id' => $listing_id,
			'number' => 1,
		    'orderby' => 'comment_date',
		    'order'   => 'DESC',
			);
			$latest_comment = get_comments($comment_args);

            //for thumbs
            $small_thumb = $large_img = $related_img = '';
            if (count((array)$media) > 0) {
                $count = 1;
                foreach ($media as $thumb) {
                    if ($count > 4) {
                        break;
                    }
                    //if (wp_attachment_is_image($listing_id)) {
                    if (wp_get_attachment_image_src($thumb, 'full') != false) {
                        $class = '';
                        $full_img = wp_get_attachment_image_src($thumb, 'full');
                        $imgthumb = wp_get_attachment_image_src($thumb, 'dwt_listing_listing_thumb');
                        $large_img = $full_img[0];
                        $small_thumb = $imgthumb[0];
                    } else {
                        $large_img = trailingslashit(get_template_directory_uri()) . 'assets/images/small-thumb.png';
                        $small_thumb = trailingslashit(get_template_directory_uri()) . 'assets/images/small-thumb.png';
                    }
                    $related_img .= '<li><a href="' . esc_url($large_img) . '" data-fancybox="images-preview-' . esc_attr($listing_id) . '"><img src="' . esc_attr($small_thumb) . '" class="img-responsive" alt="' . $final_title . '"></a></li>';
                    $count++;
                }
            }
            //location
            if (get_post_meta($listing_id, 'dwt_listing_listing_street', true) != "") {
                $streent_location = get_post_meta($listing_id, 'dwt_listing_listing_street', true);
                $location = '<a href="javascript:void(0)"> ' . $streent_location . ' </a>';
            }
            //Business Hours
            $business_hours_status = '';
            if (dwt_listing_business_hours_status($listing_id) != "") {
                $status_type = dwt_listing_business_hours_status($listing_id);
                if ($status_type == 0) {
                    $business_hours_status .= '<a class="closed">' . esc_html__('Closed', 'dwt-listing') . '</a>';
                } else if ($status_type == 2) {
                    $business_hours_status .= '<a class="open24">' . esc_html__('Always Open', 'dwt-listing') . '</a>';
                } else {
                    $business_hours_status .= '<a class="open-now">' . esc_html__('Open Now', 'dwt-listing') . '</a>';
                }
            }
            //coupon
            $coupon_tag = '';
            if (dwt_listing_check_coupon_expiry($listing_id) == '1') {
                if (get_post_meta($listing_id, 'dwt_listing_coupon_title', true) != "") {
                    $discount = get_post_meta($listing_id, 'dwt_listing_coupon_title', true);
                    $coupon_tag = '<span class="coupon-sale">' . $discount . '</span>';
                }
            }

			$usertaste_pallate = get_field('taste_palette', 'user_' . $latest_comment[0]->user_id);
			$usertaste_pallate_group = "";
			 if ($usertaste_pallate) :
				$usertaste_pallate_group = implode(', ', $usertaste_pallate);
			endif;

            $user_pic = trailingslashit(get_template_directory_uri()) . 'assets/images/users/defualt.jpg';
			if(esc_url($latest_comment[0]->commenter_dp)!=null){
				$user_pic = esc_url($latest_comment[0]->commenter_dp);
			}


            $output .= '<div class="ads-list-archive masonery_item">
				<div class="' . esc_attr($reveal) . '">
						  <div class="">
							 <div class="ad-archive-img cs-recentreviewimgcontainer">
									<a href="' . get_the_permalink($listing_id) . '">
										<img class="img-responsive" src="' . dwt_listing_return_listing_idz($media, $thumb_size) . '" alt="' . $final_title . '">
									</a>
									 ' . $business_hours_status . '
									 ' . $coupon_tag . '
							 </div>
						  </div>
						  <div class="col-md-12">
							 <div class="ad-archive-desc">
								<h3><a href="' . get_the_permalink($listing_id) . '"> ' . $final_title . ' </a>' . dwt_listing_is_listing_featured($listing_id) . '
								   <div class="last-updated">
										' . $ratings . '
								   </div>
							   </h3>
								<div class="category-title">
									<span>
									<span class="cs-cat">
										' . $categories . '
										</span>
										<span class="cs-loc">
										' . $location . '
										</span>
									</span>
								</div>
								<div class="clearfix  visible-xs-block"></div>
								<div class="cs-reviewtitle cs-recentreviewtitle">Recent Review:</div>
								<div class="cs-commenterprofile">
									<img style="display:inline-flex" src="'.$user_pic.'" class="" alt="'.esc_html__('no image', 'dwt-listing').'">
									<div>
										<div class="cs-commenter">
											<div class="cs-commentername">'.$latest_comment[0]->comment_author.'</div>
											<div class="cs-palate">'.$usertaste_pallate_group.'</div>
										</div>
											'.esc_html(wp_trim_words(esc_html($latest_comment[0]->comment_content), 20)).'
									</div>
								</div>
							 </div>
						  </div>
						  </div>
					   </div>';
        }
    } else {
        $output .= '<p>No items found.</p>';
    }

    // Restore original post data.
    wp_reset_postdata();
	$output.= '</div>';
    $output.='<div class="text-center">';
    $output.='<div class="cs-button" id="viewmorepost"> <button>View More Recent Reviews</button></div>';
	$output.= '</div>';
    return $output;
}
add_shortcode('custom_post_listing_with_recent_reviews', 'display_custom_post_listing_with__recent_reviews_shortcode');


function load_more_posts(){
    $page = $_POST['page'];

    $current_date = date('Y-m-d');

    $args = array(
        'post_type'      => 'listing', // Adjust to your post type
        'posts_per_page' => 3,      // Adjust the number of posts to load
        'paged'          => $page,
    	'orderby' => 'comment_count',
		'order' => DESC,
    );

    $query = new WP_Query($args);

    if($query->have_posts()) :
        while($query->have_posts()): $query->the_post();
            // Display your post content here
           $listing_id = get_the_ID();
			 $thumb_size = 'dwt_listing_list-view1';
            $business_hours = $status_type = $location = $business_hours_status = $ratings = '';
            $reveal = 'foo';
            if ($animation == 'no') {
                $reveal = '';
            }
            //get media
            $media = dwt_listing_fetch_listing_gallery($listing_id);
            //listing category
            $categories = dwt_listing_listing_assigned_cats($listing_id, 'list1');
            //cleantitle
            $limited_title = dwt_listing_words_count(get_the_title($listing_id), dwt_listing_text('grid_title_limit'));
            $final_title = stripslashes_deep(wp_strip_all_tags(str_replace("|", " ", $limited_title)));
            //Ratings
            if (dwt_listing_text('dwt_listing_review_enable_stars') == '1') {
                $get_percentage = dwt_listing_fetch_reviews_average($listing_id);
                if (isset($get_percentage) && count((array)$get_percentage['ratings']) > 0 && count((array)$get_percentage['rated_no_of_times']) > 0) {
                    $ratings = '<div class="ratings elegent">' . $get_percentage['total_stars'] . ' <i class="rating-counter">' . esc_attr($get_percentage['rated_no_of_times']) . '&nbsp;' . esc_html__('Reviews', 'dwt-listing') . '</i></div>';
                }
            }

            //recent review
            $comment_args = array(
			'post_id' => $listing_id,
			'number' => 1,
		    'orderby' => 'comment_date',
		    'order'   => 'DESC',
			);
			$latest_comment = get_comments($comment_args);

            //for thumbs
            $small_thumb = $large_img = $related_img = '';
            if (count((array)$media) > 0) {
                $count = 1;
                foreach ($media as $thumb) {
                    if ($count > 4) {
                        break;
                    }
                    //if (wp_attachment_is_image($listing_id)) {
                    if (wp_get_attachment_image_src($thumb, 'full') != false) {
                        $class = '';
                        $full_img = wp_get_attachment_image_src($thumb, 'full');
                        $imgthumb = wp_get_attachment_image_src($thumb, 'dwt_listing_listing_thumb');
                        $large_img = $full_img[0];
                        $small_thumb = $imgthumb[0];
                    } else {
                        $large_img = trailingslashit(get_template_directory_uri()) . 'assets/images/small-thumb.png';
                        $small_thumb = trailingslashit(get_template_directory_uri()) . 'assets/images/small-thumb.png';
                    }
                    $related_img .= '<li><a href="' . esc_url($large_img) . '" data-fancybox="images-preview-' . esc_attr($listing_id) . '"><img src="' . esc_attr($small_thumb) . '" class="img-responsive" alt="' . $final_title . '"></a></li>';
                    $count++;
                }
            }
            //location
            if (get_post_meta($listing_id, 'dwt_listing_listing_street', true) != "") {
                $streent_location = get_post_meta($listing_id, 'dwt_listing_listing_street', true);
                $location = '<a href="javascript:void(0)"> ' . $streent_location . ' </a>';
            }
            //Business Hours
            $business_hours_status = '';
            if (dwt_listing_business_hours_status($listing_id) != "") {
                $status_type = dwt_listing_business_hours_status($listing_id);
                if ($status_type == 0) {
                    $business_hours_status .= '<a class="closed">' . esc_html__('Closed', 'dwt-listing') . '</a>';
                } else if ($status_type == 2) {
                    $business_hours_status .= '<a class="open24">' . esc_html__('Always Open', 'dwt-listing') . '</a>';
                } else {
                    $business_hours_status .= '<a class="open-now">' . esc_html__('Open Now', 'dwt-listing') . '</a>';
                }
            }
            //coupon
            $coupon_tag = '';
            if (dwt_listing_check_coupon_expiry($listing_id) == '1') {
                if (get_post_meta($listing_id, 'dwt_listing_coupon_title', true) != "") {
                    $discount = get_post_meta($listing_id, 'dwt_listing_coupon_title', true);
                    $coupon_tag = '<span class="coupon-sale">' . $discount . '</span>';
                }
            }

			$usertaste_pallate = get_field('taste_palette', 'user_' . $latest_comment[0]->user_id);
			$usertaste_pallate_group = "";
			 if ($usertaste_pallate) :
				$usertaste_pallate_group = implode(', ', $usertaste_pallate);
			endif;

            $user_pic = trailingslashit(get_template_directory_uri()) . 'assets/images/users/defualt.jpg';
			if(esc_url($latest_comment[0]->commenter_dp)!=null){
				$user_pic = esc_url($latest_comment[0]->commenter_dp);
			}
			echo '

            <div class="ads-list-archive masonery_item">
				<div class="' . esc_attr($reveal) . '">
						  <div class="">
							 <div class="ad-archive-img cs-recentreviewimgcontainer">
									<a href="' . get_the_permalink($listing_id) . '">
										<img class="img-responsive" src="' . dwt_listing_return_listing_idz($media, $thumb_size) . '" alt="' . $final_title . '">
									</a>
									 ' . $business_hours_status . '
									 ' . $coupon_tag . '
							 </div>
						  </div>
						  <div class="col-md-12">
							 <div class="ad-archive-desc">
								<h3><a href="' . get_the_permalink($listing_id) . '"> ' . $final_title . ' </a>' . dwt_listing_is_listing_featured($listing_id) . '
								   <div class="last-updated">
										' . $ratings . '
								   </div>
							   </h3>
								<div class="category-title">
									<span>
									<span class="cs-cat">
										' . $categories . '
										</span>
										<span class="cs-loc">
										' . $location . '
										</span>
									</span>
								</div>
								<div class="clearfix  visible-xs-block"></div>
								<div class="cs-reviewtitle cs-recentreviewtitle">Recent Review:</div>
								<div class="cs-commenterprofile">
									<img style="display:inline-flex" src="'.$user_pic.'" class="" alt="'.esc_html__('no image', 'dwt-listing').'">
									<div>
										<div class="cs-commenter">
											<div class="cs-commentername">'.$latest_comment[0]->comment_author.'</div>
											<div class="cs-palate">'.$usertaste_pallate_group.'</div>
										</div>
											'.esc_html(wp_trim_words(esc_html($latest_comment[0]->comment_content), 20)).'
									</div>
								</div>
							 </div>
						  </div>
						  </div>
					   </div>';

        endwhile;
    endif;

    wp_reset_postdata();

    die();
}

add_action('wp_ajax_load_more_posts', 'load_more_posts');
add_action('wp_ajax_nopriv_load_more_posts', 'load_more_posts');


function display_custom_post_editors_choice_shortcode($atts) {
    $atts = shortcode_atts(array(
        'location' => 'Grande Prairie',
    ), $atts);

    // Initialize an output variable.
    $output = '<div class="flex cs-editorschoice">';

    $output .= '<a class="flex-0 cs-editorschoicebox" href="/tastyplates/5-best-restaurants-in-grande-prairie/">
	    			<img src="/tastyplates/wp-content/uploads/2018/02/Rectangle-41.png">
	    			<h4>5 Best Restaurants in '.$atts['location'].'</h4>
	    			<span>Go-to food place in '.$atts['location'].'?</span>
    			</a>';

	$output .= '<div class="flex-0 cs-editorschoicebox">
		<img src="/tastyplates/wp-content/uploads/2018/02/Rectangle-41-1.png">
		<h4>Top 10 Dessert House in '.$atts['location'].'</h4>
		<span>Go-to food place in '.$atts['location'].'?</span>
	</div>';

	$output .= '<div class="flex-0 cs-editorschoicebox">
		<img src="/tastyplates/wp-content/uploads/2018/02/Rectangle-41-2.png">
		<h4>5 Best Breakfast in '.$atts['location'].'</h4>
		<span>Go-to food place in '.$atts['location'].'?</span>
	</div>';

	$output .= '<div class="flex-0 cs-editorschoicebox">
		<img src="/tastyplates/wp-content/uploads/2018/02/Rectangle-41-3.png">
		<h4>Top 10 Dessert House in '.$atts['location'].'</h4>
		<span>Go-to food place in '.$atts['location'].'?</span>
	</div>';

	$output .= '<div class="flex-0 cs-editorschoicebox">
		<img src="/tastyplates/wp-content/uploads/2018/02/Rectangle-41-4.png">
		<h4>Top 5 Mexican Restaurants in '.$atts['location'].'</h4>
		<span>Go-to food place in '.$atts['location'].'?</span>
	</div>';

	$output .= '<div class="flex-0 cs-editorschoicebox">
		<img src="/tastyplates/wp-content/uploads/2018/02/Rectangle-41-5.png">
		<h4>Salad Houses in '.$atts['location'].'</h4>
		<span>Go-to food place in '.$atts['location'].'?</span>
	</div>';

	$output .= '<div class="flex-0 cs-editorschoicebox">
		<img src="/tastyplates/wp-content/uploads/2018/02/Rectangle-41-10.png">
		<h4>Top 5 Western Resto in '.$atts['location'].'</h4>
		<span>Go-to food place in '.$atts['location'].'?</span>
	</div>';


	$output .= '<div class="flex-0 cs-editorschoicebox">
		<img src="/tastyplates/wp-content/uploads/2018/02/Rectangle-41-8.png">
		<h4>Top Vietnamese in '.$atts['location'].'</h4>
		<span>Go-to food place in '.$atts['location'].'?</span>
	</div>';

	$output .= '<div class="flex-0 cs-editorschoicebox">
		<img src="/tastyplates/wp-content/uploads/2018/02/Rectangle-41-9.png">
		<h4>Japanese Restaurants in '.$atts['location'].'</h4>
		<span>Go-to food place in '.$atts['location'].'?</span>
	</div>';
	$output.= '</div>';
    return $output;
}
add_shortcode('custom_post_editors_choice', 'display_custom_post_editors_choice_shortcode');





function display_custom_post_listing_top_by_location_shortcode($atts) {
    $atts = shortcode_atts(array(
        'post_type' => 'listing', // Adjust post type if needed.
        'posts_per_page' => 1,
        'location' => 'Grande Prairie',
    ), $atts);

	$current_date = date('Y-m-d');

	$args = array(
	    'post_type'      => $atts['post_type'],
	    'posts_per_page' => $atts['posts_per_page'],
	    'meta_query'     => array(
	        array(
	            'key'     => 'dwt_listing_listing_street', // Replace with the actual custom meta key
	            'value'   => $atts['location'],
	            'compare' => '='
	        ),
	    ),
	    'orderby'        => 'comment_count',
	    'order'          => 'DESC', // Make sure 'DESC' is a string
	);

    $query = new WP_Query($args);

    // Initialize an output variable.
    $output = '';

    // Loop through the query results.
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
			$listing_id = get_the_ID();
			 $thumb_size = 'dwt_listing_list-view1';
            $business_hours = $status_type = $location = $business_hours_status = $ratings = '';
            $reveal = 'foo';
            if ($animation == 'no') {
                $reveal = '';
            }
            //get media
            $media = dwt_listing_fetch_listing_gallery($listing_id);
            //listing category
            $categories = dwt_listing_listing_assigned_cats($listing_id, 'list1');
            //cleantitle
            $limited_title = dwt_listing_words_count(get_the_title($listing_id), dwt_listing_text('grid_title_limit'));
            $final_title = stripslashes_deep(wp_strip_all_tags(str_replace("|", " ", get_the_title($listing_id))));
            //Ratings
            if (dwt_listing_text('dwt_listing_review_enable_stars') == '1') {
                $get_percentage = dwt_listing_fetch_reviews_average($listing_id);
                if (isset($get_percentage) && count((array)$get_percentage['ratings']) > 0 && count((array)$get_percentage['rated_no_of_times']) > 0) {
                    $ratings = '<div class="ratings elegent">' . $get_percentage['total_stars'] . ' <i class="rating-counter">' . esc_attr($get_percentage['rated_no_of_times']) . '&nbsp;' . esc_html__('Reviews', 'dwt-listing') . '</i></div>';
                }
            }

            //recent review
            $comment_args = array(
			'post_id' => $listing_id,
			'number' => 1,
		    'orderby' => 'comment_date',
		    'order'   => 'DESC',
			);
			$latest_comment = get_comments($comment_args);



            //for thumbs// Get all comments for the current post
			$all_comments_args = array(
			    'post_id' => $current_post_id,
			    'status'  => 'approve', // Only approved comments
			    'number'  => -1,        // Retrieve all comments
			);

			$all_comments = get_comments($all_comments_args);

            $small_thumb = $large_img = $related_img = '';
            if (count((array)$media) > 0) {
                $count = 1;
                foreach ($media as $thumb) {
                    if ($count > 4) {
                        break;
                    }
                    //if (wp_attachment_is_image($listing_id)) {
                    if (wp_get_attachment_image_src($thumb, 'full') != false) {
                        $class = '';
                        $full_img = wp_get_attachment_image_src($thumb, 'full');
                        $imgthumb = wp_get_attachment_image_src($thumb, 'dwt_listing_listing_thumb');
                        $large_img = $full_img[0];
                        $small_thumb = $imgthumb[0];
                    } else {
                        $large_img = trailingslashit(get_template_directory_uri()) . 'assets/images/small-thumb.png';
                        $small_thumb = trailingslashit(get_template_directory_uri()) . 'assets/images/small-thumb.png';
                    }
                    $related_img .= '<li><a href="' . esc_url($large_img) . '" data-fancybox="images-preview-' . esc_attr($listing_id) . '"><img src="' . esc_attr($small_thumb) . '" class="img-responsive" alt="' . $final_title . '"></a></li>';
                    $count++;
                }
            }
            //location
            if (get_post_meta($listing_id, 'dwt_listing_listing_street', true) != "") {
                $streent_location = get_post_meta($listing_id, 'dwt_listing_listing_street', true);
                $location = '<a href="javascript:void(0)"> ' . $streent_location . ' </a>';
            }
            //Business Hours
            $business_hours_status = '';
            if (dwt_listing_business_hours_status($listing_id) != "") {
                $status_type = dwt_listing_business_hours_status($listing_id);
                if ($status_type == 0) {
                    $business_hours_status .= '<a class="closed">' . esc_html__('Closed', 'dwt-listing') . '</a>';
                } else if ($status_type == 2) {
                    $business_hours_status .= '<a class="open24">' . esc_html__('Always Open', 'dwt-listing') . '</a>';
                } else {
                    $business_hours_status .= '<a class="open-now">' . esc_html__('Open Now', 'dwt-listing') . '</a>';
                }
            }
            //coupon
            $coupon_tag = '';
            if (dwt_listing_check_coupon_expiry($listing_id) == '1') {
                if (get_post_meta($listing_id, 'dwt_listing_coupon_title', true) != "") {
                    $discount = get_post_meta($listing_id, 'dwt_listing_coupon_title', true);
                    $coupon_tag = '<span class="coupon-sale">' . $discount . '</span>';
                }
            }


			$usertaste_pallate = get_field('taste_palette', 'user_' . $latest_comment[0]->user_id);
			$usertaste_pallate_group = "";
			 if ($usertaste_pallate) :
				$usertaste_pallate_group = implode(', ', $usertaste_pallate);
			endif;

            $user_pic = trailingslashit(get_template_directory_uri()) . 'assets/images/users/defualt.jpg';
			if(esc_url($latest_comment[0]->commenter_dp)!=null){
				$user_pic = esc_url($latest_comment[0]->commenter_dp);
			}

            $output .= '<div class="ads-list-archive masonery_item">


				<div class="' . esc_attr($reveal) . '">
						  <div class="col-md-3 col-sm-5 col-xs-12  nopadding">
							 <div class="ad-archive-img">
									<a href="' . get_the_permalink($listing_id) . '">
										<img class="img-responsive" src="' . dwt_listing_return_listing_idz($media, $thumb_size) . '" alt="' . $final_title . '">
									</a>
									 ' . $business_hours_status . '
									 ' . $coupon_tag . '
							 </div>
						  </div>
						  <div class="col-md-9 col-sm-7 col-xs-12">
							 <div class="ad-archive-desc">
								<h3><a href="' . get_the_permalink($listing_id) . '"> ' . $final_title . ' </a>' . dwt_listing_is_listing_featured($listing_id) . '
								   <div class="last-updated">
										' . $ratings . '
								   </div>
							   </h3>
								<div class="category-title">
									<span>
									<span class="cs-cat">
										' . $categories . '
										</span>
										<span class="cs-loc">
										' . $location . '
										</span>
									</span>
								</div>
								<div class="clearfix  visible-xs-block"></div>
								<div class="cs-reviewtitle">Recent Review:</div>
								<div class="cs-commenterprofile">
									<img style="display:inline-flex" src="'.$user_pic.'" class="" alt="'.esc_html__('no image', 'dwt-listing').'">
									<div>
										<div class="cs-commenter">
											<div class="cs-commentername">'.$latest_comment[0]->comment_author.'</div>
											<div class="cs-palate">'.$usertaste_pallate_group.'</div>
										</div>
											'.esc_html(wp_trim_words(esc_html($latest_comment[0]->comment_content), 50)).'
											<a class="cs-seemore" href="' . get_the_permalink($listing_id) . '"> see more</a>
									</div>
								</div>
							 </div>
						  </div>
						  </div>
					   </div>';
        }
    } else {
        $output .= '<p>No items found.</p>';
    }


    // Restore original post data.
    wp_reset_postdata();
    return $output;
}
add_shortcode('custom_post_listing_with_top_by_location', 'display_custom_post_listing_top_by_location_shortcode');



function custom_breadcrumb() {
    // Define the home URL
    $home_url = home_url();

    // Output the home link
    echo '<a href="' . esc_url($home_url) . '">Home</a>';

    // Check if it's a singular post (post, page, custom post type)
    if (is_singular()) {
        $post_type = get_post_type();
        $post_type_object = get_post_type_object($post_type);

        // Output the post type archive link
        if ($post_type_object->has_archive) {
            $post_type_archive_url = get_post_type_archive_link($post_type);
            echo ' &raquo; <a href="' . esc_url($post_type_archive_url) . '">' . esc_html($post_type_object->labels->name) . '</a>';
        }

        // Output the parent pages
        $parents = get_post_ancestors(get_the_ID());
        $parents = array_reverse($parents);

        foreach ($parents as $parent) {
            $parent_title = get_the_title($parent);
            $parent_url = get_permalink($parent);

            echo ' &raquo; <a href="' . esc_url($parent_url) . '">' . esc_html($parent_title) . '</a>';
        }

        // Output the post title
        echo ' <span>&raquo; </span>' . get_the_title();

    } elseif (is_category() || is_tag() || is_tax()) {
        // Output the category, tag, or taxonomy title
        echo ' &raquo; ' . single_term_title('', false);

    } elseif (is_author()) {
        // Output the author name
        echo ' &raquo; ' . get_the_author();

    } elseif (is_search()) {
        // Output the search results text
        echo ' &raquo; Search Results for "' . esc_html(get_search_query()) . '"';

    } elseif (is_404()) {
        // Output the 404 page text
        echo ' &raquo; 404 Not Found';
    }

    // Additional logic can be added for custom post types, custom taxonomies, etc.

    // Flush the output buffer
    ob_flush();
}