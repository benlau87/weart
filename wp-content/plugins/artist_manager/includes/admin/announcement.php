<?php
/**
 *  waa Announcement class for Admin
 *
 *  Announcement for seller
 *
 *  @since 2.1
 *
 *  @author WAA <info@WAA.com>
 */
class waa_Announcement {

    private $post_type = 'waa_announcement';
    private $assign_type = array();

	/**
	 *  Load autometically all actions
	 */
	function __construct() {
        $this->assign_type = array(
            ''                => __( '-- Select --', 'waa' ),
            'all_seller'      => __( 'All Seller' ),
            'selected_seller' => __( 'Selected Seller' )
        );

        add_action( 'init', array($this, 'post_types') );
        add_action( 'do_meta_boxes', array($this, 'do_metaboxes' ) );
        add_action( 'save_post', array($this, 'save_announcement_meta'), 10, 2 );

        add_filter( 'manage_edit-waa_announcement_columns', array( $this, 'add_type_columns' ) );
        add_filter( 'manage_waa_announcement_posts_custom_column', array( $this, 'assign_type_edit_columns' ), 10, 2 );
	}

    /**
     * Register Announcement post type
     *
     * @since 2.1
     *
     * @return void
     */
    function post_types() {
        register_post_type( $this->post_type, array(
            'label'           => __( 'Announcement', 'waa' ),
            'description'     => '',
            'public'          => false,
            'show_ui'         => true,
            'show_in_menu'    => false,
            'capability_type' => 'post',
            'hierarchical'    => false,
            'rewrite'         => array('slug' => ''),
            'query_var'       => false,
            'supports'        => array( 'title', 'editor' ),
            'labels'          => array(
                'name'               => __( 'Announcement', 'waa' ),
                'singular_name'      => __( 'Announcement', 'waa' ),
                'menu_name'          => __( 'waa Announcement', 'waa' ),
                'add_new'            => __( 'Add Announcement', 'waa' ),
                'add_new_item'       => __( 'Add New Announcement', 'waa' ),
                'edit'               => __( 'Edit', 'waa' ),
                'edit_item'          => __( 'Edit Announcement', 'waa' ),
                'new_item'           => __( 'New Announcement', 'waa' ),
                'view'               => __( 'View Announcement', 'waa' ),
                'view_item'          => __( 'View Announcement', 'waa' ),
                'search_items'       => __( 'Search Announcement', 'waa' ),
                'not_found'          => __( 'No Announcement Found', 'waa' ),
                'not_found_in_trash' => __( 'No Announcement found in trash', 'waa' ),
                'parent'             => __( 'Parent Announcement', 'waa' )
            ),
        ) );
    }

    /**
     * Initialize metabox for waa announcement post type
     *
     * @since 2.1
     *
     * @return void
     */
    function do_metaboxes() {
        add_meta_box( 'waa-announcement-meta-box', __('Announcement Settings', 'waa'), array( $this, 'meta_boxes_cb' ), $this->post_type, 'advanced', 'high' );
    }

