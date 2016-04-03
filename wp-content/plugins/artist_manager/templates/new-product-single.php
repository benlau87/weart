<?php
#error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
#ini_set('display_errors', 'On');
global $post;

$from_shortcode = false;

$user_id = get_current_user_id();

$waa_product_type = '';

if (isset($post->ID) && $post->ID && $post->post_type == 'product') {
    $post_id = $post->ID;
    $post_title = $post->post_title;
    $post_content = $post->post_content;
    $post_excerpt = $post->post_excerpt;
    $post_status = $post->post_status;
} else {
    $post_id = NULL;
    $post_title = isset($_POST['post_title']) ? $_POST['post_title'] : '';
    $post_content = isset($_POST['post_content']) ? $_POST['post_content'] : '';
    $_regular_price = isset($_POST['_regular_price']) ? $_POST['_regular_price'] : '';
    $featured_image = isset($_POST['feat_image_id']) ? $_POST['feat_image_id'] : '';
    $post_excerpt = '';
    $post_status = 'publish';
    $from_shortcode = true;
    $waa_product_type = isset($_POST['waa_product_type']) ? $_POST['waa_product_type'] : '';
    $_has_attribute = isset($_POST['_has_attribute']) ? $_POST['_has_attribute'] : '';
    #$_create_variations = $_POST['_create_variation'];
    $_manage_stock = isset($_POST['_manage_stock']) ? $_POST['_manage_stock'] : '';
    $_sold_individually = isset($_POST['_sold_individually']) ? $_POST['_sold_individually'] : '';
    $_stock = isset($_POST['_stock']) ? $_POST['_stock'] : '';
    $_stock_status = isset($_POST['_stock_status']) ? $_POST['_stock_status'] : '';
    $_backorders = isset($_POST['_backorders']) ? $_POST['_backorders'] : '';
    $_weight = isset($_POST['_weight']) ? $_POST['_weight'] : '';
    $_length = isset($_POST['_length']) ? $_POST['_length'] : '';
    $_width = isset($_POST['_width']) ? $_POST['_width'] : '';
    $_height = isset($_POST['_height']) ? $_POST['_height'] : '';
    $_overwrite_shipping = isset($_POST['_overwrite_shipping']) ? $_POST['_overwrite_shipping'] : '';
    $_additional_product_price = isset($_POST['_additional_product_price']) ? $_POST['_additional_product_price'] : '';

   # $_additional_price = isset($_POST['_additional_price']) ? waa_get_woocs_int_price_reverse($_POST['_additional_price']) : '';
    $_additional_qty = isset($_POST['_additional_qty']) ? waa_get_woocs_int_price_reverse($_POST['_additional_qty']) : '';
    $_purchase_note = isset($_POST['_purchase_note']) ? $_POST['_purchase_note'] : '';
    # $product_shipping_pt = isset($_POST['_dps_processing_time']) ? $_POST['_dps_processing_time'] : '';
}

if (isset($_GET['product_id'])) {
    $post_id = intval($_GET['product_id']);
    $post = get_post($post_id);
    $post_title = $post->post_title;
    $post_content = $post->post_content;
    $post_excerpt = $post->post_excerpt;
    $post_status = $post->post_status;
    $product = wc_get_product($post_id);
    $from_shortcode = true;
}

if (isset($product)) {
    $_regular_price = get_post_meta($post_id, '_regular_price', true);
    $_sale_price = get_post_meta($post_id, '_sale_price', true);
    $is_discount = !empty($_sale_price) ? true : false;
    $_sale_price_dates_from = get_post_meta($post_id, '_sale_price_dates_from', true);
    $_sale_price_dates_to = get_post_meta($post_id, '_sale_price_dates_to', true);

    $_sale_price_dates_from = !empty($_sale_price_dates_from) ? date_i18n('Y-m-d', $_sale_price_dates_from) : '';
    $_sale_price_dates_to = !empty($_sale_price_dates_to) ? date_i18n('Y-m-d', $_sale_price_dates_to) : '';
    $show_schedule = false;

    if (!empty($_sale_price_dates_from) && !empty($_sale_price_dates_to)) {
        $show_schedule = true;
    }

    $_featured = get_post_meta($post_id, '_featured', true);
    $_weight = get_post_meta($post_id, '_weight', true);
    $_length = get_post_meta($post_id, '_length', true);
    $_width = get_post_meta($post_id, '_width', true);
    $_height = get_post_meta($post_id, '_height', true);
    $_downloadable = get_post_meta($post_id, '_downloadable', true);
    $_stock = get_post_meta($post_id, '_stock', true);
    $_stock_status = get_post_meta($post_id, '_stock_status', true);
    $_visibility = get_post_meta($post_id, '_visibility', true);
    $_enable_reviews = $post->comment_status;
    $_required_tax = get_post_meta($post_id, '_required_tax', true);
    $_has_attribute = get_post_meta($post_id, '_has_attribute', true);
    $_create_variations = get_post_meta($post_id, '_create_variation', true);

    /* Art specific fields */
    // reverse logic: waa_only_print now stands for "i want to sell the original art as well"
    $waa_only_print = get_post_meta($post_id, 'waa_only_print', true) == 'yes' ? 'no' : 'yes';
    $waa_original_price = get_post_meta($post_id, 'waa_original_price', true);
    $waa_product_type = get_post_meta($post_id, 'waa_product_type', true) ? get_post_meta($post_id, 'waa_product_type', true) : 'fallback';


    $processing_time = waa_get_shipping_processing_times();
    $_disable_shipping = (get_post_meta($post_id, '_disable_shipping', true)) ? get_post_meta($post_id, '_disable_shipping', true) : 'no';
    #$_additional_price = waa_get_woocs_int_price_reverse(get_post_meta($post_id, '_additional_price', true));
    $_additional_qty = waa_get_woocs_int_price_reverse(get_post_meta($post_id, '_additional_qty', true));
    $_processing_time = get_post_meta($post_id, '_dps_processing_time', true);
    $dps_shipping_type_price = get_user_meta($user_id, '_dps_shipping_type_price', true);
    $dps_additional_qty = get_user_meta($user_id, '_dps_additional_qty', true);

    #$product_shipping_pt = ($_processing_time) ? $_processing_time : $dps_pt;
    $attribute_taxonomies = wc_get_attribute_taxonomies();

    $product_attributes = get_post_meta($post_id, '_product_attributes', true);

}

