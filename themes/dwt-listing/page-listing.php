<?php
/* Template Name: Ad Listing Page */
/**
 * The template for displaying Pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package dwt listing
 */
?>
<?php
get_header();
dwt_listing_check_pkg();

?>
<section class="submit-listing">
    <div class="container">
        <div class="row">
            <?php get_template_part('template-parts/submit-listing/submit'); ?>
            <?php
if(isset ($dwt_listing_options['sell-for-me']) && $dwt_listing_options['sell-for-me'] == 1){

?>
<a href=" <?php echo get_the_permalink($dwt_listing_options['dwt_listing_sell_it_for_me']); ?>" target="_blank" class="sell_for_me" id="sell-for-me"><?php echo esc_html__("Sell For Me", 'dwt-listing'); ?> </a>
<?php }


?>
        </div>
    </div>
</section>
<?php get_footer(); ?>