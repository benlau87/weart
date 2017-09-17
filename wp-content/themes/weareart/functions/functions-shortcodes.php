<?php
/*
* Show artists shortcode
*  params: number,order
*/
function waa_product_categories_art($atts)
{

		$a = shortcode_atts(array(
			'number' => -1,
			'order' => 'rand',
		), $atts);

		$user_search = new WP_User_Query(array('role' => 'seller', 'number' => 99999));
		$artists = (array)$user_search->get_results();
		$post_counts = count_many_users_posts(wp_list_pluck($artists, 'ID'), 'product');

		#$out = '<div class="row">
		#			<div class="col-md-9">';
		#$out .=	do_action('prdctfltr_output');
		#$out .=	do_shortcode('[prdctfltr_sc_products]');
		#$out .=	'</div>
		#		<div class="col-md-3">';
		#$out .= do_shortcode('[yith_woocommerce_ajax_search]');
		#$out .= '</div>
		#		</div>';

		/**
		 * Recursively sort an array of taxonomy terms hierarchically. Child categories will be
		 * placed under a 'children' member of their parent term.
		 * @param Array $cats taxonomy term objects to sort
		 * @param Array $into result array to put them in
		 * @param integer $parentId the current parent ID to put them in
		 */
		function sort_terms_hierarchicaly(Array &$cats, Array &$into, $parentId = 0)
		{
				foreach ($cats as $i => $cat) {
						if ($cat->parent == $parentId) {
								$into[$cat->term_id] = $cat;
								unset($cats[$i]);
						}
				}

				foreach ($into as $topCat) {
						$topCat->children = array();
						sort_terms_hierarchicaly($cats, $topCat->children, $topCat->term_id);
				}
		}

		$categories = get_terms('pa_stadt', array('hide_empty' => true));
		$categoryHierarchy = array();
		sort_terms_hierarchicaly($categories, $categoryHierarchy);


		$out = '<div id="prdctfltr_woocommerce" class="prdctfltr_wc prdctfltr_woocommerce woocommerce prdctfltr_wc_regular  pf_select prdctfltr_always_visible prdctfltr_click_filter prdctfltr_rows prdctfltr_scroll_active pf_mod_multirow pf_adptv_default prdctfltr_square prdctfltr_hierarchy_arrow" data-loader="oval">
				<span class="prdctfltr_filter_title">
				<a class="prdctfltr_woocommerce_filter pf_ajax_oval" href="#"><i class="prdctfltr-bars"></i></a>
	Künstler nach Stadt filtern</span>
		<form action="https://www.weare-art.com/kuenstler" class="prdctfltr_woocommerce_ordering" method="get">

					<div class="prdctfltr_buttons">
						</div>
		
		<div class="prdctfltr_filter_wrapper prdctfltr_columns_3" data-columns="3">
			<div class="prdctfltr_filter_inner">
						<div class="prdctfltr_filter prdctfltr_attributes prdctfltr_pa_stadt pf_attr_text prdctfltr_single prdctfltr_expand_parents" data-filter="pa_stadt">
							<input name="pa_stadt" type="hidden">
							<span class="prdctfltr_regular_title">Stadt<i class="prdctfltr-down"></i></span>		
							
							<div class="prdctfltr_checkboxes mCustomScrollbar _mCS_2 mCS_no_scrollbar" style="z-index:99;">
								<div id="mCSB_2" class="mCustomScrollBox mCS-light mCSB_vertical mCSB_inside" tabindex="0">
									<div id="mCSB_2_container" class="mCSB_container mCS_y_hidden mCS_no_scrollbar_y" style="position:relative; top:0; left:0;" dir="ltr">
							
							<label><input type="checkbox" value=""><span>kein Filter</span></label>';


		foreach ($categoryHierarchy as $country) {
				$out .= '<label class="prdctfltr_clicked"><input type="checkbox" value="' . $country->slug . '"><span>' . $country->name . '</span><i class="prdctfltr-plus"></i></label>';
				$out .= '<div class="prdctfltr_sub" data-sub="deutschland" style="display: block;">';

				foreach ($country->children as $city) {
						$checked = isset($_GET['pa_stadt']) && $_GET['pa_stadt'] == $city->slug ? true : false;
						$out .= '<label class="' . ($checked ? 'prdctfltr_active' : '') . '"><input type="checkbox" value="' . $city->slug . '"' . ($checked ? ' checked' : '') . '><span>' . $city->name . '</span></label>';
				}
				$out .= '</div>';
		}
		$out .= '</div><div id="mCSB_2_scrollbar_vertical" class="mCSB_scrollTools mCSB_2_scrollbar mCS-light mCSB_scrollTools_vertical mCSB_scrollTools_onDrag_expand" style="display: none;"><div class="mCSB_draggerContainer"><div id="mCSB_2_dragger_vertical" class="mCSB_dragger" style="position: absolute; min-height: 30px; top: 0px;" oncontextmenu="return false;"><div class="mCSB_dragger_bar" style="line-height: 30px;"></div></div><div class="mCSB_draggerRail"></div></div></div></div></div>
						</div>
												<div id="mCSB_3_scrollbar_vertical" class="mCSB_scrollTools mCSB_3_scrollbar mCS-light mCSB_scrollTools_vertical mCSB_scrollTools_onDrag_expand" style="display: none;"><div class="mCSB_draggerContainer"><div id="mCSB_3_dragger_vertical" class="mCSB_dragger" style="position: absolute; min-height: 30px; top: 0px;" oncontextmenu="return false;"><div class="mCSB_dragger_bar" style="line-height: 30px;"></div></div><div class="mCSB_draggerRail"></div></div></div></div>
						</div>

					<div class="prdctfltr_clear"></div>			</div>
			<div class="prdctfltr_clear"></div>
		</div>
				<div class="prdctfltr_add_inputs">
				</div>
	</form>
	</div>';

		$out .= '<ul class="products" id="artists-page">';
		$out .= '<li class="grid-sizer"></li>';
		$out .= '<li class="gutter-sizer"></li>';

		foreach ($artists as $artist) {
				$artist_info = waa_get_store_info($artist->ID);
				#print_r($artist_info);
				$args = array(
					'post_type' => 'product',
					'post_status' => 'publish',
					'posts_per_page' => 1,
					'author' => $artist->ID,
					'orderby' => 'rand'
				);

				if (isset($_GET['pa_stadt'])) {
						$args['tax_query'] = array(
							array(
								'taxonomy' => 'pa_stadt',
								'field' => 'slug',
								'terms' => isset($_GET['pa_stadt']) ? array($_GET['pa_stadt']) : array()
							)
						);
				}

				$posts_array = get_posts($args);

				foreach ($posts_array as $art) {

						# if (get_post_meta($art->ID, '_featured', true) != 'no') {


						$out .= '<li class="product type-product">
                    <a href="' . waa_get_store_url($artist->ID) . '"
                       title="' . sprintf(__('Profil von %s ansehen	', 'waa'), $artist_info['store_name']) . '">
                        <div class="the_post_image">';

						$image_title = esc_attr(get_the_title(get_post_thumbnail_id($art->ID)));
						$out .= get_the_post_thumbnail($art->ID, array(600, 600), array('title' => $image_title, 'alt' => $image_title));
						$out .= '</div>
                        <div class="artist">' . $artist_info['store_name'] . '<br/>';

						#( isset($artist_info['address']['city'] )) ? '<span class="artist-style">'.__('City', 'waa').': '.$artist_info['address']['city'].'</span>' : '';
						$out .= (isset($post_counts[$artist->ID])) ? '<span class="artist-style">' . __('Kunstwerke', 'waa') . ': ' . $post_counts[$artist->ID] . '</span>' : '';
						$out .= '</div>
                        <div class="artist-hover">
                            <div class="entry_author_image">' . get_avatar($artist->ID, 150) . '</div>
                            <div class="bottom"><a
                                  href="' . waa_get_store_url($artist->ID) . '">' . $artist_info['store_name'] . '</a>
                            </div>
                        </div>
                    </a>
                </li>';
						#   }
				}
		}
		$out .= '</ul>';

		return $out;
}

