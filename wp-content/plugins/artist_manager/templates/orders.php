<div class="waa-dashboard-wrap">
    <?php waa_get_template( 'dashboard-nav.php', array( 'active_menu' => 'order' ) ); ?>

    <div class="waa-dashboard-content waa-orders-content">

        <article class="waa-orders-area">

            <?php if ( isset( $_GET['order_id'] ) ) { ?>
                <a href="<?php echo waa_get_navigation_url( 'orders' ) ; ?>" class="waa-btn"><?php _e( '&larr; Orders', 'waa' ); ?></a>
            <?php } else {
                waa_order_listing_status_filter();
            } ?>

            <?php
            $order_id = isset( $_GET['order_id'] ) ? intval( $_GET['order_id'] ) : 0;

            if ( $order_id ) {
                waa_get_template_part( 'orders/order-details' );
            } else {
                ?>
                <div class="waa-order-filter-serach">
                    <form action="" method="GET" class="waa-left">
                        <div class="waa-form-group">
                            <label for="from"><?php _e('Date', 'waa'); ?>:</label> <input type="text" class="datepicker" name="order_date" id="order_date_filter" value="<?php echo isset( $_GET['order_date'] ) ? sanitize_key( $_GET['order_date'] ) : ''; ?>">
                            <input type="submit" name="waa_order_filter" class="waa-btn waa-btn-sm waa-btn-danger waa-btn-theme" value="<?php esc_attr_e( 'Filter', 'waa' ); ?>">
                            <input type="hidden" name="order_status" value="<?php echo isset( $_GET['order_status'] ) ? sanitize_key( $_GET['order_status'] ) : 'all'; ?>">
                        </div>
                    </form>

                    <form action="" method="POST" class="waa-right">
                        <div class="waa-form-group">
                            <input type="submit" name="waa_order_export_all"  class="waa-btn waa-btn-sm waa-btn-danger waa-btn-theme" value="<?php esc_attr_e( 'Export All', 'waa' ); ?>">
                            <input type="submit" name="waa_order_export_filtered"  class="waa-btn waa-btn-sm waa-btn-danger waa-btn-theme" value="<?php esc_attr_e( 'Export Filtered', 'waa' ); ?>">
                            <input type="hidden" name="order_date" value="<?php echo isset( $_GET['order_date'] ) ? sanitize_key( $_GET['order_date'] ) : ''; ?>">
                            <input type="hidden" name="order_status" value="<?php echo isset( $_GET['order_status'] ) ? sanitize_key( $_GET['order_status'] ) : 'all'; ?>">
                        </div>
                    </form>

                    <div class="waa-clearfix"></div>
                </div>


                <?php
                waa_get_template_part( 'orders/listing' );
            }
            ?>

        </article>
    </div> <!-- #primary .content-area -->
</div><!-- .waa-dashboard-wrap -->