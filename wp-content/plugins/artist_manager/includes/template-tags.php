<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package waa
 */

if ( ! function_exists( 'waa_content_nav' ) ) :

/**
 * Display navigation to next/previous pages when applicable
 */
function waa_content_nav( $nav_id, $query = null ) {
    global $wp_query, $post;

    if ( $query ) {
        $wp_query = $query;
    }

    // Don't print empty markup on single pages if there's nowhere to navigate.
    if ( is_single() ) {
        $previous = ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
        $next = get_adjacent_post( false, '', false );

        if ( !$next && !$previous )
            return;
    }

    // Don't print empty markup in archives if there's only one page.
    if ( $wp_query->max_num_pages < 2 && ( is_home() || is_archive() || is_search() ) )
        return;

    $nav_class = 'site-navigation paging-navigation';
    if ( is_single() )
        $nav_class = 'site-navigation post-navigation';
    ?>
    <nav role="navigation" id="<?php echo $nav_id; ?>" class="<?php echo $nav_class; ?>">

        <ul class="pager">
        <?php if ( is_single() ) : // navigation links for single posts  ?>

            <li class="previous">
                <?php previous_post_link( '%link', _x( '&larr;', 'Previous post link', 'waa' ) . ' %title' ); ?>
            </li>
            <li class="next">
                <?php next_post_link( '%link', '%title ' . _x( '&rarr;', 'Next post link', 'waa' ) ); ?>
            </li>

        <?php endif; ?>
        </ul>


        <?php if ( $wp_query->max_num_pages > 1 && ( is_home() || is_archive() || is_search() ) ) : // navigation links for home, archive, and search pages ?>
            <?php waa_page_navi( '', '', $wp_query ); ?>
        <?php endif; ?>

    </nav><!-- #<?php echo $nav_id; ?> -->
    <?php
}

endif;

if ( ! function_exists( 'waa_page_navi' ) ) :

function waa_page_navi( $before = '', $after = '', $wp_query ) {

    $posts_per_page = intval( get_query_var( 'posts_per_page' ) );
    $paged = intval( get_query_var( 'paged' ) );
    $numposts = $wp_query->found_posts;
    $max_page = $wp_query->max_num_pages;
    if ( $numposts <= $posts_per_page ) {
        return;
    }
    if ( empty( $paged ) || $paged == 0 ) {
        $paged = 1;
    }
    $pages_to_show = 7;
    $pages_to_show_minus_1 = $pages_to_show - 1;
    $half_page_start = floor( $pages_to_show_minus_1 / 2 );
    $half_page_end = ceil( $pages_to_show_minus_1 / 2 );
    $start_page = $paged - $half_page_start;
    if ( $start_page <= 0 ) {
        $start_page = 1;
    }
    $end_page = $paged + $half_page_end;
    if ( ($end_page - $start_page) != $pages_to_show_minus_1 ) {
        $end_page = $start_page + $pages_to_show_minus_1;
    }
    if ( $end_page > $max_page ) {
        $start_page = $max_page - $pages_to_show_minus_1;
        $end_page = $max_page;
    }
    if ( $start_page <= 0 ) {
        $start_page = 1;
    }

    echo $before . '<div class="waa-pagination-container"><ul class="waa-pagination">' . "";
    if ( $paged > 1 ) {
        $first_page_text = "&laquo;";
        echo '<li class="prev"><a href="' . get_pagenum_link() . '" title="First">' . $first_page_text . '</a></li>';
    }

    $prevposts = get_previous_posts_link( '&larr; Previous' );
    if ( $prevposts ) {
        echo '<li>' . $prevposts . '</li>';
    } else {
        echo '<li class="disabled"><a href="#">' . __( '&larr; Previous', 'waa' ) . '</a></li>';
    }

    for ($i = $start_page; $i <= $end_page; $i++) {
        if ( $i == $paged ) {
            echo '<li class="active"><a href="#">' . $i . '</a></li>';
        } else {
            echo '<li><a href="' . get_pagenum_link( $i ) . '">' . number_format_i18n( $i ) . '</a></li>';
        }
    }
    echo '<li class="">';
    next_posts_link( __('Next &rarr;', 'waa') );
    echo '</li>';
    if ( $end_page < $max_page ) {
        $last_page_text = "&larr;";
        echo '<li class="next"><a href="' . get_pagenum_link( $max_page ) . '" title="Last">' . $last_page_text . '</a></li>';
    }
    echo '</ul></div>' . $after . "";
}