add_shortcode('artists', 'waa_product_categories_art');


/*
* Show hirable artists shortcode
*  params: number,order
*/
function waa_show_hirable_artists($atts)
{

		$a = shortcode_atts(array(
			'number' => -1,
			'order' => 'rand',
		), $atts);

		$user_search = new WP_User_Query(array('role' => 'seller', 'number' => 99999));
		$artists = (array)$user_search->get_results();
		$post_counts = count_many_users_posts(wp_list_pluck($artists, 'ID'), 'product');
		$return = '<ul class="products" id="artists-page">';
		$return .= '<li class="grid-sizer"></li>';
		$return .= '<li class="gutter-sizer"></li>';

		foreach ($artists as $artist) {
				$artist_info = waa_get_store_info($artist->ID);


				if ($artist_info['enable_services'] == 'yes') {
						$args = array(
							'post_type' => 'product',
							'post_status' => 'publish',
							'posts_per_page' => 1,
							'author' => $artist->ID,
							'orderby' => 'rand'
						);
						$posts_array = get_posts($args);

						foreach ($posts_array as $art) {


								$return .= '<li class="product type-product">
                    <div class="the_post_image">';
								$image_title = esc_attr(get_the_title(get_post_thumbnail_id($art->ID)));
								$return .= get_the_post_thumbnail($art->ID, array(600, 600), array('title' => $image_title, 'alt' => $image_title));
								$return .= '</div>';
								$return .= '<div class="artist">' . $artist_info['store_name'] . ' <br />';
								$return .= (isset($post_counts[$artist->ID]) ? '<span class="artist-style">' . __('Kunstwerke', 'waa') . ': ' . $post_counts[$artist->ID] . '</span>' : '');
								$return .= '</div>
                    <div class="artist-hover">
                        <div class="entry_author_image">' . get_avatar($artist->ID, 150) . '</div>
                        <div class="bottom"><a href="' . waa_get_store_url($artist->ID) . '"
                                               title="' . sprintf(__('Profil von %s ansehen	', 'waa'), $artist_info['store_name']) . '">' . $artist_info['store_name'] . '</a>
                        </div>
                    </div>
                    </a>
                </li>';

						}
				}
		}
		$return .= '</ul>';
		return $return;
}

