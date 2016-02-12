<?php


class waa_Toprated_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct( 'waa-top-rated', __( 'waa: Top Rated Product Widget', 'waa'), // Name
            array( 'description' => __( 'A Widget for displaying To rated products for waa', 'waa' ), 'classname' => 'woocommerce widget_products waa-top-rated' ) // Args
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', $instance['title'] );
        extract( $instance );

        $r = waa_get_top_rated_products( $no_of_product );

        echo $args['before_widget'];
        if ( ! empty( $title ) ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        if ( $r->have_posts() ) {


            echo '<ul class="waa-bestselling-product-widget product_list_widget">';

            while ( $r->have_posts()) {
                $r->the_post();
                wc_get_template( 'content-widget-product.php', array( 'show_rating' => $show_rating ) );
            }

            echo '</ul>';

        } else {
            echo "<p>No products found</p>";
        }
        echo $args['after_widget'];

        wp_reset_postdata();
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title         = esc_attr( $instance[ 'title' ] );
            $no_of_product = esc_attr( intval( $instance[ 'no_of_product' ] ) );
            $show_rating   = esc_attr( $instance['show_rating'] );
        }  else {
            $title         = __( 'Top Rated Product', 'waa' );
            $no_of_product = '8';
            $show_rating   = '0';
        }

        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'waa' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'no_of_product' ); ?>"><?php _e( 'No of Product:', 'waa' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'no_of_product' ); ?>" name="<?php echo $this->get_field_name( 'no_of_product' ); ?>" type="text" value="<?php echo ( $no_of_product == '-1' ) ? '' : $no_of_product; ?>">
        </p>
        <p>
            <input id="<?php echo $this->get_field_id( 'show_rating' ); ?>" name="<?php echo $this->get_field_name( 'show_rating' ); ?>" type="checkbox" value="1" <?php checked( '1', $show_rating ); ?> />
            <label for="<?php echo $this->get_field_id( 'show_rating' ); ?>"><?php _e( 'Show Product Rating', 'waa' ); ?></label>
        </p>

        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance                  = array();
        $instance['title']         = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['no_of_product'] = ( ! empty( $new_instance['no_of_product'] ) && is_numeric( $new_instance['no_of_product'] ) && $new_instance['no_of_product'] > 0 ) ? strip_tags( intval( $new_instance['no_of_product'] ) ) : '8';
        $instance['show_rating']   = ( ! empty( $new_instance['show_rating'] ) ) ? strip_tags( $new_instance['show_rating'] ) : '';
        return $instance;
    }

} // class waa best selling product widget

add_action( 'widgets_init', create_function( '', "register_widget( 'waa_Toprated_Widget' );" ) );