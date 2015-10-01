<?php
function waa_product_categories_art($atts) {
	$a = shortcode_atts( array(
			'number' => -1,
			'order' => 'rand',
		), $atts);
		
		/*$args = array( 'post_type' => 'product', 'posts_per_page' => $a['number'], 'orderby' => $a['order'], 'tax_query' => array('taxonomy' => '') );
		$loop = new WP_Query( $args );
		while ( $loop->have_posts() ) : $loop->the_post(); 
		global $product; */
		$taxonomyName = "product_cat";
    $prod_categories = get_terms($taxonomyName, array(
            'orderby'=> 'name',
            'order' => 'ASC',
            'hide_empty' => 1
    ));  
		echo '<ul class="products" id="artists-page">';
			echo '<li class="grid-sizer"></li>';
			echo '<li class="gutter-sizer"></li>';
    foreach( $prod_categories as $prod_cat ) :
	if($prod_cat->parent) {
		$user = get_user_by('login',$prod_cat->slug);
		$user_info = get_userdata($user->ID);
		$user_meta = get_user_meta($user->ID);
		$post_array = get_posts(array('post_type' => 'product', 'term' => $prod_cat->term_id, 'numberposts' => 1, 'orderby' => rand));
		$rand_post_id = $post_array[0]->ID;
			?>
			<li class="product type-product">    
				<a href="<?= get_term_link((int)$prod_cat->term_id, 'product_cat') ?>" title="<?php printf( __('Profil von %s ansehen	', 'waa'), $prod_cat->name); ?>">
					<div class="the_post_image">	
						<?php echo get_the_post_thumbnail($rand_post_id, 'post-thumbnail');	?>
					</div>
						<div class="artist"><?= $prod_cat->name; ?><br/>
								<span class="artist-style">Stil: Street Art, Modern
						</div>
						<div class="artist-hover">
							<div class="desc"><h3>Lorem Ipsum</h3>
								<p>Lorem ipsum dolir Sit Amed</p>
							</div>
							<div class="bottom">Some more info..</div>
						</div>
				</a>
			</li>
	<?php } endforeach; ?>
	</ul>
<?php }
add_shortcode('artists', 'waa_product_categories_art');
?>