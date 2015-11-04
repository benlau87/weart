<?php
$user_id        = get_current_user_id();
$waa_withdraw = waa_Template_Withdraw::init();
?>
<div class="waa-dashboard-wrap">
    <?php waa_get_template( 'dashboard-nav.php', array('active_menu' => 'withdraw') ); ?>

    <div class="waa-dashboard-content waa-withdraw-content">

        <article class="waa-withdraw-area">
            <header class="entry-header">
                <h1 class="entry-title"><?php _e( 'Withdraw', 'waa' ); ?></h1>
            </header><!-- .entry-header -->

            <div class="entry-content">

                <?php if ( is_wp_error(waa_Template_Shortcodes::$validate) ) {
                    $messages = waa_Template_Shortcodes::$validate->get_error_messages();

                    foreach( $messages as $message ) {
                        ?>
                        <div class="waa-alert waa-alert-danger" style="width: 55%; margin-left: 10%;">
                            <button type="button" class="waa-close" data-dismiss="alert">&times;</button>
                            <strong><?php echo $type.$message; ?></strong>
                        </div>

                        <?php
                    }
                } ?>
            </div><!-- .entry-content -->

            <?php $current = isset( $_GET['type'] ) ? $_GET['type'] : 'pending'; ?>
            <ul class="list-inline subsubsub">
                <li<?php echo $current == 'pending' ? ' class="active"' : ''; ?>>
                    <a href="<?php echo waa_get_navigation_url( 'withdraw' ); ?>"><?php _e( 'Withdraw Request', 'waa' ); ?></a>
                </li>
                <li<?php echo $current == 'approved' ? ' class="active"' : ''; ?>>
                    <a href="<?php echo add_query_arg( array( 'type' => 'approved' ), waa_get_navigation_url( 'withdraw' ) ); ?>"><?php _e( 'Approved Requests', 'waa' ); ?></a>
                </li>
            </ul>

            <div class="waa-alert waa-alert-warning">
                <strong><?php printf( __( 'Current Balance: %s', 'waa' ), waa_get_seller_balance( $user_id ) ); ?> €</strong>
            </div>

            <?php if ( $current == 'pending' ) {
                $waa_withdraw->withdraw_form( waa_Template_Shortcodes::$validate );
            } elseif ( $current == 'approved' ) {
                $waa_withdraw->user_approved_withdraws( $user_id );
            } ?>

        </article>

    </div><!-- .waa-dashboard-content -->
</div><!-- .waa-dashboard-wrap -->