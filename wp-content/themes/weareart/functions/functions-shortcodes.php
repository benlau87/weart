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

    echo '<ul class="products" id="artists-page">';
    echo '<li class="grid-sizer"></li>';
    echo '<li class="gutter-sizer"></li>';

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

        $posts_array = get_posts($args);

        foreach ($posts_array as $art) {
            ?>

            <li class="product type-product">
                <a href="<?= waa_get_store_url($artist->ID) ?>"
                   title="<?php printf(__('Profil von %s ansehen	', 'waa'), $artist_info['store_name']); ?>">
                    <div class="the_post_image">
                        <?php
                        $image_title = esc_attr(get_the_title(get_post_thumbnail_id($art->ID)));
                        echo get_the_post_thumbnail($art->ID, array(600, 600), array('title' => $image_title, 'alt' => $image_title)); ?>
                    </div>
                    <div class="artist"><?= $artist_info['store_name']; ?><br/>
                        <?php
                        #( isset($artist_info['address']['city'] )) ? '<span class="artist-style">'.__('City', 'waa').': '.$artist_info['address']['city'].'</span>' : '';
                        ?>
                        <?= (isset($post_counts[$artist->ID])) ? '<span class="artist-style">' . __('Kunstwerke', 'waa') . ': ' . $post_counts[$artist->ID] . '</span>' : ''; ?>
                    </div>
                    <div class="artist-hover">
                        <div class="entry_author_image"><?php echo get_avatar($artist->ID, 150); ?></div>
                        <div class="bottom"><a
                                href="<?= waa_get_store_url($artist->ID) ?>"><?= $artist_info['store_name']; ?></a>
                        </div>
                    </div>
                </a>
            </li>
            <?php
        }
    }
    echo '</ul>';
}

add_shortcode('artists', 'waa_product_categories_art');


/*
* Show artists shortcode
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
    $return .= '<ul class="products" id="artists-page">';
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
                                               title="' . printf(__('Profil von %s ansehen	', 'waa'), $artist_info['store_name']) . '">' . $artist_info['store_name'] . '</a>
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


/*
* Home Slider (art show)
*  params: none
*/
function waa_home_art_slider($atts)
{
    $a = shortcode_atts(array(
        'number' => -1,
        'order' => 'rand',
    ), $atts);

    $user_search = new WP_User_Query(array('role' => 'seller', 'number' => 99999));
    $artists = (array)$user_search->get_results();
    $post_counts = count_many_users_posts(wp_list_pluck($artists, 'ID'), 'product');

    echo '<div class="art-slider">';
    echo '<ul>';
    foreach ($artists as $artist) {
        $artist_info = waa_get_store_info($artist->ID);
        #print_r($artist_info);
        $args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => 8,
            'author' => $artist->ID,
            'orderby' => 'rand'
        );

        $posts_array = get_posts($args);

        foreach ($posts_array as $art) {
            $image_title = esc_attr(get_the_title(get_post_thumbnail_id($art->ID)));
            $large_image_url = wp_get_attachment_image_src(get_post_thumbnail_id($art->ID), 'large');
            #echo $large_image_url[1].'x'.$large_image_url[2];

            if ($large_image_url[1] > $large_image_url[2] * 1.5) {
                ?>
                <li>
                    <img src="<?= $large_image_url[0]; ?>" alt="<?= $image_title ?>">
                    <header>
                    </header>
                    <footer>
                        <a href="<?= get_permalink($art->ID) ?>"
                           class="title"><?= $art->post_title; ?></a><br><?= __('von', 'waa') ?> <a
                            href="<?= waa_get_store_url($artist->ID) ?>" class="artist-name"
                            title="<?php printf(__('Kunstwerk von %s	', 'waa'), $artist_info['store_name']); ?>"><?= $artist_info['store_name']; ?></a>
                    </footer>
                </li>
            <?php }
        }
    }
    echo '</ul>';
    echo '</div>';
}

add_shortcode('slider', 'waa_home_art_slider');
?>