<?php

/**
 * new WordPress Widget format
 * Wordpress 2.8 and above
 * @see http://codex.wordpress.org/Widgets_API#Developing_Widgets
 */
class waa_Best_Seller_Widget extends WP_Widget {

    /**
     * Constructor
     *
     * @return void
     **/
    public function __construct() {
        $widget_ops = array( 'classname' => 'waa-best-seller-widget', 'description' => 'waa best seller widget' );
        $this->WP_Widget( 'waa-best-seller-widget', 'waa: Best Sellers', $widget_ops );
    }

    /**
     * Outputs the HTML for this widget.
     *
     * @param array  An array of standard parameters for widgets in this theme
     * @param array  An array of settings for this widget instance
     * @return void Echoes it's output
     **/
    function widget( $args, $instance ) {

        extract( $args, EXTR_SKIP );

        $title = apply_filters( 'widget_title', $instance['title'] );
        $limit = absint( $instance['count'] ) ? absint( $instance['count'] ) : 10;

        $seller = waa_get_best_sellers( $limit );

        echo $before_widget;

        if ( ! empty( $title ) ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        ?>
        <ul class="waa-best-sellers">
            <?php

            if ( $seller ) {

                foreach ($seller as $key => $value) {
                    $rating = waa_get_seller_rating( $value->seller_id );
                    $display_rating = $rating['rating'];

                    if ( ! $rating['count'] ) {
                        $display_rating = __( 'No ratings found yet!', 'waa' );
                    }
                    ?>
                    <li>
                        <a href="<?php echo waa_get_store_url( $value->seller_id ); ?>">
                            <?php echo $value->display_name; ?>
                        </a><br />
                        <i class='fa fa-star'></i>
                        <?php echo $display_rating; ?>
                    </li>

                    <?php
                }
            }
            ?>
        </ul>
        <?php

        echo $after_widget;
    }

    /**
     * Deals with the settings when they are saved by the admin. Here is
     * where any validation should be dealt with.
     *
     * @param array  An array of new settings as submitted by the admin
     * @param array  An array of the previous settings
     * @return array The validated and (if necessary) amended settings
     **/
    function update( $new_instance, $old_instance ) {

        // update logic goes here
        $updated_instance = $new_instance;
        return $updated_instance;
    }

    /**
     * Displays the form for this widget on the Widgets page of the WP Admin area.
     *
     * @param array  An array of the current settings for this widget
     * @return void Echoes it's output
     **/
    function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array(
            'title' => __( 'Best Seller', 'waa' ),
            'count' => __( '3', 'waa' )
        ) );

        $title = $instance['title'];
        $count = $instance['count'];
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'waa' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'No of Seller:', 'waa' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="text" value="<?php echo esc_attr( $count ); ?>" />
        </p>
        <?php
    }
}

add_action( 'widgets_init', create_function( '', "register_widget( 'waa_Best_Seller_Widget' );" ) );