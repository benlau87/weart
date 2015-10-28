<div class="waa-dashboard-wrap">
    <?php waa_get_template( 'dashboard-nav.php', array( 'active_menu' => 'product' ) ); ?>

    <div class="waa-dashboard-content waa-product-listing">

        <?php do_action( 'waa_before_listing_product' ); ?>

            <article class="waa-product-listing-area">
                <div class="product-listing-top waa-clearfix">
                    <?php waa_product_listing_status_filter(); ?>

                    <span class="waa-add-product-link">
                        <a href="<?php echo waa_get_navigation_url( 'new-product' ); ?>" class="waa-btn waa-btn-theme waa-right"><i class="fa fa-briefcase">&nbsp;</i> <?php _e( 'Add new product', 'waa' ); ?></a>
                    </span>
                </div>

                <?php waa_product_dashboard_errors(); ?>

                <div class="waa-w12">
                    <?php waa_product_listing_filter(); ?>
                </div>

                <table class="table table-striped product-listing-table">
                    <thead>
                        <tr>
                            <th><?php _e( 'Image', 'waa' ); ?></th>
                            <th><?php _e( 'Name', 'waa' ); ?></th>
                            <th><?php _e( 'Status', 'waa' ); ?></th>
                            <th><?php _e( 'Stock', 'waa' ); ?></th>
                            <th><?php _e( 'Price', 'waa' ); ?></th>
                            <th><?php _e( 'Views', 'waa' ); ?></th>
                            <th><?php _e( 'Date', 'waa' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $pagenum      = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;

                        $post_statuses = array('publish', 'draft', 'pending');
                        $args = array(
                            'post_type'      => 'product',
                            'post_status'    => $post_statuses,
                            'posts_per_page' => 10,
                            'author'         => get_current_user_id(),
                            'orderby'        => 'post_date',
                            'order'          => 'DESC',
                            'paged'          => $pagenum
                        );

                        if ( isset( $_GET['post_status']) && in_array( $_GET['post_status'], $post_statuses ) ) {
                            $args['post_status'] = $_GET['post_status'];
                        }

                        if( isset( $_GET['date'] ) && $_GET['date'] != 0 ) {
                            $args['m'] = $_GET['date'];
                        }

                        if( isset( $_GET['product_cat'] ) && $_GET['product_cat'] != -1 ) {
                            $args['tax_query']= array(
                                array(
                                    'taxonomy' => 'product_cat',
                                    'field' => 'id',
                                    'terms' => (int)  $_GET['product_cat'],
                                    'include_children' => false,
                                )
                            );
                        }

                        if ( isset( $_GET['product_search_name']) && !empty( $_GET['product_search_name'] ) ) {
                            $args['s'] = $_GET['product_search_name'];
                        }


                        $original_post = $post;
                        $product_query = new WP_Query( apply_filters( 'waa_product_listing_query', $args ) );
                        
                        if ( $product_query->have_posts() ) {
                            while ($product_query->have_posts()) {
                                $product_query->the_post();

                                $tr_class = ($post->post_status == 'pending' ) ? ' class="danger"' : '';
                                $product = get_product( $post->ID );
                                ?>
                                <tr<?php echo $tr_class; ?>>
                                    <td>
                                        <a href="<?php echo waa_edit_product_url( $post->ID ); ?>"><?php echo $product->get_image(); ?></a>
                                    </td>
                                    <td>
                                        <p><a href="<?php echo waa_edit_product_url( $post->ID ); ?>"><?php echo $product->get_title(); ?></a></p>

                                        <div class="row-actions">
                                            <span class="edit"><a href="<?php echo waa_edit_product_url( $post->ID ); ?>"><?php _e( 'Edit', 'waa' ); ?></a> | </span>
                                            <span class="delete"><a onclick="return confirm('Are you sure?');" href="<?php echo wp_nonce_url( add_query_arg( array( 'action' => 'waa-delete-product', 'product_id' => $post->ID ), waa_get_navigation_url('products') ), 'waa-delete-product' ); ?>"><?php _e( 'Delete Permanently', 'waa' ); ?></a> | </span>
                                            <span class="view"><a href="<?php echo get_permalink( $product->ID ); ?>" rel="permalink"><?php _e( 'View', 'waa' ); ?></a></span>
                                        </div>
                                    </td>
                                    <td class="post-status">
                                        <label class="waa-label <?php echo $post->post_status; ?>"><?php echo waa_get_post_status( $post->post_status ); ?></label>
                                    </td>
                                    <td>
                                        <?php
                                        if ( $product->is_in_stock() ) {
                                            echo '<mark class="instock">' . __( 'In stock', 'woocommerce' ) . '</mark>';
                                        } else {
                                            echo '<mark class="outofstock">' . __( 'Out of stock', 'woocommerce' ) . '</mark>';
                                        }

                                        if ( $product->managing_stock() ) :
                                            echo ' &times; ' . $product->get_total_stock();
                                        endif;
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ( $product->get_price_html() ) {
                                            echo $product->get_price_html();
                                        } else {
                                            echo '<span class="na">&ndash;</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php echo (int) get_post_meta( $post->ID, 'pageview', true ); ?>
                                    </td>
                                    <td class="post-date">
                                        <?php
                                        if ( '0000-00-00 00:00:00' == $post->post_date ) {
                                            $t_time = $h_time = __( 'Unpublished', 'waa' );
                                            $time_diff = 0;
                                        } else {
                                            $t_time = get_the_time( __( 'd.m.Y h:i:s', 'waa' ) );
                                            $m_time = $post->post_date;
                                            $time = get_post_time( 'G', true, $post );

                                            $time_diff = time() - $time;

                                            if ( $time_diff > 0 && $time_diff < 24 * 60 * 60 ) {
                                                $h_time = sprintf( __( '%s ago', 'waa' ), human_time_diff( $time ) );
                                            } else {
                                                $h_time = mysql2date( __( 'd.m.Y', 'waa' ), $m_time );
                                            }
                                        }

                                        echo '<abbr title="' . $t_time . '">' . apply_filters( 'post_date_column_time', $h_time, $post, 'date', 'all' ) . '</abbr>';
                                        echo '<br />';
                                        if ( 'publish' == $post->post_status ) {
                                            _e( 'Published', 'waa' );
                                        } elseif ( 'future' == $post->post_status ) {
                                            if ( $time_diff > 0 ) {
                                                echo '<strong class="attention">' . __( 'Missed schedule', 'waa' ) . '</strong>';
                                            } else {
                                                _e( 'Scheduled', 'waa' );
                                            }
                                        } else {
                                            _e( 'Last Modified', 'waa' );
                                        }
                                        ?>
                                    </td>
                                </tr>

                            <?php } ?>

                        <?php } else { ?>
                            <tr>
                                <td colspan="7"><?php _e( 'No product found', 'waa' ); ?></td>
                            </tr>
                        <?php } ?>

                    </tbody>

                </table>

                <?php
                wp_reset_postdata();

                $pagenum      = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
                $base_url = waa_get_navigation_url('products');
                
                if ( $product_query->max_num_pages > 1 ) {
                    echo '<div class="pagination-wrap">';
                    $page_links = paginate_links( array(
                        'current'   => $pagenum,
                        'total'     => $product_query->max_num_pages,
                        'base'      => $base_url. '%_%',
                        'format'    => '?pagenum=%#%',
                        'add_args'  => false,
                        'type'      => 'array',
                        'prev_text' => __( '&laquo; Previous', 'waa' ),
                        'next_text' => __( 'Next &raquo;', 'waa' )
                    ) );

                    echo '<ul class="pagination"><li>';
                    echo join("</li>\n\t<li>", $page_links);
                    echo "</li>\n</ul>\n";
                    echo '</div>';
                }
                ?>
            </article>

        <?php do_action( 'waa_after_listing_product' ); ?>
    </div><!-- #primary .content-area -->
</div><!-- .waa-dashboard-wrap -->