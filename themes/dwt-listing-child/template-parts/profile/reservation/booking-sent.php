<?php
global $dwt_listing_options;
$profile = new dwt_listing_profile();
$my_listing_ID = get_the_ID();
$user_id = $profile->user_info->ID;
$loader = $contitional_input = $menu_listing_id = '';

?>
<div class="container-fluid">
    <?php get_template_part('template-parts/profile/author-stats/stats'); ?>

    <div class="row">
        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php echo esc_html__('Appontments Sent By Author', 'dwt-listing'); ?></h3>
                </div>
                <div class="panel-body">
                    <?php
                    $paged = 1;
                    $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
                    //fetch listings
                    $listing_status = '';
                    $query_args = $profile->dwt_listing_fetch_owner_listings($listing_status, $paged);
                  
                   
                    //fetch listings
                    $query_args = array(
                        'post_type' => 'reservation',
                        'author' => get_current_user_id(), 
                        'posts_per_page'=>10,
                         
                      );
                     
                      $the_query = new WP_Query( $query_args );
                     //print_r($the_query);
                      if ( $the_query->have_posts() ) {
   
                        $paged = 1;
                       // print_r($sid);
                        $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
                        ?>        
                                    <div class="table-responsive dwt-admin-tabelz">
                                        <table class="dwt-admin-tabelz-panel table-hover">
                                            <thead>
                                            <tr>
                                        <th><?php echo esc_html__('Client Name', 'dwt-listing'); ?></th>
                                        <th><?php echo esc_html__('Number', 'dwt-listing'); ?></th>
                                        <th><?php echo esc_html__('Email', 'dwt-listing'); ?></th>
                                        <th><?php echo esc_html__('Date', 'dwt-listing'); ?></th>
                                        <th><?php echo esc_html__('Timings', 'dwt-listing'); ?></th>
                                        <th><?php echo esc_html__('Lisitng Id', 'dwt-listing'); ?></th>
                                        <th><?php echo esc_html__('Lisitng Name', 'dwt-listing'); ?></th>
                                        <th><?php echo esc_html__('Status', 'dwt-listing'); ?></th>
                                    </tr>
                                            </thead>
                                            <?php
                                
                                    $loop  =    $the_query;
                                while ( $loop->have_posts() ) : $loop->the_post(); 
                                    $theid = get_the_ID(  );
                                   // $post_metas = get_post_meta( $theid);
               
                                $listing_title = get_the_title( );
                                $reservation_name = get_post_meta( $theid, 'dwt_listing_reserver_name', true );
                                $reservation_number = get_post_meta( $theid, 'dwt_listing_reserver_number', true );
                                $reservation_email = get_post_meta( $theid, 'dwt_listing_reserver_email', true );
                                $reservation_day = get_post_meta( $theid, 'dwt_listing_reservation_day', true );
                                $reservation_time = get_post_meta( $theid, 'dwt_listing_reservation_time', true );
                                $reservation_id = get_post_meta( $theid, 'dwt_listing_reservation_id', true );
                                $reservation_listing_title = get_post_meta( $theid, 'dwt_listing_reservation_title', true );
                                
                                $current_status = get_post_meta( $theid , 'booked_listings_reservations', true );
                                
                                
                                $current_status  =  $current_status == ""   ?  esc_html__('Pending','') : $current_status;
                                /*---------addding table for Sent Appointments--------------*/
                                ?>
                                <tbody>

                                
                                        <tr class="unique-key->">
                                        <td><?php echo $reservation_name;    ?></td>    
                                        <td><?php echo $reservation_number;  ?></td>
                                        <td><?php echo $reservation_email;   ?></td>
                                        <td><?php echo $reservation_day;     ?></td>
                                        <td><?php echo $reservation_time;    ?></td> 
                                        <td><?php echo $reservation_id;      ?></td>
                                        <td><?php echo $reservation_listing_title;      ?></td>

                                        <td><?php echo $current_status;      ?></td>
                                     
                                        </tr>
                                        <?php
                                    
                                    wp_reset_postdata();
                                    ?>

                                </tbody>
                                <?php    endwhile;

                                ?>
                                        </table>
                                    </div>
                        <?php
                    } else {
                        get_template_part('template-parts/profile/my-listings/content', 'none');
                    }
                    ?>
                    <div class="admin-pagination">
                          <?php echo dwt_listing_listing_pagination($the_query); ?>

                         
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>