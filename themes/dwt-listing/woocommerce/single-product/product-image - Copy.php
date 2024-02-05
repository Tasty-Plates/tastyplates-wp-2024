<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.1
 */
?>
<div class="dwt_listing_single-product">
    <?php
    defined('ABSPATH') || exit;
    global $product;
    $full_img = $get_img_thumb = $product_id = '';
    $product_id = get_the_ID();
    $product = wc_get_product($product_id);

    $sale_banner = '';
    if ($product->is_on_sale()) {
        $sale_banner = '<span class="prod-sale-banner">' . esc_html__('Sale', 'dwt-listing') . '</span>';
    }
    if ($product->get_image_id() != "") {
        ?>
        <div class="produt-slider owl-carousel owl-theme">
            <?php
            $fetch_images = dwt_listing_fetch_product_images($product_id);
            foreach ($fetch_images as $product_images) {
                $full_img = wp_get_attachment_image_src($product_images, 'full');
                $get_img_thumb = wp_get_attachment_image_src($product_images, 'dwt_listing_woo-single-thumb');
                ?>
                <div class="item">
                    <?php echo '' . $sale_banner; ?>
                    <a href="<?php echo esc_url($full_img[0]); ?>" data-fancybox="group"><img class="img-responsive"
                                                                                              src="<?php echo esc_url($get_img_thumb[0]); ?>"
                                                                                              alt="<?php esc_html__('dwt-listing', 'dwt-listing'); ?>"></a>
                </div>
                <?php
            }
            ?>
        </div>
        <?php
    } else {
        ?>
        <?php echo '' . $sale_banner; ?>
        <img class="img-responsive" alt="<?php esc_html__('dwt-listing', 'dwt-listing'); ?>"
             src="<?php echo esc_url(wc_placeholder_img_src()); ?>">
        <?php
    }
    ?>
</div>