add_shortcode('hirable_artists', 'waa_show_hirable_artists');

function wp_user_query_random_enable($query)
{
		if ($query->query_vars["orderby"] == 'rand') {
				$query->query_orderby = 'ORDER by RAND()';
		}
}

add_filter('pre_user_query', 'wp_user_query_random_enable');

/*
* Home Slider (art show)
*  params: $atts
*/
function waa_home_art_slider($atts)
{
		$a = shortcode_atts(array(
			'number' => -1,
			'order' => 'rand',
		), $atts);

		$user_search = new WP_User_Query(array('role' => 'seller', 'number' => 99999, 'orderby' => 'rand'));
		$artists = (array)$user_search->get_results();
		$post_counts = count_many_users_posts(wp_list_pluck($artists, 'ID'), 'product');


		echo '<div class="art-slider">';
		echo '<ul>';
		$i = 0;
		foreach ($artists as $artist) {
				#if($i==20) break;
				$artist_info = waa_get_store_info($artist->ID);

				$posts_array = get_featured_products_by_artist($artist->ID);

				foreach ($posts_array as $art) {
						if ($i >= 10)
								break; else $i++;
						$image_title = esc_attr(get_the_title(get_post_thumbnail_id($art->ID)));
						$large_image_url = wp_get_attachment_image_src(get_post_thumbnail_id($art->ID), 'large');
						#echo $large_image_url[1].'x'.$large_image_url[2];

						#print_r($art);
						# echo get_post_meta($art->ID, '_featured', true);

						# if ($large_image_url[1] > $large_image_url[2] * 1.5) {
						#if (get_post_meta($art->ID, '_featured', true) != 'no') {
						?>
			<li>
				<div class="slider-img"><a href="<?= get_permalink($art->ID) ?>"
				                           class="title"><img src="<?= $large_image_url[0]; ?>"
				                                              alt="<?= $image_title ?>"></a></div>
				<header>
				</header>
				<footer>
					<a href="<?= get_permalink($art->ID) ?>"
					   class="title"><?= $art->post_title; ?></a><br><?= __('von', 'waa') ?> <a
							href="<?= waa_get_store_url($artist->ID) ?>" class="artist-name"
							title="<?php printf(__('Kunstwerk von %s	', 'waa'), $artist_info['store_name']); ?>"><?= $artist_info['store_name']; ?></a>
				</footer>
			</li>
						<?php
						#}
				}
				# }
		}
		echo '</ul>';
		echo '</div>';
}

add_shortcode('slider', 'waa_home_art_slider');
?>