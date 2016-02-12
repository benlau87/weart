<?php
$waa_admin_withdraw = waa_Template_Withdraw::init();
$counts = waa_get_withdraw_count();

$status = isset( $_GET['status'] ) ? $_GET['status'] : 'pending';
?>
<div class="wrap">
    <h2><?php _e( 'Withdraw', 'waa' ); ?></h2>

    <ul class="subsubsub" style="float: none;">
        <li>
            <a href="admin.php?page=waa-withdraw&amp;status=pending" <?php if ( $status == 'pending' ) echo 'class="current"'; ?>>
                <?php _e( 'Pending', 'waa' ); ?> <span class="count">(<?php echo $counts['pending'] ?>)</span>
            </a> |
        </li>
        <li>
            <a href="admin.php?page=waa-withdraw&amp;status=completed" <?php if ( $status == 'completed' ) echo 'class="current"'; ?>>
                <?php _e( 'Approved', 'waa' ); ?> <span class="count">(<?php echo $counts['completed'] ?>)</span>
            </a> |
        </li>
        <li>
            <a href="admin.php?page=waa-withdraw&amp;status=cancelled" <?php if ( $status == 'cancelled' ) echo 'class="current"'; ?>>
                <?php _e( 'Cancelled', 'waa' ); ?> <span class="count">(<?php echo $counts['cancelled'] ?>)</span>
            </a>
        </li>
    </ul>

    <?php

    $waa_admin_withdraw->admin_withdraw_list( $status );
    ?>
</div>