<div class="waa-dashboard-wrap">
    <?php waa_get_template( 'dashboard-nav.php', array( 'active_menu' => 'report' ) ); ?>

    <div class="waa-dashboard-content waa-reports-content">

        <article class="waa-reports-area">
            <header class="waa-dashboard-header">
                <h1 class="entry-title"><?php _e( 'Reports', 'waa' ) ?></h1>
            </header><!-- .waa-dashboard-header -->

            <div class="waa-report-wrap">
                <?php
                global $woocommerce;

                require_once dirname( dirname(__FILE__) ) . '/includes/reports.php';

                $charts = waa_get_reports_charts();

                $link = waa_get_navigation_url( 'reports' );
                $current = isset( $_GET['chart'] ) ? $_GET['chart'] : 'overview';
                echo '<ul class="waa_tabs">';
                foreach ($charts['charts'] as $key => $value) {
                    $class = ( $current == $key ) ? ' class="active"' : '';
                    printf( '<li%s><a href="%s">%s</a></li>', $class, add_query_arg( array( 'chart' => $key ), $link ), $value['title'] );
                }
                echo '</ul>';
                ?>

                <?php if ( isset( $charts['charts'][$current] ) ) { ?>
                    <div id="waa_tabs_container">
                        <div class="tab-pane active" id="home">
                            <?php
                            $func = $charts['charts'][$current]['function'];
                            if ( $func && ( is_callable( $func ) ) ) {
                                call_user_func( $func );
                            }
                            ?>
                        </div>
                    </div>
                <?php } ?>
        </article>

    </div><!-- .waa-dashboard-content -->
</div><!-- .waa-dashboard-wrap -->