//print_r(get_post_meta($post_id));

$processing_time = waa_get_shipping_processing_times();

$tax_classes = array_filter(array_map('trim', explode("\n", get_option('woocommerce_tax_classes'))));
$classes_options = array();
$classes_options[''] = __('Standard', 'waa');

if ($tax_classes) {

    foreach ($tax_classes as $class) {
        $classes_options[sanitize_title($class)] = esc_html($class);
    }
}

if (!$from_shortcode) {
    get_header();
}

$woocs = new WOOCS();

if ($_POST['waa_product_type'] == 'sell-original' || $waa_product_type == 'sell-original') {
    ?>
    <script>jQuery(document).ready(function () {
            setWaaProductTypeOriginal();
        }); </script>
<?php }
if ($_POST['waa_product_type'] == 'sell-prints' || $waa_product_type == 'sell-prints') {
    ?>
    <script>jQuery(document).ready(function () {
            setWaaProductTypePrints();
        }); </script>
<?php }
if ($_POST['waa_product_type'] == 'sell-both' || $waa_product_type == 'sell-both' || $waa_product_type == 'fallback') {
    ?>
    <script>jQuery(document).ready(function () {
            setWaaProductTypeBoth();
        }); </script>
<?php }
if (empty($waa_product_type) && !empty($product))
    echo '<script>jQuery(document).ready(function() { showProductContainer(); });</script>';
?>
<script type="text/javascript">
    function showProductContainer() {
        jQuery('.product-set-type').hide();
        jQuery('.product-edit-new-container').show();
    }
    function setWaaProductTypeOriginal() {
        showProductContainer();
        jQuery('.hide-if-sell-original').addClass('display-none');
        jQuery('.hide_if_only_print').show();
        jQuery('#waa_only_print').attr('checked', true);
        jQuery('#_sold_individually').attr('checked', true);
        jQuery('#_manage_stock').attr('checked', true);
        jQuery('#_stock').val(1);
        jQuery('#waa_product_type').val('sell-original');
        jQuery('#_regular_price').attr('required', true);
    }
    function setWaaProductTypePrints() {
        showProductContainer();
        jQuery('.hide-if-sell-prints').css('display', 'none !important');
        jQuery('#waa_only_print').attr('checked', false);
        jQuery('#_sold_individually').attr('checked', false);
        jQuery('#waa_product_type').val('sell-prints');
        jQuery('#_regular_price').attr('required', false);
    }
    function setWaaProductTypeBoth() {
        showProductContainer();
        jQuery('.hide-if-sell-both').css('display', 'none !important');
        jQuery('#waa_product_type').val('sell-both');
    }