endif;

function waa_product_dashboard_errors() {
    $type = isset( $_GET['message'] ) ? $_GET['message'] : '';

    switch ($type) {
        case 'product_deleted':
            ?>
            <div class="waa-alert waa-alert-success">
                <?php echo __( 'Product has been deleted successfully!', 'waa' ); ?>
            </div>
            <?php
            break;

        case 'error':
            ?>
            <div class="waa-alert waa-alert-danger">
                <?php echo __( 'Something went wrong!', 'waa' ); ?>
            </div>
            <?php
            break;
    }
}

function waa_product_listing_status_filter() {
    $permalink = waa_get_navigation_url( 'products' );
    $status_class = isset( $_GET['post_status'] ) ? $_GET['post_status'] : 'all';
    $post_counts = waa_count_posts( 'product', get_current_user_id() );
    ?>
    <ul class="waa-listing-filter waa-left subsubsub">
        <li<?php echo $status_class == 'all' ? ' class="active"' : ''; ?>>
            <a href="<?php echo $permalink; ?>"><?php printf( __( 'All (%d)', 'waa' ), $post_counts->total ); ?></a>
        </li>
        <li<?php echo $status_class == 'publish' ? ' class="active"' : ''; ?>>
            <a href="<?php echo add_query_arg( array( 'post_status' => 'publish' ), $permalink ); ?>"><?php printf( __( 'Online (%d)', 'waa' ), $post_counts->publish ); ?></a>
        </li>
        <!--<li<?php /*echo $status_class == 'pending' ? ' class="active"' : ''; */?>>
            <a href="<?php /*echo add_query_arg( array( 'post_status' => 'pending' ), $permalink ); */?>"><?php /*printf( __( 'Pending Review (%d)', 'waa' ), $post_counts->pending ); */?></a>
        </li>-->
        <li<?php echo $status_class == 'draft' ? ' class="active"' : ''; ?>>
            <a href="<?php echo add_query_arg( array( 'post_status' => 'draft' ), $permalink ); ?>"><?php printf( __( 'Draft (%d)', 'waa' ), $post_counts->draft ); ?></a>
        </li>
    </ul> <!-- .post-statuses-filter -->
    <?php
}