    /**
     * Announcement metabox callback function
     *
     * @param  integer $post_id
     *
     * @return void
     */
    function meta_boxes_cb( $post_id ) {
        global $post;

        $user_search = new WP_User_Query( array( 'role' => 'seller' ) );
        $sellers     = $user_search->get_results();

        $announcement_type    = get_post_meta( $post->ID, '_announcement_type', true );
        $announcement_users   = get_post_meta( $post->ID, '_announcement_selected_user', true );
        $announcement_sellers = ( $announcement_users ) ? $announcement_users : array();

        ?>
            <table class="form-table waa-announcement-meta-wrap-table">
                <tr>
                    <th><?php _e( 'Send Announcement To', 'waa' ); ?></th>
                    <td>
                        <select name="waa_announcement_assign_type" id="waa_announcement_assign_type" style="width:60%">
                            <?php foreach ( $this->assign_type as $key => $type ): ?>
                                <option value="<?php echo $key; ?>" <?php selected( $announcement_type, $key ); ?>><?php echo $type; ?></option>
                            <?php endforeach ?>
                        </select>
                    </td>
                </tr>

                <tr class="selected_seller_field">
                    <th><?php _e( 'Select Sellers', 'waa' ); ?></th>
                    <td>
                        <select name="waa_announcement_assign_seller[]" data-placeholder= '<?php echo __( 'Select Sellers...', 'waa' ); ?>' id="waa_announcement_assign_seller" multiple="multiple">
                            <option></option>
                            <?php
                            foreach ( $sellers as $user ) {
                                $info = waa_get_store_info( $user->ID );

                                if ( isset( $info['store_name'] ) ) {
                                    ?>
                                    <option <?php echo in_array( $user->ID, $announcement_sellers ) ? 'selected="selected"' : ''; ?> value='<?php echo $user->ID  ?>'><?php echo esc_html( $info['store_name'] ) ?></option>
                                <?php } ?>
                            <?php } ?>

                        </select>
                    </td>
                </tr>
            </table>
            <?php wp_nonce_field( 'waa_announcement_meta_action', 'waa_announcement_meta_action_nonce' ); ?>

            <script>
                (function($){
                    $(document).ready( function() {
                        $('#waa_announcement_assign_seller').chosen( { width: '60%' });
                        $('table.waa-announcement-meta-wrap-table').on( 'change', 'select#waa_announcement_assign_type', function(){
                            var self = $(this);

                            if ( self.val() == 'selected_seller' ) {
                                $( 'tr.selected_seller_field' ).show();
                            } else {
                                $( 'tr.selected_seller_field' ).hide();
                            }
                        });

                        $('select#waa_announcement_assign_type').trigger('change')
                    });
                })(jQuery);
            </script>
            <style>
                .chosen-choices li.search-field input[type="text"] {
                    height: 23px !important;
                }
                tr.selected_seller_field{
                    display: none;
                }
            </style>
        <?php
    }

    /**
     * Add custom column label
     *
     * @since  2.1
     *
     * @param array $columns
     */
    function add_type_columns( $columns ) {
        unset( $columns['date'] );

        $columns['assign_type'] = __( 'Sent To' );
        $columns['date']        = __( 'Date' );

        return $columns;
    }

    /**
     * Render custom column content
     *
     * @since  2.1
     *
     * @param  string $column
     * @param  integer $post_id
     *
     * @return void
     */
    function assign_type_edit_columns( $column, $post_id ) {
        global $post;

        if ( $column == 'assign_type' ) {
            $assign_type = get_post_meta( $post_id, '_announcement_type', true );

            if ( $assign_type ) {
                echo $this->assign_type[$assign_type];
            } else {
                _e( 'No seller assigned!', 'waa' );
            }
        }
    }

