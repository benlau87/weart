<?php




/*-----------------------------------------------------------------------------------*/
/*  Change user profile page
/*-----------------------------------------------------------------------------------*/
function modify_user_contact_methods( $user_contact ) {

	// Add user contact methods
	$user_contact['pinterest']   = __( 'Pinterest Username'   );
	$user_contact['linkedin']   = __( 'LinkedIn User'   );
	$user_contact['twitter'] = __( 'Twitter Username' );

	// Remove user contact methods
	unset( $user_contact['aim']    );
	unset( $user_contact['jabber'] );

	return $user_contact;
}
add_filter( 'user_contactmethods', 'modify_user_contact_methods' );

function waa_add_country_field_user_profile( $user ) {
?>
	<h3><?php _e('Extra Profile Information', 'your_textdomain'); ?></h3>
	
<?php

global $woocommerce;
    $countries_obj   = new WC_Countries();
    $countries   = $countries_obj->__get('countries');
    echo '<table class="form-table">
		<tr>
			<th>
				<label for="artist_country">' . __('Country', 'waa') . '</label></th><td>';

    woocommerce_form_field('artist_country', array(
    'type'       => 'select',
    'class'      => array( 'country_to_state country_select ' ),
    'options'    => $countries,
		'custom_attributes' => array('data-selected' => esc_attr( get_the_author_meta( 'artist_country', $user->ID ) ))
    )
    );
    echo '</td></tr></table>'; ?>
		
		<script>
		jQuery(document).ready(function($) {
			$('#artist_country option').each(function() {
				if($(this).val() == $('#artist_country').attr('data-selected'))
					$(this).attr('selected',true);
			});
		});
		</script>
		<table class="form-table">
<tr>
	<th>
		<label for="artist_city"><?php _e('City'); ?></label>
	</th>
	<td>
		<input type="text" name="artist_city" id="artist_city" value="<?php echo esc_attr( get_the_author_meta( 'artist_city', $user->ID ) ); ?>" class="regular-text" />
	</td>
</tr>
</table>
<?php
}

function waa_save_country_field_user_profile( $user_id ) {
	
	if ( !current_user_can( 'edit_user', $user_id ) )
		return FALSE;
	
	update_usermeta( $user_id, 'artist_country', $_POST['artist_country'] );
	update_usermeta( $user_id, 'artist_city', $_POST['artist_city'] );
}

add_action( 'show_user_profile', 'waa_add_country_field_user_profile' );
add_action( 'edit_user_profile', 'waa_add_country_field_user_profile' );

add_action( 'personal_options_update', 'waa_save_country_field_user_profile' );
add_action( 'edit_user_profile_update', 'waa_save_country_field_user_profile' );




/*-----------------------------------------------------------------------------------*/
/*  Create menu entry for wp-admin
/*-----------------------------------------------------------------------------------*/
add_action( 'admin_menu', 'register_artist_order_page' );

function register_artist_order_page(){
	add_menu_page( 'My Sales', 'Sales', 'edit_products', 'artists_orders', 'artists_order_page', '', 45 ); 
	add_menu_page( 'My Products', 'Products', 'edit_products', 'artists_products', '', '', 40 ); 
	#add_submenu_page( 'artists_products', 'Add Product', 'Add product', 'edit_products', 'artists_add_product', '', 41); 
}


add_action( 'admin_menu' , 'my_function_name' );
	function my_function_name() {
	global $menu;
	$menu[40][2] = home_url('/') . 'wp-admin/edit.php?s&post_type=product&product_cat=pablo-picasso';
}



/*-----------------------------------------------------------------------------------*/
/*  Get all products of an artist
/*-----------------------------------------------------------------------------------*/
function artist_get_products() {
	global $woocommerce;
	// woocommerce db query products
	$args = array('post_type'	=> 'product', 'post_status'	=> 'any', 'posts_per_page' => -1, 'author__in' => array(get_current_user_id()), 'fields' => 'ids');
	$products = get_posts($args);
	return $products;
}


/*-----------------------------------------------------------------------------------*/
/*  Get all orders of an artist
/*-----------------------------------------------------------------------------------*/
function artist_get_orders() {
	global $woocommerce;
	
	// get products sold by artist
	$products = artist_get_products();
	
	// woocommerce db query orders
	$args = array('post_type'	=> 'shop_order', 'post_status'	=> 'any', 'posts_per_page' => -1);
	$loop = new WP_Query( $args );
	$i=0;
	while ( $loop->have_posts() ) : $loop->the_post();		// loop all orders
		$order_id = $loop->post->ID;
		$order = new WC_Order($order_id);
		$items = $order->get_items();		
		$orders[$i]['order_id'] = $order_id;
		$orders[$i]['order_date'] = get_the_time('d.m.Y');
		$orders[$i]['order_customer'] = $order->get_billing_address();
		$orders[$i]['order_status'] = wc_get_order_status_name( $order->get_status() );
		$orders[$i]['order_notes'] = $order->customer_message;
		foreach ( $items as $item ) {
			if(in_array($item['product_id'], $products)) { 			
				$orders[$i]['order_items'][] = $item;			
			}
		}
		$i++;		
	endwhile; 	
	return $orders;	
}
	