function waa_order_listing_status_filter() {
    $orders_url = waa_get_navigation_url( 'orders' );

    $status_class = isset( $_GET['order_status'] ) ? $_GET['order_status'] : 'all';
    $orders_counts = waa_count_orders( get_current_user_id() );
    $order_date = ( isset( $_GET['order_date'] ) ) ? $_GET['order_date'] : '';
    $date_filter = array();
    $all_order_url = array();
    $complete_order_url = array();
    $processing_order_url = array();
    $pending_order_url = array();
    $on_hold_order_url = array();
    $canceled_order_url = array();
    $refund_order_url = array();
    ?>

    <ul class="list-inline order-statuses-filter">
        <li<?php echo $status_class == 'all' ? ' class="active"' : ''; ?>>
            <?php
                if( $order_date ) {
                    $date_filter = array(
                        'order_date' => $order_date,
                        'waa_order_filter' => 'Filter',
                    );
                }
                $all_order_url = array_merge( $date_filter, array( 'order_status' => 'all' ) );
            ?>
            <a href="<?php echo ( empty( $all_order_url ) ) ? $orders_url : add_query_arg( $complete_order_url, $orders_url ); ?>">
                <?php printf( __( 'All (%d)', 'waa' ), $orders_counts->total ); ?></span>
            </a>
        </li>
        <li<?php echo $status_class == 'wc-completed' ? ' class="active"' : ''; ?>>
            <?php
                if( $order_date ) {
                    $date_filter = array(
                        'order_date' => $order_date,
                        'waa_order_filter' => 'Filter',
                    );
                }
                $complete_order_url = array_merge( array( 'order_status' => 'wc-completed' ), $date_filter );
            ?>
            <a href="<?php echo add_query_arg( $complete_order_url, $orders_url ); ?>">
                <?php printf( __( 'Completed (%d)', 'waa' ), $orders_counts->{'wc-completed'} ); ?></span>
            </a>
        </li>
        <li<?php echo $status_class == 'wc-processing' ? ' class="active"' : ''; ?>>
            <?php
                if( $order_date ) {
                    $date_filter = array(
                        'order_date' => $order_date,
                        'waa_order_filter' => 'Filter',
                    );
                }
                $processing_order_url = array_merge( $date_filter, array( 'order_status' => 'wc-processing' ) );
            ?>
            <a href="<?php echo add_query_arg( $processing_order_url, $orders_url ); ?>">
                <?php printf( __( 'Processing (%d)', 'waa' ), $orders_counts->{'wc-processing'} ); ?></span>
            </a>
        </li>
        <li<?php echo $status_class == 'wc-on-hold' ? ' class="active"' : ''; ?>>
            <?php
                if( $order_date ) {
                    $date_filter = array(
                        'order_date' => $order_date,
                        'waa_order_filter' => 'Filter',
                    );
                }
                $on_hold_order_url = array_merge( $date_filter, array( 'order_status' => 'wc-on-hold' ) );
            ?>
            <a href="<?php echo add_query_arg( $on_hold_order_url, $orders_url ); ?>">
                <?php printf( __( 'On-hold (%d)', 'waa' ), $orders_counts->{'wc-on-hold'} ); ?></span>
            </a>
        </li>
        <li<?php echo $status_class == 'wc-pending' ? ' class="active"' : ''; ?>>
            <?php
                if( $order_date ) {
                    $date_filter = array(
                        'order_date' => $order_date,
                        'waa_order_filter' => 'Filter',
                    );
                }
                $pending_order_url = array_merge( $date_filter, array( 'order_status' => 'wc-pending' ) );
            ?>
            <a href="<?php echo add_query_arg( $pending_order_url, $orders_url ); ?>">
                <?php printf( __( 'Pending (%d)', 'waa' ), $orders_counts->{'wc-pending'} ); ?></span>
            </a>
        </li>
        <li<?php echo $status_class == 'wc-canceled' ? ' class="active"' : ''; ?>>
            <?php
                if( $order_date ) {
                    $date_filter = array(
                        'order_date' => $order_date,
                        'waa_order_filter' => 'Filter',
                    );
                }
                $canceled_order_url = array_merge( $date_filter, array( 'order_status' => 'wc-cancelled' ) );
            ?>
            <a href="<?php echo add_query_arg( $canceled_order_url, $orders_url ); ?>">
                <?php printf( __( 'Cancelled (%d)', 'waa' ), $orders_counts->{'wc-cancelled'} ); ?></span>
            </a>
        </li>
        <li<?php echo $status_class == 'wc-refunded' ? ' class="active"' : ''; ?>>
            <?php
                if( $order_date ) {
                    $date_filter = array(
                        'order_date' => $order_date,
                        'waa_order_filter' => 'Filter',
                    );
                }
                $refund_order_url = array_merge( $date_filter, array( 'order_status' => 'wc-refunded' ) );
            ?>
            <a href="<?php echo add_query_arg( $refund_order_url, $orders_url ); ?>">
                <?php printf( __( 'Refunded (%d)', 'waa' ), $orders_counts->{'wc-refunded'} ); ?></span>
            </a>
        </li>
    </ul>
    <?php
}