    /**
     * Save Announcement post meta
     *
     * @since  2.1
     *
     * @param  integer $post_id
     * @param  object $post
     *
     * @return void
     */
    function save_announcement_meta( $post_id, $post ) {

        if ( ! isset( $_POST['waa_announcement_meta_action_nonce'] ) ) {
            return $post_id;
        }

        if ( ! wp_verify_nonce( $_POST['waa_announcement_meta_action_nonce'], 'waa_announcement_meta_action' ) ) {
            return $post_id;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return $post_id;

        $post_type = get_post_type_object( $post->post_type );

        if ( !current_user_can( $post_type->cap->edit_post, $post_id ) ) {
            return $post_id;
        }

        $announcement_assign_type         = ( isset( $_POST['waa_announcement_assign_type'] ) ) ? $_POST['waa_announcement_assign_type']: '';
        $announcement_assign_seller       = ( isset( $_POST['waa_announcement_assign_seller'] ) ) ? $_POST['waa_announcement_assign_seller']: array();
        $announcement_assign_seller_array = array();

        update_post_meta( $post_id, '_announcement_type', $announcement_assign_type );
        update_post_meta( $post_id, '_announcement_selected_user', $announcement_assign_seller );

        if ( $announcement_assign_type == 'selected_seller' ) {

            $this->process_seller_announcement_data( $announcement_assign_seller, $post_id );

        } elseif ( $announcement_assign_type == 'all_seller' ) {

            $users   = new WP_User_Query( array( 'role' => 'seller' ) );
            $sellers = $users->get_results();

            if ( $sellers ) {
                foreach ( $sellers as $user ) {
                    $announcement_assign_seller_array[] = $user->ID;
                }
            }

            $this->process_seller_announcement_data( $announcement_assign_seller_array, $post_id );
        }
    }

    /**
     * Proce seller announcement data
     *
     * @since  2.1
     *
     * @param  array $announcement_seller
     * @param  integer $post_id
     *
     * @return void
     */
    function process_seller_announcement_data( $announcement_seller, $post_id ) {

        $inserted_seller_id = $this->get_assign_seller( $post_id );

        if ( !empty( $inserted_seller_id ) ) {
            foreach ( $inserted_seller_id as $key => $value) {
                $db[] = $value['user_id'];
            }
        } else {
            $db = array();
        }

        $sellers         = $announcement_seller;
        $existing_seller = $new_seller = $del_seller = array();

        foreach( $sellers as $seller ) {
            if ( in_array( $seller, $db ) ) {
                $existing_seller[] = $seller;
            } else {
                $new_seller[] = $seller;
            }
        }

        $del_seller = array_diff( $db, $existing_seller );

        if ( $del_seller ) {
            $this->delete_assign_seller( $del_seller, $post_id );
        }

        if ( $new_seller ) {
            $this->insert_assign_seller( $new_seller, $post_id );
        }
    }

    /**
     * Get assign seller
     *
     * @since  2.1
     *
     * @param  integer $post_id
     *
     * @return array
     */
    function get_assign_seller( $post_id ) {
        global $wpdb;

        $table_name = $wpdb->prefix.'waa_announcement';

        $sql = "SELECT `user_id` FROM {$table_name} WHERE `post_id`= $post_id";

        $results = $wpdb->get_results( $sql, ARRAY_A );

        if ( $results ) {
            return $results;
        } else {
            return array();
        }
    }

    /**
     * Insert assing seller
     *
     * @since 2.1
     *
     * @param  array $seller_array
     * @param  integer $post_id
     *
     * @return void
     */
    function insert_assign_seller( $seller_array, $post_id ) {
        global $wpdb;

        $values     = '';
        $table_name = $wpdb->prefix.'waa_announcement';
        $i          = 0;

        foreach ( $seller_array as $key => $seller_id ) {
            $sep    = ( $i==0 ) ? '':',';
            $values .= sprintf( "%s ( %d, %d, '%s')", $sep, $seller_id, $post_id, 'unread' );

            $i++;
        }

        $sql = "INSERT INTO {$table_name} (`user_id`, `post_id`, `status` ) VALUES $values";
        $wpdb->query( $sql );
    }

    /**
     * Delete assign seller
     *
     * @since  2.1
     *
     * @param  array $seller_array
     * @param  integer $post_id
     *
     * @return void
     */
    function delete_assign_seller( $seller_array, $post_id ) {
        if ( ! is_array( $seller_array ) ) {
            return;
        }

        global $wpdb;

        $table_name = $wpdb->prefix.'waa_announcement';
        $values     = '';
        $i          = 0;

        foreach ( $seller_array as $key => $seller_id ) {
            $sep    = ( $i == 0 ) ? '' : ',';
            $values .= sprintf( "%s( %d, %d )", $sep, $seller_id, $post_id );

            $i++;
        }

        // $sellers = implode( ',', $seller_array );
        $sql = "DELETE FROM {$table_name} WHERE (`user_id`, `post_id` ) IN ($values)";

        if ( $values ) {
            $wpdb->query( $sql );
        }
    }
}