function artists_order_page($userid){
	?>	
	<div class="wrap">
		<h1 style="margin-bottom:10px"><?= _e('My Sales', 'waa'); ?></h1>
		<?php 
		if( current_user_can( 'edit_products' ) ) {
			$orders = artist_get_orders();
		?>
			<div class="clear"></div>
			<div class="table">
		<table class="wp-list-table widefat fixed striped posts">
	<thead>
		<tr>
			<th scope="col" id="order_number" class="manage-column column-order_status" style="width:100px"><span class="status_head"><?= _e( 'Order No.', 'waa' ); ?></span>			</th>
			<th scope="col" id="order_items" class="manage-column column-order_title column-primary"><span class="status_head"><?= _e( 'Purchased', 'waa' ); ?></span></th>
			<th scope="col" id="shipping_address" class="manage-column column-order_items"><?= _e( 'Ship to', 'waa' ); ?></th>
			<th scope="col" id="order_status" class="manage-column column-shipping_address"><?= _e( 'Order Status', 'waa' ); ?></th>
			<th scope="col" id="order_date" class="manage-column column-order_date"><span><?= _e( 'Date', 'waa' ); ?></span></th>
			<th scope="col" id="order_notes" class="manage-column column-order_notes"><span class="order-notes_head"><?= _e( 'Order Notes', 'waa' ); ?></span></th>
			<th scope="col" id="order_total" style="text-align:right" class="manage-column column-order_total"><span><strong><?= _e( 'Total', 'waa' ); ?></strong></span></th>
		</tr>
	</thead>
	<tbody>
					<?php
						$total_revenue = 0;
						for($i=0;$i<=count($orders);$i++) {							
							if(is_array($orders[$i]['order_items'])) {		
								echo '<tr>';
								echo '<td>#'.$orders[$i]['order_id'].'<br></td>';
								echo '<td>';
								$order_total = 0;		
									foreach ($orders[$i]['order_items'] as $order_item) {
										echo $order_item['qty'] . 'x <a href="' . home_url('/') . 'wp-admin/post.php?post=' . $order_item['product_id'] . '&action=edit">'.$order_item['name'].'</a><br>'; 
										$order_total += $order_item['line_total'];
									}
								echo '</td>';
								echo '<td>'.$orders[$i]['order_customer'].'</td>';
								echo '<td>'.$orders[$i]['order_status'].'</td>';
								echo '<td>'.$orders[$i]['order_date'].'</td>';			
								echo '<td>'.($orders[$i]['order_notes'] ? $orders[$i]['order_notes'] : '-').'</td>';			
								echo '<td style="font-weight:bold; text-align:right">'.format_currency_price($order_total).'</td>';
								$order_count += count($orders[$i]['order_id']);
								$total_revenue += $order_total;
							} 
						}
				  ?>
					</tbody>
				</table>	
				<br>
				<?= _e('Total orders: ', 'waa') . '<strong>' . ($order_count ? $order_count : '0') . '</strong><br>';
				echo _e('Total revenue: ', 'waa') . '<strong>' . format_currency_price($total_revenue) . '</strong><br>'; ?>
			</div>
	</div>
	<?php } else { echo 'You do not have permission for this page'; } 
	} 
	
	
	
	
/**
 * Automatically save artist category when adding a Product
 *
 */
function save_artist_category( $post_id, $post, $update ) {
	if(get_current_user_role() == 'artist') {
    $slug = 'product';
    // If this isn't a 'product' post, don't update it.
    if ( $slug != $post->post_type ) {
        return;
    }
		global $current_user;
    $current_user = wp_get_current_user();
		$user_login = $current_user->user_login;
		$cat_obj = get_term_by('slug',$user_login,'product_cat');
		$cat_id = $cat_obj->term_id;
		
    // - Update the post's metadata.
     wp_set_post_terms( $post_id, array($cat_id), 'product_cat' );
	}
}
add_action( 'save_post', 'save_artist_category', 10, 3 );


add_action('admin_init','load_my_script');
function load_my_script() {
  global $pagenow;
  if (($pagenow=='post.php' || $pagenow=='post-new.php') && get_current_user_role() == 'artist') {
		wp_register_style( 'hide-cats', get_template_directory_uri() . '/css/hide-cats.css' );
		wp_enqueue_style( 'hide-cats' );
  }
}

function get_current_user_role() {
	global $current_user;
	#get_currentuserinfo();
	$user_roles = $current_user->roles;
	$user_role = array_shift($user_roles);
	return $user_role;
};
	
?>