/**
 * Dashboard Navigation menus
 *
 * @return array
 */
function waa_get_dashboard_nav() {

    $urls = array(
        'dashboard' => array(
            'title' => __( 'Dashboard', 'waa'),
            'icon'  => '<i class="ui ui-tachometer"></i>',
            'url'   => waa_get_navigation_url()
        ),
        'product' => array(
            'title' => __( 'Products', 'waa'),
            'icon'  => '<i class="ui ui-paint-brush"></i>',
            'url'   => waa_get_navigation_url( 'products' )
        ),
  /*      'order' => array(
            'title' => __( 'Orders', 'waa'),
            'icon'  => '<i class="ui ui-shopping-cart"></i>',
            'url'   => waa_get_navigation_url( 'orders' )
        ),
        'coupon' => array(
            'title' => __( 'Coupons', 'waa'),
            'icon'  => '<i class="ui ui-gift"></i>',
            'url'   => waa_get_navigation_url( 'coupons' )
        ),
        'reviews' => array(
            'title' => __( 'Reviews', 'waa'),
            'icon'  => '<i class="ui ui-comments-o"></i>',
            'url'   => waa_get_navigation_url( 'reviews' )
        ), 
				'report' => array(
            'title' => __( 'Reports', 'waa'),
            'icon'  => '<i class="ui ui-line-chart"></i>',
            'url'   => waa_get_navigation_url( 'reports' )
        ), 
        'withdraw' => array(
            'title' => __( 'Withdraw', 'waa'),
            'icon'  => '<i class="ui ui-upload"></i>',
            'url'   => waa_get_navigation_url( 'withdraw' )
        ), */
    );

    $urls = apply_filters( 'waa_get_dashboard_nav', $urls );

    $settings = array(
        'title' => __( 'Settings <i class="ui ui-angle-right pull-right"></i>', 'waa'),
        'icon'  => '<i class="ui ui-user"></i>',
        'url'   => waa_get_navigation_url( 'settings/store' )
    );

    $settings_sub = array(
        'back' => array(
            'title' => __( 'Back to Dashboard', 'waa'),
            'icon'  => '<i class="ui ui-long-arrow-left"></i>',
            'url'   => waa_get_navigation_url()
        ),
        'store' => array(
            'title' => __( 'Store', 'waa'),
            'icon'  => '<i class="ui ui-university"></i>',
            'url'   => waa_get_navigation_url( 'settings/store' )
        ),
        'payment' => array(
            'title' => __( 'Payment', 'waa'),
            'icon'  => '<i class="ui ui-credit-card"></i>',
            'url'   => waa_get_navigation_url( 'settings/payment' )
        )
    );

    $waa_shipping_option = get_option( 'woocommerce_waa_product_shipping_settings' );
    $enable_shipping       = ( isset( $waa_shipping_option['enabled'] ) ) ? $waa_shipping_option['enabled'] : 'yes';
    
    if ( $enable_shipping == 'yes' ) {
        $settings_sub['shipping'] = array(
            'title' => __( 'Shipping', 'waa'),
            'icon'  => '<i class="ui ui-truck"></i>',
            'url'   => waa_get_navigation_url( 'settings/shipping' )
        );
    }
    $settings_sub['social'] = array(
        'title' => __( 'Social Profile', 'waa'),
        'icon'  => '<i class="ui ui-share-alt-square"></i>',
        'url'   => waa_get_navigation_url( 'settings/social' )
    );
		$settings_sub['account'] = array(
        'title' => __( 'My Account', 'waa'),
        'icon'  => '<i class="ui ui-cog"></i>',
        'url'   => wc_customer_edit_account_url()
    );

    if ( waa_get_option( 'store_seo', 'waa_general', 'on' ) === 'on' ) {
        $settings_sub['seo'] = array(
            'title' => __( 'Store SEO', 'waa' ),
            'icon'  => '<i class="ui ui-globe"></i>',
            'url'   => waa_get_navigation_url( 'settings/seo' )
        );
    }
    
    /**
     * Filter to get the seller dashboard settings navigation.
     *
     * @since 2.2
     *
     * @param array.
     */
    $settings['sub']  = apply_filters( 'waa_get_dashboard_settings_nav', $settings_sub );

    $urls['settings'] = $settings;

    /**
     * Filter to get the final seller dashboard navigation.
     *
     * @since 2.2
     *
     * @param array $urls.
     */
    return apply_filters( 'waa_get_seller_dashboard_nav', $urls );
}