</script>
<section id="content" role="main">
    <div class="container">
        <div class="row">
            <div class="col-md-12" role="main">
                <div class="waa-dashboard-wrap">
                    <?php waa_get_template('dashboard-nav.php', array('active_menu' => 'product')); ?>

                    <?php if (artist_can_add_product($user_id) === true): ?>
                    <div class="waa-dashboard-content waa-product-edit">
                        <header class="waa-dashboard-header waa-clearfix">
                            <h1 class="entry-title">
                                <?php if (!$post_id): ?>
                                    <?php _e('Add New Product', 'waa'); ?>
                                <?php else: ?>
                                    <?php _e('Edit Product', 'waa'); ?>
                                    <span
                                        class="waa-label <?php echo waa_get_post_status_label_class($post->post_status); ?> waa-product-status-label">
																<?php echo waa_get_post_status($post->post_status); ?>
														</span>

                                    <?php if ($post->post_status == 'publish') { ?>
                                        <span class="waa-right">
																		<a class="view-product btn"
                                                                           href="<?php echo get_permalink($post->ID); ?>"
                                                                           target="_blank"><?php _e('View Product', 'waa'); ?>
                                                                            <i class="ui ui-external-link"></i></a>
																</span>
                                    <?php } ?>

                                    <?php if ($_visibility == 'hidden') { ?>
                                        <span class="waa-right waa-label waa-label-default waa-product-hidden-label"><i
                                                class="ui ui-eye-slash"></i> <?php _e('Hidden', 'waa'); ?></span>
                                    <?php } ?>

                                <?php endif ?>
                            </h1>
                        </header><!-- .entry-header -->


                        <div class="product-set-type">

                            <div class="col-md-4 col-md-offset-1 product-type-box" id="sell-original-btn">
                                <span>Original verkaufen</span>

                                <div class="type-desc">
                                    <?= __('Falls du das Original-Kunstwerk verkaufen möchtest, wähle diese Option aus. ', 'waa') ?>
                                </div>
                            </div>

                            <div class="col-md-4 col-md-offset-1 product-type-box" id="sell-prints-btn">
                                <span>Prints verkaufen</span>

                                <div class="type-desc">
                                    <?= __('Du möchtest Abzüge von deinem Kunstwerk verkaufen? Dann klicke hier.', 'waa') ?>
                                </div>

                            </div>

                        </div>

                        <div class="product-edit-new-container" style="display: none">
                            <?php if (waa_Template_Shortcodes::$errors) { ?>
                                <div class="waa-alert waa-alert-danger">
                                    <a class="waa-close" data-dismiss="alert">&times;</a>
                                    <?php
                                    foreach (waa_Template_Shortcodes::$errors as $error) { ?>

                                        <strong><?php _e('Error!', 'waa'); ?></strong> <?php echo $error ?>.<br>

                                    <?php } ?>
                                </div>
                            <?php } ?>

                            <?php if (isset($_GET['message']) && $_GET['message'] == 'success') { ?>
                                <div class="waa-message">
                                    <button type="button" class="waa-close" data-dismiss="alert">&times;</button>
                                    <strong><?php _e('Success!', 'waa'); ?></strong> <?php _e('The product has been saved successfully.', 'waa'); ?>

                                    <?php if ($post->post_status == 'publish') { ?>
                                        <a href="<?php echo get_permalink($post_id); ?>"
                                           target="_blank"><?php _e('View Product &rarr;', 'waa'); ?></a>
                                    <?php } ?>
                                </div>
                            <?php } ?>

                            <?php
                            $can_sell = apply_filters('waa_can_post', true);

                            if ($can_sell) {

                                if (waa_is_seller_enabled(get_current_user_id())) { ?>

                                    <form class="waa-product-edit-form" role="form" method="post">

                                        <input name="waa_product_type" id="waa_product_type" type="hidden"
                                               value="<?= isset($waa_product_type) ? $waa_product_type : 'default' ?>"/>
                                        <?php if ($post_id): ?>
                                            <?php do_action('waa_product_data_panel_tabs'); ?>
                                        <?php endif; ?>
                                        <?php do_action('waa_product_edit_before_main'); ?>

                                        <div class="waa-form-top-area">

                                            <div class="content-half-part">

                                                <div class="waa-form-group">
                                                    <input type="hidden" name="waa_product_id"
                                                           value="<?php echo $post_id; ?>">
                                                    <input type="hidden" name="_discounted_price" value="no">

                                                    <label for="post_title"
                                                           class="form-label"><?php _e('Title', 'waa'); ?></label>
                                                    <?php waa_post_input_box($post_id, 'post_title', array('placeholder' => __('Product name..', 'waa'), 'value' => $post_title, 'required' => true)); ?>
                                                </div>

                                                <?php if (waa_get_option('product_category_style', 'waa_selling', 'single') == 'single'): ?>
                                                    <div class="waa-form-group">
                                                        <label for="product_cat"
                                                               class="form-label"><?php _e('Category', 'waa'); ?></label>
                                                        <?php
                                                        $product_cat = ($_POST['product_cat'] ? $_POST['product_cat'] : -1);
                                                        $term = array();
                                                        $term = wp_get_post_terms($post_id, 'product_cat', array('fields' => 'ids'));

                                                        if ($term) {
                                                            $product_cat = reset($term);
                                                        }

                                                        wp_dropdown_categories(array(
                                                            'show_option_none' => __('- Select a category -', 'waa'),
                                                            'hierarchical' => 1,
                                                            'hide_empty' => 0,
                                                            'name' => 'product_cat',
                                                            'id' => 'product_cat',
                                                            'taxonomy' => 'product_cat',
                                                            'title_li' => '',
                                                            'class' => 'product_cat waa-form-control chosen',
                                                            'exclude' => '',
                                                            'selected' => $product_cat,
                                                        ));
                                                        ?>
                                                    </div>
                                                <?php elseif (waa_get_option('product_category_style', 'waa_selling', 'single') == 'multiple'): ?>
                                                    <div class="waa-form-group waa-list-category-box">
                                                        <h5><?php _e('Choose a category', 'waa'); ?></h5>
                                                        <ul class="waa-checkbox-cat">
                                                            <?php
                                                            $term = array();
                                                            $term = wp_get_post_terms($post_id, 'product_cat', array('fields' => 'ids'));

                                                            include_once waa_LIB_DIR . '/class.category-walker.php';
                                                            wp_list_categories(array(
                                                                'walker' => new waaCategoryWalker(),
                                                                'title_li' => '',
                                                                'id' => 'product_cat',
                                                                'hide_empty' => 0,
                                                                'taxonomy' => 'product_cat',
                                                                'hierarchical' => 1,
                                                                'selected' => $term
                                                            ));
                                                            ?>
                                                        </ul>
                                                    </div>
                                                <?php endif; ?>

                                                <div class="waa-clearfix waa-form-group">
                                                    <input name="waa_only_print" id="waa_only_print"
                                                           value="yes"
                                                           type="checkbox" <?php checked($waa_only_print, 'yes'); ?>
                                                           style="display: none">

                                                    <div class="hide_if_only_print waa-clearfix" style="display:none">
                                                        <div class="waa-form-group waa-clearfix waa-price-container">
                                                            <div class="regular-price">
                                                                <label for="_regular_price"
                                                                       class="form-label"><?php _e('Price', 'waa'); ?></label>

                                                                <div class="waa-input-group">
                                                                <span
                                                                    class="waa-input-group-addon"><?php echo get_woocommerce_currency_symbol(); ?></span>
                                                                    <?php waa_post_input_box($post_id, '_regular_price', array('placeholder' => __('0.00', 'waa'), 'value' => $_regular_price), 'number', true); ?>
                                                                    <input type="hidden" name="waa_currency"
                                                                           value="<?= get_woocommerce_currency_symbol(); ?>"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div><!-- .content-half-part -->

                                            <div class="content-half-part featured-image">

                                                <div class="waa-feat-image-upload">
                                                    <?php
                                                    if (isset($featured_image) && $featured_image != 0) {
                                                        $wrap_class = '';
                                                        $instruction_class = ' waa-hide';
                                                        $feat_image_id = 0;
                                                    } else {
                                                        $wrap_class = ' waa-hide';
                                                        $instruction_class = '';
                                                        $feat_image_id = 0;
                                                    }

                                                    if (has_post_thumbnail($post_id)) {
                                                        $wrap_class = '';
                                                        $instruction_class = ' waa-hide';
                                                        $feat_image_id = get_post_thumbnail_id($post_id);
                                                    }
                                                    ?>

                                                    <div class="instruction-inside<?php echo $instruction_class; ?>">
                                                        <input type="hidden" name="feat_image_id"
                                                               class="waa-feat-image-id"
                                                               value="<?= ($featured_image ? $featured_image : $feat_image_id); ?>">

                                                        <i class="ui ui-cloud-upload"></i>
                                                        <a href="#"
                                                           class="waa-feat-image-btn btn btn-sm"><?php _e('Upload a product cover image', 'waa'); ?></a>

                                                        <p><?= __('Das Bild sollte mindestens 800x800 Pixel groß sein.', 'waa') ?></p>
                                                    </div>

                                                    <div class="image-wrap<?php echo $wrap_class; ?>">
                                                        <a class="close waa-remove-feat-image">&times;</a>
                                                        <?php if ($feat_image_id) { ?>
                                                            <?php echo get_the_post_thumbnail($post_id, apply_filters('single_product_large_thumbnail_size', 'shop_single'), array('height' => '', 'width' => '')); ?>
                                                        <?php } else { ?>

                                                            <div
                                                                class="instruction-inside<?php echo $instruction_class; ?>"
                                                                id="image_size_warning" style="display:none">
                                                                <i class="ui ui-cloud-upload"></i>
                                                                <a href="#"
                                                                   class="waa-feat-image-btn btn btn-sm"><?php _e('Upload a product cover image', 'waa'); ?></a>

                                                                <p><span
                                                                        style="font-weight:bold; color:red;"><?= __('Das Bild ist zu klein!', 'waa') ?></span><br><?= __('Das Bild sollte mindestens 800x800 Pixel groß sein.', 'waa') ?>
                                                                </p>
                                                            </div>
                                                            <img height="" width=""
                                                                 src="<?= ($featured_image ? wp_get_attachment_url($featured_image) : ''); ?>"
                                                                 alt="">
                                                        <?php } ?>
                                                    </div>
                                                </div><!-- .waa-feat-image-upload -->

                                                <div class="waa-product-gallery">
                                                    <div class="waa-side-body" id="waa-product-images">
                                                        <div id="product_images_container">
                                                            <ul class="product_images waa-clearfix">
                                                                <?php
                                                                $product_images = get_post_meta($post_id, '_product_image_gallery', true);
                                                                $gallery = explode(',', $product_images);

                                                                if ($gallery) {
                                                                    foreach ($gallery as $image_id) {
                                                                        if (empty($image_id)) {
                                                                            continue;
                                                                        }

                                                                        $attachment_image = wp_get_attachment_image_src($image_id, 'thumbnail');
                                                                        ?>
                                                                        <li class="image"
                                                                            data-attachment_id="<?php echo $image_id; ?>">
                                                                            <img
                                                                                src="<?php echo $attachment_image[0]; ?>"
                                                                                alt="">
                                                                            <a href="#" class="action-delete"
                                                                               title="<?php esc_attr_e('Delete image', 'waa'); ?>">&times;</a>
                                                                        </li>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </ul>

                                                            <input type="hidden" id="product_image_gallery"
                                                                   name="product_image_gallery"
                                                                   value="<?php echo esc_attr($product_images); ?>">
                                                        </div>
                                                    </div>
                                                </div> <!-- .product-gallery -->
                                            </div><!-- .content-half-part -->
                                        </div><!-- .waa-form-top-area -->

                                        <div class="waa-product-description">
                                            <label for="post_content"
                                                   class="form-label"><?php _e('Description', 'waa'); ?> <span
                                                    class="waa-tooltips-help tips" title=""
                                                    data-original-title="<?= __('Beschreibe dein Kunstwerk möglichst genau. Was hat dich bei deiner Arbeit inspiriert? Welche Emotionen möchtest du beim Betrachter erwecken?', 'waa') ?>">
																			<i class="ui ui-question-circle"></i>
																		</span></label>
                                            <textarea name="post_content"
                                                      style="width:100%; height:150px"><?= $post_content ?></textarea>
                                        </div>

                                        <div class="waa-form-group tag-group">
                                            <label for="product_tag"
                                                   class="form-label"><?php _e('Tags', 'waa'); ?></label>
                                            <?php
                                            require_once waa_LIB_DIR . '/class.tag-walker.php';
                                            $term = ($product ? wp_get_post_terms($post_id, 'product_tag', array('fields' => 'ids')) : $_POST['product_tag']);
                                            $selected = ($term) ? $term : array();
                                            $drop_down_tags = wp_dropdown_categories(array(
                                                'show_option_none' => __('', 'waa'),
                                                'hierarchical' => 1,
                                                'hide_empty' => 0,
                                                'name' => 'product_tag[]',
                                                'id' => 'product_tag',
                                                'taxonomy' => 'product_tag',
                                                'title_li' => '',
                                                'class' => 'product_tags waa-form-control chosen',
                                                'exclude' => '',
                                                'selected' => $selected,
                                                'echo' => 0,
                                                'walker' => new waa_Walker_Tag_Multi()
                                            ));

                                            echo str_replace('<select', '<select data-placeholder="' . __('Select product tags', 'waa') . '" multiple="multiple" ', $drop_down_tags);

                                            ?>
                                        </div>


                                        <div
                                            class="waa-edit-row waa-clearfix waa-variation-container hide-if-sell-original">

                                            <?php
                                            $current_user = get_current_user_id();
                                            $profile_info = waa_get_store_info($current_user);
                                            $city_term = get_term_by('id', sanitize_title($profile_info['region']), 'pa_stadt');
                                            ?>
                                            <input type="hidden" name="waa_region_val" value="<?= $city_term->name; ?>">

                                            <label class="form-label" for="_has_attribute">
                                                <input name="_has_attribute" value="no" type="hidden">
                                                <input name="_has_attribute" id="_has_attribute" value="yes"
                                                       type="checkbox"
                                                       style="display:none" <?php checked($_create_variations, 'yes'); ?>>
                                                
                                            </label>
																						<?= __('Biete Prints in verschiedenen Größen und Ausführungen an. Eine Zeile steht für eine Printvariante.', 'waa') ?>

                                            <?php if ($_create_variations != 'yes'): ?>
                                                <div class="waa-side-body waa-attribute-content-wrapper waa-hide">
                                                    <input type="hidden" value="print_groesse"
                                                           class="waa-form-control waa-attribute-option-name-label"
                                                           data-attribute_name="print_groesse">
                                                    <input type="hidden" name="attribute_names[]"
                                                           value="pa_print_groesse" class="waa-attribute-option-name">
                                                    <input type="hidden" name="attribute_is_taxonomy[]" value="1">
                                                    <input type="hidden" name="attribute_values[]" value="">
                                                    <input type="hidden" value="print_material"
                                                           class="waa-form-control waa-attribute-option-name-label"
                                                           data-attribute_name="print_material">
                                                    <input type="hidden" name="attribute_names[]"
                                                           value="pa_print_material" class="waa-attribute-option-name">
                                                    <input type="hidden" name="attribute_is_taxonomy[]" value="1">
                                                    <input type="hidden" name="attribute_values[]" value="">
                                                    <input type="hidden" name="_create_variation" id="_create_variation"
                                                           value="no">
                                                    <input type="hidden" name="waa_create_new_variations"
                                                           id="waa_create_new_variations" value="no">

                                                    <!-- create "original" variation -->
                                                    <input type="hidden" name="attribute_pa_print_groesse[]"
                                                           value="original">
                                                    <input type="hidden" name="attribute_pa_print_material[]"
                                                           value="original">
                                                    <input type="hidden" name="variation_menu_order[]" value="0">
                                                    <input type="hidden" name="variable_enabled[]" value="yes">
                                                    <input type="hidden" name="variable_sku[]">
                                                    <input type="hidden" name="variable_shipping_price_DE[]">
                                                    <input type="hidden" name="variable_shipping_price_everywhere[]">
                                                    <input type="hidden" name="variable_shipping_price_CH[]">
                                                    <input type="hidden" name="variable_shipping_price_AT[]">
                                                    <input type="hidden" name="variable_regular_price[]"
                                                           id="original-price"
                                                           value="<?= ($_regular_price ? $_regular_price : '') ?>">

                                                    <table class="waa-table">
                                                        <thead>
                                                        <tr>
                                                            <th width="15%"><?= __('Variant', 'waa') ?></th>
                                                            <th width="25%"><?= __('Material / Ausführung', 'waa') ?></th>
                                                            <th width="15%"><?= __('Price', 'waa') ?>
                                                                (in <?= get_woocommerce_currency_symbol(); ?>)
                                                            </th>
																														
																														<?php
																															$dps_country_rates = get_user_meta($user_id, '_dps_country_rates', true);
																															$_additional_price = get_post_meta($post_id, '_additional_price', true);
																															foreach ($dps_country_rates as $country => $cost) { ?>
																																	<th width="15%">
																																		<span	
																																						class="hide-if-sell-both">
																																						 <?php
																																						 if(get_user_meta($user_id, '_dps_form_location', true) == $country) {
																																								 _e('Versand (inland)', 'waa');
																																						 } elseif($country == 'everywhere') {
																																								 _e('Versand (EU)', 'waa'); }
																																						 elseif($country == 'CH') {
																																								 _e('Versand (CH)', 'waa'); }
																																						 elseif($country == 'DE') {
																																								 _e('Versand (DE)', 'waa'); } ?>
																																						</span>                                                                                
																																	</th>
                                                             <?php } ?>
																						
                                                            <th width="5%"></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr class="print-variation">
                                                            <td>
                                                                <input type="text" name="attribute_pa_print_groesse[]"
                                                                       class="print-size" maxlength="14" value=""
                                                                       placeholder="<?= __('z.B. 60x40', 'waa'); ?>">
                                                                <span
                                                                    id="print-size-alert"><?= __('Verwende folgendes Format: 60x40', 'waa') ?></span>
                                                                <input type="hidden" name="variation_menu_order[]"
                                                                       value="0">
                                                                <input type="hidden" name="variable_enabled[]"
                                                                       value="yes">
                                                            </td>
                                                            <td>
                                                                <input type="text" name="attribute_pa_print_material[]"
                                                                       value=""
                                                                       placeholder="<?= __('z.B. Hochglanzpapier', 'waa'); ?>">
                                                            </td>
                                                            <td class="">

                                                                <input type="number" name="variable_regular_price[]"
                                                                       placeholder="0,00" class="waa-form-control"
                                                                       min="0" step="any">
                                                                <input type="hidden" name="variable_sku[]"
                                                                       placeholder="SKU" class="waa-form-control">
                                                            </td>
																														
																														
																														
																														<?php foreach ($dps_country_rates as $country => $cost) { ?>
																														<td class="hide-if-sell-both">
																																<input
																																	name="variable_shipping_price_<?= $country; ?>[]"
																																	placeholder="0,00"
																																	min="0"
																																	class="waa-form-control"
																																	type="number"
																																	step="any">
																															</td>
																														 <?php } ?>
																														
                                                            <td>
                                                                <a href="#" class="btn-remove-new-print"><i
                                                                        class="ui ui-trash-o"></i></a>
                                                            </td>
                                                        </tr>
                                                        <tr class="add-print-btn">
                                                            <td colspan="6"><a href="#" class="btn-add-print"
                                                                               id="add-row"><i class="ui ui-plus"></i>
                                                                    &nbsp;<?= __('Add Variation', 'waa'); ?></a><br><br>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="waa-variation-content-wrapper"></div>

                                            <?php elseif ($_create_variations == 'yes'): ?>
                                                <input type="hidden" id="_create_variation" name="_create_variation"
                                                       value="yes"/>
                                                <?php include_once 'edit/load_variation_template.php'; ?>

                                                <?php if ($post_id): ?>
                                                    <?php do_action('waa_product_edit_after_variations'); ?>
                                                <?php endif; ?>
                                                <label class="form-label hide-if-sell-prints hide-if-sell-original"
                                                       for="waa_only_print">
                                                    <input name="waa_only_print" id="waa_only_print" value="yes"
                                                           type="checkbox" <?php checked($waa_only_print, 'yes'); ?>>
                                                    <?= __('Ich möchte das Original nicht verkaufen.', 'waa') ?>
                                                </label>
                                                <div class="waa-divider-top"></div>
                                                <input type="hidden" name="_variation_product_update"
                                                       value="<?php esc_attr_e('yes', 'waa'); ?>">
                                            <?php endif ?>
                                        </div><!-- .waa-divider-top -->


                                        <?php do_action('waa_new_product_form'); ?>
                                        <?php if ($post_id): ?>
                                            <?php do_action('waa_product_edit_after_main'); ?>
                                        <?php endif; ?>
                                        <div
                                            class="waa-product-inventory waa-edit-row waa-clearfix hide-if-sell-original hide-if-sell-both hide-if-sell-prints">
                                            <div class="waa-side-left">
                                                <h2><?php _e('Inventory & Variants', 'waa'); ?></h2>

                                                <p>
                                                    <?php _e('Manage inventory, and configure the options for selling this product.', 'waa'); ?>
                                                </p>
                                            </div>

                                            <div class="waa-side-right">
                                                <div class="waa-form-group hide_if_variation">
                                                    <?php waa_post_input_box($post_id, '_manage_stock', array('label' => __('Enable product stock management', 'waa'), 'value' => $_manage_stock), 'checkbox'); ?>
                                                </div>

                                                <div
                                                    class="show_if_stock waa-stock-management-wrapper waa-form-group waa-clearfix">

                                                    <div class="waa-w3 hide_if_variation">
                                                        <label for="_stock"
                                                               class="waa-form-label"><?php _e('Quantity', 'waa'); ?></label>
                                                        <input type="number" name="_stock" id="_stock"
                                                               placeholder="<?php __('1', 'waa'); ?>"
                                                               value="<?php echo wc_stock_amount($_stock); ?>" min="0"
                                                               step="1">
                                                    </div>

                                                    <div class="waa-w3 hide_if_variation">
                                                        <label for="_stock_status"
                                                               class="waa-form-label"><?php _e('Stock Status', 'waa'); ?></label>

                                                        <?php waa_post_input_box($post_id, '_stock_status', array('options' => array(
                                                            'instock' => __('In Stock', 'waa'),
                                                            'outofstock' => __('Out of Stock', 'waa'),
                                                        ), 'value' => $_stock_status), 'select'); ?>
                                                    </div>

                                                    <div class="waa-w3 hide_if_variation">
                                                        <label for="_backorders"
                                                               class="waa-form-label"><?php _e('Allow Backorders', 'waa'); ?></label>

                                                        <?php waa_post_input_box($post_id, '_backorders', array('options' => array(
                                                            'no' => __('Do not allow', 'waa'),
                                                            'notify' => __('Allow but notify customer', 'waa'),
                                                            'yes' => __('Allow', 'waa')
                                                        ), 'value' => $_backorders), 'select'); ?>
                                                    </div>
                                                </div><!-- .show_if_stock -->

                                                <div class="waa-form-group">
                                                    <?php waa_post_input_box($post_id, '_sold_individually', array('label' => __('Allow only one quantity of this product to be bought in a single order', 'waa'), 'value' => $_sold_individually), 'checkbox'); ?>
                                                </div>

                                                <?php if ($post_id): ?>
                                                    <?php do_action('waa_product_edit_after_inventory'); ?>
                                                <?php endif; ?>



                                                <?php if ($post_id): ?>
                                                    <?php do_action('waa_product_edit_after_downloadable'); ?>
                                                <?php endif; ?>
                                                <?php if ($post_id): ?>
                                                    <?php do_action('waa_product_edit_after_sidebar'); ?>
                                                <?php endif; ?>
                                                <!-- <div class="waa-divider-top"></div> -->
                                            </div><!-- .waa-side-right -->
                                        </div><!-- .waa-product-inventory -->

                                        <?php if ($post_id): ?>
                                            <?php do_action('waa_product_options_shipping_before'); ?>
                                        <?php endif; ?>

                                        <?php if ('yes' == get_option('woocommerce_calc_shipping') || 'yes' == get_option('woocommerce_calc_taxes')): ?>
                                            <div
                                                class="waa-product-shipping-tax waa-edit-row waa-clearfix hide-if-sell-prints<?php echo ('no' == get_option('woocommerce_calc_shipping')) ? 'woocommerce-no-shipping' : '' ?> <?php echo ('no' == get_option('woocommerce_calc_taxes')) ? 'woocommerce-no-tax' : '' ?>">
                                                <div class="waa-side-left">
                                                    <h2><?php _e('Shipping & Tax', 'waa'); ?></h2>

                                                    <p>
                                                        <?php _e('Manage shipping and tax for this product', 'waa'); ?><br>
                                                        <strong><?php echo __('Angaben in', 'waa') . ' ' . get_woocommerce_currency_symbol();?> <?php _e('inkl. MwSt.', 'waa'); ?></strong>
                                                    </p>
                                                </div>

                                                <div class="waa-side-right">
                                                    <?php if ('yes' == get_option('woocommerce_calc_shipping')): ?>
                                                        <div
                                                            class="waa-clearfix hide_if_downloadable waa-shipping-container">
                                                            <input type="hidden" name="product_shipping_class"
                                                                   value="0">
                                                            <input type="checkbox" id="_disable_shipping"
                                                                   name="_disable_shipping" <?php checked($_disable_shipping, 'no'); ?>>

                                                            <?php if ($post_id): ?>
                                                                <?php do_action('waa_product_options_shipping'); ?>
                                                            <?php endif; ?>

                                                            <div class="waa-shipping-product-options">
                                                                <input name="_overwrite_shipping"
                                                                       id="_overwrite_shipping" value="yes"
                                                                       type="checkbox" checked="checked"
                                                                       style="display:none">

                                                                <div class="waa-form-group show_if_override">
                                                                    <div class="row">
                                                                        <?php
                                                                        $dps_country_rates = get_user_meta($user_id, '_dps_country_rates', true);
                                                                        $_additional_price = get_post_meta($post_id, '_additional_price', true);
                                                                        foreach ($dps_country_rates as $country => $cost) { ?>

                                                                            <div class="col-md-4">
                                                                                <input name="dps_country_to[]"
                                                                                       type="hidden"
                                                                                       value="<?= $country; ?>"/>

                                                                                <label class="waa-control-label"
                                                                                       for="dps_country_to_price[]"><span
                                                                                        class="hide-if-sell-both">
                                                                                         <?php
                                                                                         if(get_user_meta($user_id, '_dps_form_location', true) == $country) {
                                                                                             _e('Nationaler Versand', 'waa');
                                                                                         } elseif($country == 'everywhere') {
                                                                                             _e('Versand in die EU', 'waa'); }
                                                                                         elseif($country == 'CH') {
                                                                                             _e('Versand in die Schweiz', 'waa'); }
                                                                                         elseif($country == 'DE') {
                                                                                             _e('Versand nach Deutschland', 'waa'); } ?>
                                                                                        </span>

                                                                                </label>
                                                                                <input
                                                                                    value="<?php echo $_additional_price[$country] ?>"
                                                                                    name="dps_country_to_price[]"
                                                                                    id="dps_country_to_price"
                                                                                    placeholder="z.B. 9,99"
                                                                                    min="0"
                                                                                    class="waa-form-control"
                                                                                    type="number"
                                                                                    step="any">
                                                                            </div>
                                                                        <?php } ?>
                                                                    </div>
                                                                </div>

                                                                <div
                                                                    class="waa-form-group hide-if-sell-original">
                                                                    <label class="waa-control-label"
                                                                           for="dps_additional_qty"><?php _e('Per Qty Additional Price', 'waa'); ?>
                                                                       <span
                                                                            class="waa-tooltips-help tips" title=""
                                                                            data-original-title="<?= __('Falls ein Kunde mehr als nur einen Print kaufen möchte, kannst du hier festlegen, wie viel zusätzliche Versandkosten dafür anfallen sollen.', 'waa') ?>">
																			<i class="ui ui-question-circle"></i>
																		</span></label>
                                                                    <input id="additional_qty"
                                                                           value="<?php echo ($_additional_qty) ? $_additional_qty : $dps_additional_qty; ?>"
                                                                           name="_additional_qty"
                                                                           min="0"
                                                                           placeholder="z.B. 1,99"
                                                                           class="waa-form-control" type="number"
                                                                           step="any">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if ('yes' == get_option('woocommerce_calc_shipping') && 'yes' == get_option('woocommerce_calc_taxes')): ?>

                                                    <?php endif ?>

                                                </div><!-- .waa-side-right -->
                                            </div><!-- .waa-product-inventory -->
                                        <?php endif; ?>

                                        <?php if ($post_id): ?>
                                            <?php do_action('waa_product_edit_after_shipping'); ?>
                                        <?php endif; ?>






                                        <div class="waa-other-options waa-edit-row waa-clearfix hide-if-sell-prints">
                                            <div class="waa-side-left">
                                                <h2><?php _e('Dimensions', 'waa'); ?></h2>
                                                <p>
                                                    <?php _e('Bitte gib hier die Maße deines Kunstwerks an.', 'waa'); ?><br>
                                                </p>
                                            </div>

                                            <div class="waa-side-right">
                                                <div
                                                    class="waa-clearfix hide_if_downloadable waa-shipping-container">

                                                <div
                                                    class="waa-shipping-dimention-options">
                                                    <?php waa_post_input_box($post_id, '_weight', array('class' => '', 'placeholder' => __('Gewicht (' . esc_html(get_option('woocommerce_weight_unit')) . ')', 'waa'), 'value' => $_weight), 'number'); ?>
                                                    <?php waa_post_input_box($post_id, '_length', array('class' => '', 'placeholder' => __('L&auml;nge (' . esc_html(get_option('woocommerce_dimension_unit')) . ')', 'waa'), 'value' => $_length), 'number'); ?>
                                                    <?php waa_post_input_box($post_id, '_width', array('class' => '', 'placeholder' => __('Breite (' . esc_html(get_option('woocommerce_dimension_unit')) . ')', 'waa'), 'value' => $_width), 'number'); ?>
                                                    <?php waa_post_input_box($post_id, '_height', array('class' => '', 'placeholder' => __('H&ouml;he (' . esc_html(get_option('woocommerce_dimension_unit')) . ')', 'waa'), 'value' => $_height), 'number'); ?>
                                                    <div class="waa-clearfix"></div>
                                                </div>

                                            </div>
                                                </div>
                                        </div><!-- .waa-other-options -->



                                        <div class="waa-other-options waa-edit-row waa-clearfix">
                                            <div class="waa-side-left">
                                                <h2><?php _e('Other Options', 'waa'); ?></h2>
                                            </div>

                                            <div class="waa-side-right">
                                                <?php if ($post_id): /* ?>
																						<div class="waa-form-group">
																								<label for="post_status" class="form-label"><?php _e( 'Product Status', 'waa' ); ?></label>
																								<?php if ( $post_status != 'pending' ) { ?>
																										<?php $post_statuses = apply_filters( 'waa_post_status', array(
																												'publish' => __( 'Online', 'waa' ),
																												'draft'   => __( 'Draft', 'waa' )
																										), $post ); ?>

																										<select id="post_status" class="waa-form-control" name="post_status">
																												<?php foreach ( $post_statuses as $status => $label ) { ?>
																														<option value="<?php echo $status; ?>"<?php selected( $post_status, $status ); ?>><?php echo $label; ?></option>
																												<?php } ?>
																										</select>
																								<?php } else { ?>
																										<?php $pending_class = $post_status == 'pending' ? '  waa-label waa-label-warning': ''; ?>
																										<span class="waa-toggle-selected-display<?php echo $pending_class; ?>"><?php echo waa_get_post_status( $post_status ); ?></span>
																								<?php } ?>
																						</div>
																				<?php */
                                                endif ?>

                                                <input type="hidden" name="_visibility" value="visible"/>


                                                <div class="waa-form-group">
                                                    <label for="_purchase_note"
                                                           class="form-label"><?php _e('Purchase Note', 'waa'); ?></label>
                                                    <?php waa_post_input_box($post_id, '_purchase_note', array('placeholder' => __('Customer will get this info in their order email', 'waa'), 'value' => $_purchase_note), 'textarea'); ?>
                                                </div>
                                            </div>
                                        </div><!-- .waa-other-options -->

                                        <?php if ($post_id): ?>
                                            <?php do_action('waa_product_edit_after_options'); ?>
                                        <?php endif; ?>

                                        <?php wp_nonce_field('waa_add_new_product', 'waa_add_new_product_nonce'); ?>
                                        <input type="submit" name="waa_add_product"
                                               class="waa-btn waa-btn-theme waa-btn-lg btn-block"
                                               value="<?php esc_attr_e('Save Product', 'waa'); ?>"/>

                                    </form>

                                <?php } else { ?>

                                    <?php waa_seller_not_enabled_notice(); ?>

                                <?php } ?>

                            <?php } else { ?>

                                <?php do_action('waa_can_post_notice'); ?>

                            <?php } ?>
                        </div> <!-- #primary .content-area -->
                    </div>
                </div><!-- .waa-dashboard-wrap -->
                <div class="waa-clearfix"></div>

                <?php
                if ($post_id) {
                    ?>
                    <div class="variation-single-content">
                        <?php include_once 'edit/vatiation-popup.php'; ?>
                    </div>
                    <?php
                }
                ?>
            </div>
            <?php else: ?>
            <div class="waa-dashboard-content waa-product-edit">
                <header class="waa-dashboard-header waa-clearfix">
                    <h1 class="entry-title">
                        Neues Produkt anlegen
                    </h1>
                </header><!-- .entry-header -->

                <div class="product-edit-new-container">
                    <div class="waa-alert waa-alert-danger">
                        <a class="waa-close" data-dismiss="alert">&times;</a>
                        <strong><?php _e('Error!', 'waa'); ?></strong> <?php printf(__('Bevor du dein erstes Kunstwerk verkaufen kannst, <a href="%s">gebe bitte deine Versanddetails an</a>.', 'waa'), waa_get_navigation_url('settings/shipping')); ?>
                        <br>
                    </div>
                </div> <!-- #primary .content-area -->
            </div>
        </div><!-- .waa-dashboard-wrap -->
        <div class="waa-clearfix"></div>
    </div>
    <?php endif; ?>


    <script type="text/html" id="tmpl-waa-single-attribute">
        <div id="doakn-single-attribute-wrapper" class="white-popup">
            <form action="" method="post" id="doakn-single-attribute-form">
                <table class="waa-table waa-single-attribute-options-table">
                    <thead>
                    <tr>
                        <th width="10%"><?php _e('Option Name', 'waa') ?></th>
                        <th width="90%"><?php _e('Option Values', 'waa') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <# if ( !_.isNull( data.attribute_data ) ){ #>
                        <# _.each( data.attribute_data, function( attr_val, attr_key ) { #>
                            <# if ( attr_val.is_variation ) { #>
                                <tr class="waa-single-attribute-options">
                                    <td class="{{attr_val.data_attr_name}}  {{attr_val.is_variation}}">
                                        <# if ( attr_val.is_taxonomy ) { #>
                                            {{ attr_val.label }}<input type="hidden"
                                                                       value="{{ attr_val.label }}"
                                                                       class="waa-form-control waa-single-attribute-option-name-label"
                                                                       data-attribute_name="{{attr_val.data_attr_name}}">
                                            <input type="hidden" name="attribute_names[]"
                                                   value="{{attr_val.name}}"
                                                   class="waa-single-attribute-option-name">
                                            <input type="hidden" name="attribute_is_taxonomy[]" value="1">
                                            <# } else { #>
                                                <input type="text" name="attribute_names[]"
                                                       value="{{attr_val.name}}"
                                                       class="waa-form-control waa-single-attribute-option-name">
                                                <input type="hidden" name="attribute_is_taxonomy[]"
                                                       value="0">
                                                <# } #>
                                    </td>
                                    <td>
                                        <# if ( attr_val.is_taxonomy ) { #>
                                            <input type="text" name="attribute_values[]"
                                                   value="{{ attr_val.term_value.replace(/\|/g, ',' ).replace('Original', '') }}"
                                                   class="waa-form-control waa-single-attribute-option-values">
                                            <# } else { #>
                                                <input type="text" name="attribute_values[]"
                                                       value="{{ attr_val.value.replace(/\|/g, ',' ) }}"
                                                       class="waa-form-control waa-single-attribute-option-values">
                                                <# } #>
                                    </td>
                                </tr>
                                <# } #>
                                    <# }) #>
                                        <# } else { #>
                                            <tr colspan="3" class="waa-single-attribute-options">
                                                <td width="20%">
                                                    <input type="text" name="attribute_names[]" value=""
                                                           class="waa-form-control waa-single-attribute-option-name">
                                                    <input type="hidden" name="attribute_is_taxonomy[]"
                                                           value="0">
                                                </td>
                                                <td><input type="text" name="attribute_values[]" value=""
                                                           class="waa-form-control waa-single-attribute-option-values">
                                                </td>
                                            </tr>
                                            <# } #>
                    </tbody>
                </table>
                <input type="hidden" name="product_id" value="<?php echo $post_id ?>">
                <input type="submit" class="waa-btn waa-btn-theme waa-right"
                       name="waa_new_attribute_option_save"
                       value="<?php esc_attr_e('Save', 'waa'); ?>">
                <span class="waa-loading waa-save-single-attr-loader waa-hide"></span>

                <div class="waa-clearfix"></div>
            </form>
        </div>
    </script>
    <?php

    wp_reset_postdata();

    if (!$from_shortcode) {
        get_footer();
    }
    ?>

