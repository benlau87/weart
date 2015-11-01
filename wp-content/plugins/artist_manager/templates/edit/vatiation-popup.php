<script type="text/html" id="tmpl-waa-single-variations">
    <div id="variation-edit-popup" class="white-popup">
        <div class="product-variation-single-popup-content">
            <div id="product-variations">
                <form action="" method="post" id="waa-single-variation-form" novalidate>
                    <div class="wc-metaboxes-wrapper" id="variable_product_options">
                        <div id="variable_product_options_inner">
                            <div class="woocommerce_variation wc-metabox closed">
                                <h3 class="ui-sortable-handle">
                                    <# if( !_.isUndefined( data.variation_id ) ) { #>
                                        <strong> #{{ data.variation_id[0] }}&mdash; </strong>
                                    <# } else { #>
                                        <strong><?php _e( 'Add New ', 'waa' );?>&mdash;</strong>
                                    <# } #>

                                    <# _.each( data.variation_attributes[0], function( title, index ) { #>

                                        <select name="attribute_{{index}}[]">
                                            <option value=""><?php _e( 'Any ', 'waa' ); ?>{{title.name}}…</option>
                                                <# _.each( title.term, function( term_val, term_key ) {
                                                        if( !_.isUndefined( data['attribute_' + index ] ) && term_key == data['attribute_' + index ][0] ) {
                                                            var selected = 'selected="selected"';
                                                        } else {
                                                            var selected = '';
                                                        }
                                                     #>
                                                    <option value="{{term_key}}" {{ selected }}>{{term_val}}</option>
                                                <# }); #>
                                        </select>
                                    <# }); #>
                                    <input type="hidden" value="{{ ( !_.isUndefined( data.variation_id ) ) ? data.variation_id[0] : ''  }}" name="variable_post_id[]">
                                    <input type="hidden" value="{{ ( !_.isUndefined( data.post_id ) ) ? data.post_id[0] : ''  }}" name="post_id">
                                </h3>
                                <table cellspacing="0" cellpadding="0" class="woocommerce_variable_attributes wc-metabox-content">
                                    <tbody>
                                        <tr>
                                            <td rowspan="2" class="data">
                                                <table cellspacing="0" cellpadding="0" class="data_table">
                                                    <tbody>
                                                        <?php if ( get_option( 'woocommerce_manage_stock' ) == 'yes' ) : ?>
                                                            <tr class="show_if_variation_manage_stock" style="display: none;">
                                                                <td>
                                                                    <# stock_qty = _.isUndefined( data._stock ) ? '' : data._stock[0]; #>
                                                                    <label><?php _e( 'Stock Qty: ', 'waa' ); ?><a href="#" title="" class="tips" data-original-title="<?php _e( 'Enter a quantity to enable stock management at variation level, or leave blank to use the parent product\'s options.', 'waa' ) ?>">[?]</a></label>
                                                                    <input type="number" step="any" value="{{ stock_qty }}" name="variable_stock[]" size="5">
                                                                </td>
                                                                <td>
                                                                    <label><?php _e( 'Allow Backorders?', 'waa' ); ?></label>
                                                                    <select name="variable_backorders[]" class="variation_select_fileld" data-selected_data="{{( !_.isUndefined( data.variation_backorders ) ) ? data.variation_backorders[0] : ''}}">
                                                                        <option value="no"><?php _e( 'Do not allow', 'waa' ); ?></option>
                                                                        <option value="notify"><?php _e( 'Allow but notify customer', 'waa' ); ?></option>
                                                                        <option value="yes"><?php _e( 'Allow', 'waa' ); ?></option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2">
                                                                    <label><?php _e( 'Stock status', 'waa' ); ?></label>
                                                                    <select name="variable_stock_status[0]" class="variation_select_fileld" data-selected_data="{{ ( !_.isUndefined( data._stock_status ) ) ? data._stock_status[0] : ''}}">
                                                                        <option value="instock"><?php _e( 'In stock', 'waa' ); ?></option>
                                                                        <option value="outofstock"><?php _e( 'Out of stock', 'waa' ) ?></option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                        <?php endif; ?>

                                                        <tr class="variable_pricing">
                                                            <td>
                                                                <label><?php _e( 'Regular Price: ', 'waa' ) ?>(<?php echo get_woocommerce_currency_symbol(); ?>)</label>
                                                                <input type="text" placeholder="<?php _e( 'Variation price (required)', 'waa' ) ?>" class="wc_input_price" value="{{ ( !_.isUndefined( data._regular_price ) ) ? data._regular_price[0] : '' }}" name="variable_regular_price[]" size="5">
                                                            </td>                                                            
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="upload_image">
                                                <span class="variation_placeholder_image" data-placeholder_image="{{data.placeholder_image[0]}}"></span>
                                                <a href="#" class="upload_image_button {{ ( ( !_.isUndefined( data._thumbnail_id ) ) && data._thumbnail_id[0] > 0 ) ? 'waa-img-remove' : '' }}" rel="{{ ( !_.isUndefined( data._variation_id ) ) ? data._variation_id[0] : '' }}">
                                                <img src="{{ ( ( !_.isUndefined( data.thumbnail_url ) && data.thumbnail_url[0] ) ? data.thumbnail_url[0] : '' ) ? data.thumbnail_url[0] : data.placeholder_image[0] }}"; />
                                                <input type="hidden" name="upload_image_id[]" class="upload_image_id" value="{{ ( !_.isUndefined( data._thumbnail_id ) ) ? data._thumbnail_id[0] : '' }}" />
                                                <span class="overlay"></span></a>
                                            </td>
                                            <td class="options">
                                                <label class="checkbox"><input type="checkbox"  {{ ( !_.isUndefined( data.variation_post_status ) && ( data.variation_post_status[0] == 'publish' ) ) ? 'checked="checked"': '' }} name="variable_enabled[]" class="checkbox"> Enabled</label>

                                                <label class="checkbox"><input type="checkbox" {{ ( !_.isUndefined( data._manage_stock ) && data._manage_stock == 'yes' ) ? 'checked="checked"': '' }} name="variable_manage_stock[]" class="checkbox variable_manage_stock"> Manage Stock? <a href="#" title="" class="tips" data-original-title="Enable this option to enable stock management at variation level">[?]</a></label>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <input type="submit" id="waa_save_single_variation" name="waa_save_single_variation" value="<?php _e( 'Save', 'waa' ) ?>" class="waa-btn waa-btn-theme waa-right">
                    <span class="waa-loading waa-hide"></span>
                </form>
                <div class="waa-clearfix"></div>
            </div>
        </div>
    </div>
</script>