/**
 * Renders the waa dashboard menu
 *
 * For settings menu, the active menu format is `settings/menu_key_name`.
 * The active menu will be splitted at `/` and the `menu_key_name` will be matched
 * with settings sub menu array. If it's a match, the settings menu will be shown
 * only. Otherwise the main navigation menu will be shown.
 *
 * @param  string  $active_menu
 *
 * @return string rendered menu HTML
 */
function waa_dashboard_nav( $active_menu = '' ) {

    $nav_menu          = waa_get_dashboard_nav();
    $active_menu_parts = explode( '/', $active_menu );

    if ( isset( $active_menu_parts[1] ) && $active_menu_parts[0] == 'settings' && array_key_exists( $active_menu_parts[1], $nav_menu['settings']['sub'] ) ) {
        $urls        = $nav_menu['settings']['sub'];
        $active_menu = $active_menu_parts[1];
    } else {
        $urls = $nav_menu;
    }

    $menu = '<ul class="waa-dashboard-menu">';

    foreach ($urls as $key => $item) {
        $class = ( $active_menu == $key ) ? 'active ' . $key : $key;
        $menu .= sprintf( '<li class="%s"><a href="%s">%s %s</a></li>', $class, $item['url'], $item['icon'], $item['title'] );
    }
		$menu .= '<li><a href="'.wp_logout_url( home_url() ).'"><i class="ui ui-power-off"></i> '.__('Logout', 'waa').'</a></li>';
    $menu .= '</ul>';

    return $menu;
}


if ( ! function_exists( 'waa_store_category_menu' ) ) :

/**
 * Store category menu for a store
 *
 * @param  int $seller_id
 * @return void
 */
function waa_store_category_menu( $seller_id, $title = '' ) { ?>
    <aside class="widget waa-category-menu">
        <h3 class="widget-title"><?php echo $title; ?></h3>
        <div id="cat-drop-stack">
            <?php
            global $wpdb;

            $categories = get_transient( 'waa-store-category-'.$seller_id );

            if ( false === $categories ) {
                $sql = "SELECT t.term_id,t.name, tt.parent FROM $wpdb->terms as t
                        LEFT JOIN $wpdb->term_taxonomy as tt on t.term_id = tt.term_id
                        LEFT JOIN $wpdb->term_relationships AS tr on tt.term_taxonomy_id = tr.term_taxonomy_id
                        LEFT JOIN $wpdb->posts AS p on tr.object_id = p.ID
                        WHERE tt.taxonomy = 'product_cat'
                        AND p.post_type = 'product'
                        AND p.post_status = 'publish'
                        AND p.post_author = $seller_id GROUP BY t.term_id";

                $categories = $wpdb->get_results( $sql );
                set_transient( 'waa-store-category-'.$seller_id , $categories );
            }

            $args = array(
                'taxonomy'      => 'product_cat',
                'selected_cats' => ''
            );

            $walker = new waa_Store_Category_Walker( $seller_id );
            echo "<ul>";
            echo call_user_func_array( array(&$walker, 'walk'), array($categories, 0, array()) );
            echo "</ul>";
            ?>
        </div>
    </aside>
<?php
}

