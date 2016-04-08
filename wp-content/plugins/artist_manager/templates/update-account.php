<?php
/**
 * The Template for update customer to seller.
 *
 * @package waa
 * @package waa - 2014 1.0
 */
$user_id = get_current_user_id();

$f_name = get_user_meta($user_id, 'first_name', true);
$l_name = get_user_meta($user_id, 'last_name', true);

if ($f_name == '') {
    if (isset($_POST['fname'])) {
        $f_name = $_POST['fname'];
    }
}

if ($l_name == '') {
    if (isset($_POST['lname'])) {
        $l_name = $_POST['lname'];
    }
}
$cu_slug = get_user_meta($user_id, 'nickname', true);
?>


<h2><?php _e('Update account to Seller', 'waa'); ?></h2>
<form method="post" action="">

    <div class="waa-become-seller">
        <fieldset>
            <div class="split-row form-row-wide">
                <p class="form-row form-group">
                    <label for="first-name"><?php _e('First Name', 'waa'); ?> <span class="required">*</span></label>
                    <input type="text" class="input-text" name="fname" id="first-name"
                           value="<?php if (!empty($f_name)) echo esc_attr($f_name); ?>" required="required"/>
                </p>

                <p class="form-row form-group">
                    <label for="last-name"><?php _e('Last Name', 'waa'); ?> <span class="required">*</span></label>
                    <input type="text" class="input-text" name="lname" id="last-name"
                           value="<?php if (!empty($l_name)) echo esc_attr($l_name); ?>" required="required"/>
                </p>
            </div>

            <div class="form-group">
                <label for="waa_user_country"><?php _e('Land', 'waa'); ?> <span class="required">*</span></label>
                <?php
                global $woocommerce;
                $countries_obj = new WC_Countries();
                $countries = $countries_obj->get_allowed_countries();
                ?>
                <select id="waa_user_country" name="waa_user_country" class="select required" required="required">
                    <option value=""><?= __('Bitte wählen', 'waa') ?></option>
                    <?php
                    foreach ($countries as $key => $value) {
                        echo '<option value="'.$key.'">'.$value.'</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="form-group" id="region-select-container">
                <label for="waa_user_region"><?php _e('Region wählen', 'waa'); ?></label>

                <p class="form-row" id="waa_user_region_field"></p>
            </div>


            <p class="form-row form-group form-row-wide">
                <label for="company-name"><?php _e('Shop Name', 'waa'); ?> <span class="required">*</span></label>
                <input type="text" class="input-text" name="shopname" id="company-name"
                       value="<?php if (!empty($_POST['shopname'])) echo esc_attr($_POST['shopname']); ?>"
                       required="required"/>
            </p>

            <p class="form-row form-group form-row-wide">
                <label for="seller-url" class="pull-left"><?php _e('Shop URL', 'waa'); ?> <span
                        class="required">*</span></label>
                <strong id="url-alart-mgs" class="pull-right"></strong>
                <input type="text" class="input-text" name="shopurl" id="seller-url"
                       value="<?php if (empty ($cu_slug)) {
                           if (!empty($_POST['shopurl'])) echo esc_attr($_POST['shopurl']);
                       } else echo esc_attr($cu_slug); ?>" required="required"/>
                <small><?php echo home_url() . '/' . waa_get_option('custom_store_url', 'waa_selling', 'store'); ?>/<strong id="url-alart"></strong></small>
            </p>

            <p class="form-row form-group form-row-wide">
                <label for="shop-phone"><?php _e('Phone', 'waa'); ?><span class="required">*</span></label>
                <input type="text" class="input-text form-control" name="phone" id="shop-phone"
                       value="<?php if (!empty($_POST['phone'])) echo esc_attr($_POST['phone']); ?>"
                       required="required"/>
            </p>

            <p class="form-row">
                <?php wp_nonce_field('account_migration', 'waa_nonce'); ?>
                <input type="hidden" name="user_id" value="<?php echo $user_id ?>">
                <input type="submit" class="waa-btn waa-btn-default" name="waa_migration"
                       value="<?php _e('Become a Seller', 'waa'); ?>"/>
            </p>
        </fieldset>
    </div>
</form>
