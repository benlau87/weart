<div class="wrap">
    <h2><?php _e( 'Seller Listing', 'waa' ); ?></h2>

    <form action="<?php echo admin_url( 'users.php' ); ?>" method="get" style="margin-top: 15px;">

        <input type="hidden" name="s" value="">
        <?php wp_nonce_field( 'bulk-users' ); ?>

        <table class="widefat withdraw-table">
            <thead>
                <tr>
                    <th class="check-column">
                        <input type="checkbox" class="waa-withdraw-allcheck">
                    </th>
                    <th><?php _e( 'Name', 'waa' ); ?></th>
                    <th><?php _e( 'Shop Name', 'waa' ); ?></th>
                    <th><?php _e( 'E-mail', 'waa' ); ?></th>
                    <th><?php _e( 'Products', 'waa' ); ?></th>
                    <th><?php _e( 'Balance', 'waa' ); ?></th>
                    <th><?php _e( 'Kontodaten', 'waa' ); ?></th>
                    <th><?php _e( 'Phone', 'waa' ); ?></th>
                    <th><?php _e( 'Status', 'waa' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $paged = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
                $limit = 20;
                $count = 0;
                $offset = ( $paged - 1 ) * $limit;
                $user_search = new WP_User_Query( array( 'role' => 'seller', 'number' => $limit, 'offset' => $offset ) );
                $sellers = (array) $user_search->get_results();
                $post_counts = count_many_users_posts( wp_list_pluck( $sellers, 'ID' ), 'product' );

                if ( $sellers ) {

                    foreach ($sellers as $user) {
                        $info = waa_get_store_info( $user->ID );
                        $seller_enable = waa_is_seller_enabled( $user->ID );
                        $edit_link = esc_url( add_query_arg( 'wp_http_referer', urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ), get_edit_user_link( $user->ID ) ) );
                        ?>
                        <tr class="<?php echo ($count % 2 == 0) ? 'alternate' : 'odd'; ?> ">
                            <th class="check-column">
                                <input type="checkbox" class="waa-withdraw-allcheck" value="<?php echo $user->ID; ?>" name="users[]">
                            </th>
                            <td><?php echo $user->display_name; ?>
                                <div class="row-actions toggle-seller-status">
                                    <?php if ( !$seller_enable ) { ?>
                                        <span class="active"><a class="toggle-seller" href="#" data-id="<?php echo $user->ID; ?>" data-type="yes"><?php _e( 'Activate Selling', 'waa' ); ?></a> | </span>
                                    <?php } else { ?>
                                        <span class="active delete"><a class="toggle-seller" href="#" data-id="<?php echo $user->ID; ?>" data-type="no"><?php _e( 'Make Inactivate', 'waa' ); ?></a> | </span>
                                    <?php } ?>

                                    <span class="products-link"><a href="<?php echo admin_url( 'edit.php?post_type=product&author=' . $user->ID ); ?>"><?php _e( 'Products', 'waa' ); ?></a> | </span>
                                    <span class="orders-link"><a href="<?php echo admin_url( 'edit.php?post_type=shop_order&author=' . $user->ID ); ?>"><?php _e( 'Orders', 'waa' ); ?></a></span>
                                </div></td>
                            <td><?php echo empty( $info['store_name'] ) ? '--' : $info['store_name']; ?></td>
                            <td><?php echo $user->user_email; ?></td>
                            <td>
                                <a href="<?php echo admin_url( 'edit.php?post_type=product&author=' . $user->ID ); ?>">
                                    <?php echo isset( $post_counts[$user->ID] ) ? $post_counts[$user->ID] : 0; ?>
                                </a>
                            </td>
                            <td><?php echo waa_get_seller_balance( $user->ID ); ?></td>
                            <td><?php
                                echo empty ( $info['payment']['bank']['ac_name'] ) ? '' : 'Kontoinhaber: ' . $info['payment']['bank']['ac_name'] . '<br>';
                                echo empty ( $info['payment']['bank']['bank_name'] ) ? '' : 'Bank: ' . $info['payment']['bank']['bank_name'] . '<br>';
                                echo empty ( $info['payment']['bank']['ac_iban'] ) ? '' : 'IBAN: ' . $info['payment']['bank']['ac_iban'] . '<br>';
                                echo empty ( $info['payment']['bank']['ac_bic'] ) ? '' : 'BIC: ' . $info['payment']['bank']['ac_bic'] . '<br>';
                                echo empty ( $info['payment']['paypal']['email'] ) ? '' : 'PayPal: ' . $info['payment']['paypal']['email'] . '<br>';

                                ?></td>
                            <td><?php echo empty( $info['phone'] ) ? '--' : $info['phone']; ?></td>
                            <td>
                                <?php if ( $seller_enable ) {
                                    echo '<span class="seller-active">' . __( 'Active', 'waa' ) . '</span>';
                                } else {
                                    echo '<span class="seller-inactive">' . __( 'Inactive', 'waa' ) . '</span>';
                                } ?>
                            </td>
                        </tr>
                        <?php
                        $count++;
                    }
                } else {
                    echo '<tr><td colspan="9">' . __( 'No users found!', 'waa' ) .'</td></tr>';
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th class="check-column">
                        <input type="checkbox" class="waa-withdraw-allcheck">
                    </th>
                    <th><?php _e( 'Username', 'waa' ); ?></th>
                    <th><?php _e( 'Name', 'waa' ); ?></th>
                    <th><?php _e( 'Shop Name', 'waa' ); ?></th>
                    <th><?php _e( 'E-mail', 'waa' ); ?></th>
                    <th><?php _e( 'Products', 'waa' ); ?></th>
                    <th><?php _e( 'Balance', 'waa' ); ?></th>
                    <th><?php _e( 'Phone', 'waa' ); ?></th>
                    <th><?php _e( 'Status', 'waa' ); ?></th>
                </tr>
            </tfoot>
        </table>

        <div class="tablenav bottom">
            <div class="alignleft actions bulkactions">
                <select name="action2">
                    <option value="-1" selected="selected"><?php _e( 'Bulk Actions', 'waa' ); ?></option>
                    <option value="delete"><?php _e( 'Delete', 'waa' ); ?></option>
                </select>

                <input type="submit" name="" id="doaction2" class="button button-primary" value="<?php esc_attr_e( 'Apply', 'waa' ); ?>">
            </div>

            <?php
            $user_count = $user_search->total_users;
            $num_of_pages = ceil( $user_count / $limit );

            if ( $num_of_pages > 1 ) {
                $page_links = paginate_links( array(
                    'current' => $paged,
                    'total' => $num_of_pages,
                    // 'base' => admin_url( 'admin.php?page=waa-sellers&amp;page=%#%' ),
                    'base' => add_query_arg( 'pagenum', '%#%' ),
                    'prev_text' => __( '&larr; Previous', 'waa' ),
                    'next_text' => __( 'Next &rarr;', 'waa' ),
                    'add_args'  => false,
                ) );

                if ( $page_links ) {
                    echo '<div class="tablenav-pages" style="margin: 1em 0"><span class="pagination-links">' . $page_links . '</span></div>';
                }
            }
            ?>
        </div>
    </form>

    <style type="text/css">
        .seller-active { color: green; }
        .seller-inactive { color: red; }
    </style>

    <script type="text/javascript">
        jQuery(function($) {
            $('.toggle-seller-status').on('click', 'a.toggle-seller', function(e) {
                e.preventDefault();

                var data = {
                    'action' : 'waa_toggle_seller',
                    'user_id' : $(this).data('id'),
                    'type' : $(this).data('type')
                };

                $.post(ajaxurl, data, function(resp) {
                    window.location.reload();
                });
            });
        });
    </script>


</div>