/**
 * Store categories by artist
 *
 * @param  int $seller_id
 * @return void
 */
function waa_store_categories( $seller_id ) { ?>
            <?php
            global $wpdb;

            $categories = get_transient( 'waa-store-category-'.$seller_id );

            if ( false === $categories ) {
                $sql = "SELECT t.term_id,t.name, tt.parent FROM $wpdb->terms as t
                        LEFT JOIN $wpdb->term_taxonomy as tt on t.term_id = tt.term_id
                        LEFT JOIN $wpdb->term_relationships AS tr on tt.term_taxonomy_id = tr.term_taxonomy_id
                        LEFT JOIN $wpdb->posts AS p on tr.object_id = p.ID
                        WHERE tt.taxonomy = 'product_cat'
                        AND p.post_type = 'product'
                        AND p.post_status = 'publish'
                        AND p.post_author = $seller_id GROUP BY t.term_id";

                $categories = $wpdb->get_results( $sql );
               # set_transient( 'waa-store-category-'.$seller_id , $categories );
            }
						$numItems = count($categories);
						$i = 0;
            foreach ($categories as $category) {
							#$url = get_term_link( $category, 'product_cat' );
							if(++$i == $numItems) {
								return $category->name;
							} else {
								#return $category->name.', ';
								return $category->name.' ';
							}
						}
            ?>
        </div>
<?php
}
endif;

/**
 * Clear transient once a product is saved or deleted
 *
 * @param  int $post_id
 *
 * @return void
 */
function waa_store_category_delete_transient( $post_id ) {

    $post_tmp = get_post( $post_id );
    $seller_id = $post_tmp->post_author;

    //delete store category transient
    delete_transient( 'waa-store-category-'.$seller_id );
}

add_action( 'delete_post', 'waa_store_category_delete_transient' );
add_action( 'save_post', 'waa_store_category_delete_transient' );



