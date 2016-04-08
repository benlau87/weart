<?php
$user_id = get_current_user_id();
$home_url = home_url();
$active_class = ' class="active"'
?>

<div class="waa-dash-sidebar">
    <?php echo waa_dashboard_nav( $active_menu ); ?>
</div>

<?php
if (!waa_is_seller_enabled($user_id)) {
    wp_enqueue_style('get-started-style', waa_PLUGIN_ASSEST . '/css/get-started.css');
    ?>

    <script>
        jQuery(document).ready(function ($) {
            $('#get-started').modal({
                backdrop: 'static',
                keyboard: false
            });
        });

    </script>
    <div class="modal bsfade in" id="get-started" tabindex="-1" role="dialog" aria-labelledby="get-started"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <?php
                    require_once(waa_DIR . '/classes/get-started-modal.php');
                    $getStarted = new waa_GetStarted();
                    $getStarted->output_step();
                    ?>
                </div>
                <!-- <a class="close-popup" data-dismiss="modal" href="#">Schließen</a>-->
            </div>
        </div>
    </div>
    <?php
    #waa_seller_not_enabled_notice();
}
?>