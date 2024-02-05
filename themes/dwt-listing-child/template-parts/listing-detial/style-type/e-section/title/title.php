<?php global $dwt_listing_options;
if (isset($_GET['review_id']) && $_GET['review_id'] != "") {
  $listing_id = $_GET['review_id'];
} else {
  $listing_id  =  get_the_ID();
}
$get_custom_fields = $wpdb->get_results("SELECT meta_value , meta_key FROM $wpdb->postmeta WHERE post_id = '$listing_id' AND meta_key LIKE 'field_multi_%' ORDER BY meta_id ASC");


?>
<section class="single-page-title-bar single-post">
  <div class="container">
    <div class="row">
      <div class="col-lg-10 col-xs-12 col-md-10 col-sm-12">

        <div class="short-detail">







          <div class="list-heading">
            <h2><?php echo get_the_title($listing_id); ?> <?php echo dwt_listing_is_listing_featured($listing_id); ?></h2>
          </div>



          <div class="list-category cs-primary-category">
            <ul>
              <li> <?php echo dwt_listing_listing_assigned_cats($listing_id); ?> </li>
            </ul>
          </div>


          <?php if (count($get_custom_fields) > 0) { ?>
          <div class="cs-other-category" id="dcustom-fields">

            <?php
            foreach ($get_custom_fields as $value) {
              $get_exploded_idz = explode('_', $value->meta_key);
              $get_single_id = $get_exploded_idz[3];
              $get_data = explode('|', $value->meta_value);

              foreach ($get_data as $data) {
              ?>
                <span class="cs-other-single-cat"> <?php echo esc_attr($data); ?></span>
              <?php
              }
              ?>
            <?php
            }
            ?>
          </div>
        <?php }  ?>



          <?php if(get_post_meta($listing_id, 'dwt_listing_listing_street', true) !=""){?>
              <p class="street-adr"><i class="ti-location-pin"></i> <?php echo get_post_meta($listing_id, 'dwt_listing_listing_street', true); ?></p>
             <?php } ?>


          <div class="list-meta">
            <?php get_template_part('template-parts/listing-detial/style-type/e-section/listing-meta/listing', 'meta'); ?>
          </div>


        </div>
      </div>
      <div class="col-lg-2 col-xs-12 col-md-2 col-sm-12">
        <div class="single-page-buttons-section">
          <ul class="list-inline d_action_btnz">
            <li class="d-l-favorite">
              <a class="tool-tip sonu-button-<?php echo esc_attr($listing_id); ?> bookmark-listing" title="<?php echo esc_html__('Favourite', 'dwt-listing'); ?>" data-loading-text="<i class='fa fa-spinner fa-spin '></i>" href="javascript:void(0)" data-listing-id="<?php echo esc_attr($listing_id); ?>">
                <i class="fa fa-heart"></i>
              </a>
            </li>
            <?php if (isset($dwt_listing_options['enable_report_option']) && $dwt_listing_options['enable_report_option'] == true) {
            ?>
              <li class="d-l-report">
                <a class="tool-tip" title="<?php echo esc_html__('Report this listing', 'dwt-listing'); ?>" href="javascript:void(0)" data-target=".report-quote" data-toggle="modal"><i class="ti-alert"></i></a>
              </li>
            <?php } ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>