function waa_seller_reg_form_fields() {
if ( isset ( $_GET['role'] ) && !isset ( $_POST['role'] ) ) {
    $role = $_GET['role'];
} else {
    if ( isset ( $_POST['role'] )) {
        $role = $_POST['role'];
    } else {
        $role = 'customer';
    }
}
    $role_style = ( $role == 'customer' ) ? ' style="display:none"' : '';
    ?>
		<script>
		jQuery(document).ready(function($) {
			$('#region-select-container').hide();
			$('#country').change(function() {
				var selected_country = $(this).val();
				
				if(selected_country) {		
					$('#region-select-container').show();
					
					if(selected_country == "CH") {
						$('#region-select-container label.region_label').html('<?= __('Kanton wählen', 'waa');?>');
						$('#waa_user_region_field').html('<select id="regions_ch" name="region" class="region-select" required><?php $terms = get_terms("pa_stadt", array("hide_empty" => 0, "parent" => 50)); foreach ( $terms as $term ) {	echo "<option value=".$term->term_id.">".$term->name."</option>";	}	?></select>');						
					}
					else if(selected_country == "DE") {
						$('#region-select-container label.region_label').html('<?= __('Region wählen', 'waa');?>');
						$('#waa_user_region_field').html('<select id="regions_de" name="region" class="region-select" required><?php $terms = get_terms("pa_stadt", array("hide_empty" => 0, "parent" => 58)); foreach ( $terms as $term ) {	echo "<option value=".$term->term_id.">".$term->name."</option>";	}	?></select>');					
					} else if(selected_country == "AT") {
						$('#region-select-container label.region_label').html('<?= __('Region wählen', 'waa');?>');
						$('#waa_user_region_field').html('<select id="regions_at" name="region" class="region-select" required><?php $terms = get_terms("pa_stadt", array("hide_empty" => 0, "parent" => 60)); foreach ( $terms as $term ) {	echo "<option value=".$term->term_id.">".$term->name."</option>";	}	?></select>');					
				}
				}					
			});
		});
			</script>
    <div class="show_if_seller"<?php echo $role_style; ?>>

        <div class="split-row form-row-wide">
            <p class="form-row form-group">
                <label for="first-name"><?php _e( 'First Name', 'waa' ); ?> <span class="required">*</span></label>
                <input type="text" class="input-text" name="fname" id="first-name" value="<?php if ( ! empty( $_POST['fname'] ) ) echo esc_attr($_POST['fname']); ?>" required="required" />
            </p>

            <p class="form-row form-group">
                <label for="last-name"><?php _e( 'Last Name', 'waa' ); ?> <span class="required">*</span></label>
                <input type="text" class="input-text" name="lname" id="last-name" value="<?php if ( ! empty( $_POST['lname'] ) ) echo esc_attr($_POST['lname']); ?>" required="required" />
            </p>
        </div>

        <p class="form-row form-group form-row-wide">
            <label for="company-name"><?php _e( 'Shop Name', 'waa' ); ?> <span class="required">*</span></label>
            <input type="text" class="input-text" name="shopname" id="company-name" value="<?php if ( ! empty( $_POST['shopname'] ) ) echo esc_attr($_POST['shopname']); ?>" required="required" />
        </p>

        <p class="form-row form-group form-row-wide">
            <label for="seller-url" class="pull-left"><?php _e( 'Shop URL', 'waa' ); ?> <span class="required">*</span></label>
            <strong id="url-alart-mgs" class="pull-right"></strong>
            <input type="text" class="input-text" name="shopurl" id="seller-url" value="<?php if ( ! empty( $_POST['shopurl'] ) ) echo esc_attr($_POST['shopurl']); ?>" required="required" />
            <small><?php echo home_url() . '/' . waa_get_option( 'custom_store_url', 'waa_selling', 'store' ); ?>/<strong id="url-alart"></strong></small>
        </p>
				
				<div class="form-row form-group form-row-wide" id="country-select-countainer">
					<label for="waa_user_country" class="pull-left"><?php _e('Country', 'waa'); ?></label>
					<?php							
						global $woocommerce;
						$countries_obj   = new WC_Countries();
						$countries   = $countries_obj->get_allowed_countries();

						woocommerce_form_field('country', array(
						'type'       => 'select',
						'placeholder' => _x('Please Select An Option...', 'placeholder','woocommerce'),
						'options'    => array_merge(array('empty' => _x('Bitte wählen', 'waa')), $countries)
						)
						);
					?>
				</div>
				<div class="form-group" id="region-select-container">
					<label for="waa_user_region" class="region_label"><?php _e('Region wählen', 'waa'); ?></label>
					<div class="form-row" id="waa_user_region_field"></div>
				</div>

        <div class="form-row form-group form-row-wide">
            <label for="shop-phone"><?php _e( 'Phone', 'waa' ); ?><span class="required">*</span></label>
            <input type="text" class="input-text" name="phone" id="shop-phone" value="<?php if ( ! empty( $_POST['phone'] ) ) echo esc_attr($_POST['phone']); ?>" required="required" />
        </div>

        <?php  do_action( 'waa_seller_registration_field_after' ); ?>

    </div>

    <?php do_action( 'waa_reg_form_field' ); ?>

    <div class="radio user-role">
        <label>
            <input type="radio" name="role" value="customer"<?php checked( $role, 'customer' ); ?>>
            <?php _e( 'I am a customer', 'waa' ); ?>
        </label>
		</div>
		<div class="radio user-role">
        <label>
            <input type="radio" name="role" value="seller"<?php checked( $role, 'artist' ); ?>>
            <?php _e( 'I am a seller', 'waa' ); ?>
        </label>
			</div>
        <?php do_action( 'waa_registration_form_role', $role ); ?>
    </p>

    <?php
}

add_action( 'register_form', 'waa_seller_reg_form_fields' );

function waa_seller_not_enabled_notice() {
    ?>
        <div class="waa-alert waa-alert-warning">
            <strong><?php _e( 'Error!', 'waa' ); ?></strong>
            <?php _e( 'Your account is not enabled for selling, please contact the admin', 'waa' ); ?>
        </div>
    <?php
}

if ( !function_exists( 'waa_header_user_menu' ) ) :

/**
 * User top navigation menu
 *
 * @return void
 */
function waa_header_user_menu() {
    ?>
    <ul class="nav navbar-nav navbar-right">
        <li>
            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php printf( __( 'Cart %s', 'waa' ), '<span class="waa-cart-amount-top">(' . WC()->cart->get_cart_total() . ')</span>' ); ?> <b class="caret"></b></a>

            <ul class="dropdown-menu">
                <li>
                    <div class="widget_shopping_cart_content"></div>
                </li>
            </ul>
        </li>

        <?php if ( is_user_logged_in() ) { ?>

            <?php
            global $current_user;

            $user_id = $current_user->ID;
            if ( waa_is_user_seller( $user_id ) ) {
                ?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php _e( 'Seller Dashboard', 'waa' ); ?> <b class="caret"></b></a>

                    <ul class="dropdown-menu">
                        <li><a href="<?php echo waa_get_store_url( $user_id ); ?>" target="_blank"><?php _e( 'Visit your store', 'waa' ); ?> <i class="ui ui-external-link"></i></a></li>
                        <li class="divider"></li>
                        <?php
                        $nav_urls = waa_get_dashboard_nav();

                        foreach ($nav_urls as $key => $item) {
                            printf( '<li><a href="%s">%s &nbsp;%s</a></li>', $item['url'], $item['icon'], $item['title'] );
                        }
                        ?>
                    </ul>
                </li>
            <?php } ?>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo esc_html( $current_user->display_name ); ?> <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="<?php echo waa_get_page_url( 'my_orders' ); ?>"><?php _e( 'My Orders', 'waa' ); ?></a></li>
                    <li><a href="<?php echo waa_get_page_url( 'myaccount', 'woocommerce' ); ?>"><?php _e( 'My Account', 'waa' ); ?></a></li>
                    <li><a href="<?php echo wc_customer_edit_account_url(); ?>"><?php _e( 'Edit Account', 'waa' ); ?></a></li>
                    <li class="divider"></li>
                    <li><a href="<?php echo wc_get_endpoint_url( 'edit-address', 'billing', get_permalink( wc_get_page_id( 'myaccount' ) ) ); ?>"><?php _e( 'Billing Address', 'waa' ); ?></a></li>
                    <li><a href="<?php echo wc_get_endpoint_url( 'edit-address', 'shipping', get_permalink( wc_get_page_id( 'myaccount' ) ) ); ?>"><?php _e( 'Shipping Address', 'waa' ); ?></a></li>
                </ul>
            </li>

            <li><?php wp_loginout( home_url() ); ?></li>

        <?php } else { ?>
            <li><a href="<?php echo waa_get_page_url( 'myaccount', 'woocommerce' ); ?>"><?php _e( 'Log in', 'waa' ); ?></a></li>
            <li><a href="<?php echo waa_get_page_url( 'myaccount', 'woocommerce' ); ?>"><?php _e( 'Sign Up', 'waa' ); ?></a></li>
        <?php } ?>
    </ul>
    <?php
}

endif;

function waa_account_migration_button() {
    $user = wp_get_current_user();

    if ( waa_is_user_customer( $user->ID ) ) {
        ?>
        <p>&nbsp;</p>
        <p>
            <a href="<?php echo waa_get_page_url( 'myaccount', 'woocommerce' ); ?>account-migration/seller/" class="button button-primary"><?php _e( 'Become a Seller', 'waa' ); ?></a>
        </p>
        <?php
    }
}

add_action( 'woocommerce_after_my_account', 'waa_account_migration_button' );