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
                                            <td colspan="2" class="sku">
                                                <label><?php _e( 'SKU: ', 'waa' ); ?><a href="#" title="" class="tips" data-original-title="<?php _e( 'Enter a SKU for this variation or leave blank to use the parent product SKU.', 'waa' ); ?>">[?]</a></label>
                                                <input type="text" placeholder="" value="{{ ( !_.isUndefined( data._sku ) ) ? data._sku[0] : '' }}" name="variable_sku[]" size="7">
                                            </td>
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
                                                            <td>
                                                                <label><?php _e( 'Sale Price: ', 'waa' ); ?> (<?php echo get_woocommerce_currency_symbol(); ?>) <a class="sale_schedule" href="#"><?php _e( 'Schedule', 'waa' ); ?></a>
                                                                <a style="display:none" class="cancel_sale_schedule" href="#"><?php _e( 'Cancel schedule', 'waa' ); ?></a></label>
                                                                <input type="text" class="wc_input_price" value="{{ ( !_.isUndefined( data._sale_price ) ) ? data._sale_price[0] : '' }}" name="variable_sale_price[]" size="5" placeholder="<?php _e( '0.00', 'waa' ); ?>">
                                                            </td>
                                                        </tr>

                                                        <tr style="display:none" class="sale_price_dates_fields">
                                                            <td>
                                                                <label><?php _e( 'Sale start date:', 'waa' ); ?></label>
                                                                <input type="text" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" maxlength="10" placeholder="<?php _e( 'From… YYYY-MM-DD', 'waa' ); ?>" value="{{ ( !_.isUndefined( data._sale_price_dates_from ) ) ? data._sale_price_dates_from[0] : '' }}" name="variable_sale_price_dates_from[]" class="sale_price_dates_from">
                                                            </td>
                                                            <td>
                                                                <label><?php _e( 'Sale end date:', 'waa' ); ?></label>
                                                                <input type="text" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" maxlength="10" placeholder="<?php _e( 'To… YYYY-MM-DD', 'waa' ); ?>" value="{{ ( !_.isUndefined( data._sale_price_dates_to ) ) ? data._sale_price_dates_to[0] : '' }}" name="variable_sale_price_dates_to[0]">
                                                            </td>
                                                        </tr>

                                                        <?php if ( wc_product_weight_enabled() || wc_product_dimensions_enabled() ) : ?>

                                                            <tr>
                                                                <?php if ( wc_product_weight_enabled() ) : ?>

                                                                    <td class="hide_if_variation_virtual">
                                                                        <label><?php echo __( 'Weight', 'waa' ) . ' (' . esc_html( get_option( 'woocommerce_weight_unit' ) ) . '):'; ?> <a href="#" title="" class="tips" data-original-title="Enter a weight for this variation or leave blank to use the parent product weight.">[?]</a></label>
                                                                        <input type="text" class="wc_input_decimal" placeholder="0.00" value="{{ ( !_.isUndefined( data._weight ) ) ? data._weight[0] : '' }}" name="variable_weight[]" size="5">
                                                                    </td>
                                                                <?php else : ?>
                                                                    <td>&nbsp;</td>
                                                                <?php endif; ?>
                                                                <?php if ( wc_product_dimensions_enabled() ) : ?>
                                                                    <td class="dimensions_field hide_if_variation_virtual">
                                                                        <label for="product_length"><?php echo __( 'Dimensions (L&times;W&times;H)', 'waa' ) . ' (' . esc_html( get_option( 'woocommerce_dimension_unit' ) ) . '):'; ?></label>
                                                                        <input type="text" placeholder="0" value="{{ ( !_.isUndefined( data._length ) ) ? data._length[0] : '' }}" name="variable_length[]" size="6" class="input-text wc_input_decimal" id="product_length">
                                                                        <input type="text" placeholder="0" value="{{ ( !_.isUndefined( data._width ) ) ? data._width[0] : '' }}" name="variable_width[]" size="6" class="input-text wc_input_decimal">
                                                                        <input type="text" placeholder="0" value="{{ ( !_.isUndefined( data._height ) ) ? data._height[0] : '' }}" name="variable_height[]" size="6" class="input-text wc_input_decimal last">
                                                                    </td>
                                                                <?php else : ?>
                                                                    <td>&nbsp;</td>
                                                                <?php endif; ?>
                                                            </tr>
                                                        <?php endif; ?>

                                                        <tr>
                                                            <td>
                                                            <# var variation_shipping = ( !_.isUndefined( data.variation_shippingclass ) ) ? data.variation_shippingclass[0] : ''; #>
                                                                <?php _e( 'Shipping class:', 'waa' ); ?></label> <?php
                                                                    $args = array(
                                                                        'taxonomy'          => 'product_shipping_class',
                                                                        'hide_empty'        => 0,
                                                                        'show_option_none'  => __( 'Same as parent', 'waa' ),
                                                                        'name'              => 'variable_shipping_class[0]',
                                                                        'id'                => '',
                                                                        'echo'              => 0
                                                                    );

                                                                    $shipping = wp_dropdown_categories( $args );

                                                                    echo str_replace( '<select', '<select class="variation_select_fileld" data-selected_data="{{ variation_shipping }}"', $shipping );
                                                                ?>
                                                            </td>
                                                            <td>
                                                            <?php if ( get_option( 'woocommerce_calc_taxes' ) == 'yes' ) : ?>
                                                                <label><?php _e( 'Tax class:', 'waa' ); ?></label>
                                                                <select name="variable_tax_class[]" class="variation_select_fileld" data-selected_data="{{ ( !_.isUndefined( data.variation_taxclass ) ) ? data.variation_taxclass[0] : '' }}">
                                                                    <option value="parent"><?php _e( 'Same as parent', 'waa' ); ?></option>
                                                                    <# _.each( data.tax_class_options[0], function( tax_val, tax_key ) { #>
                                                                        <option value="{{tax_key}}">{{ tax_val }}</option>
                                                                    <# }); #>
                                                                </select>
                                                            <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                        <tr class="show_if_variation_downloadable" style="display:none">
                                                            <td colspan="2">
                                                                <div class="form-field downloadable_files">
                                                                    <label><?php _e( 'Downloadable Files', 'waa' ); ?>:</label>
                                                                    <table class="waa-table">
                                                                        <thead>
                                                                            <tr>
                                                                                <td><?php _e( 'Name', 'waa' ); ?> <span class="tips" title="<?php _e( 'This is the name of the download shown to the customer.', 'waa' ); ?>">[?]</span></td>
                                                                                <td colspan="2"><?php _e( 'File URL', 'waa' ); ?> <span class="tips" title="<?php _e( 'This is the URL or absolute path to the file which customers will get access to.', 'waa' ); ?>">[?]</span></td>
                                                                                <td>&nbsp;</td>
                                                                            </tr>
                                                                        </thead>
                                                                        <tfoot>
                                                                            <tr>
                                                                                <th colspan="4">
                                                                                    <a href="#" class="insert-file-row btn btn-sm btn-success" data-row='
                                                                                        <tr>
                                                                                            <td class="file_name"><input type="text" class="input_text" placeholder="<?php _e( "File Name", "waa" ); ?>" name="_wc_variation_file_names[{{ ( !_.isUndefined( data.variation_id ) ) ? data.variation_id[0] : ''  }}][]" value="" /></td>
                                                                                            <td class="file_url"><input type="text" class="input_text wc_file_url" placeholder="<?php _e( "http://", "waa" ); ?>" name="_wc_variation_file_urls[{{ ( !_.isUndefined( data.variation_id ) ) ? data.variation_id[0] : ''  }}][]" value="" /></td>
                                                                                            <td class="file_url_choose" width="1%"><a href="#" class="waa-btn waa-btn-sm waa-btn-default upload_file_button" data-choose="<?php _e( "Choose file", "waa" ); ?>" data-update="<?php _e( "Insert file URL", "waa" ); ?>"><?php echo str_replace( " ", "&nbsp;", __( "Choose file", "waa" ) ); ?></a></td>
                                                                                            <td width="1%"><a href="#" class="waa-btn waa-btn-sm waa-btn-danger delete"><?php _e( "Delete", "waa" ); ?></a></td>
                                                                                        </tr>
                                                                                    '><?php _e( 'Add File', 'waa' ); ?></a>
                                                                                </th>
                                                                            </tr>
                                                                        </tfoot>
                                                                        <tbody>
                                                                            <#
                                                                                if ( !_.isUndefined( data._downloadable_files ) && data._downloadable_files ) {
                                                                                    _.each( data._downloadable_files[0], function( file_val, file_key ) {

                                                                                        if ( ! _.isArray( file_val ) ) {
                                                                                            var file = {
                                                                                                file : file_val.file,
                                                                                                name : file_val.name
                                                                                            };
                                                                                        }
                                                                                        #>
                                                                                        <tr>
                                                                                            <td class="file_name"><input type="text" class="input_text" placeholder="<?php _e( 'File Name', 'waa' ); ?>" name="_wc_variation_file_names[{{ data.variation_id[0] }}][]" value="{{ file.name }}" /></td>
                                                                                            <td class="file_url"><input type="text" class="input_text wc_file_url" placeholder="<?php _e( 'http://', 'waa' ); ?>" name="_wc_variation_file_urls[{{ data.variation_id[0] }}][]" value="{{ file.file }}" /></td>
                                                                                            <td class="file_url_choose" width="1%"><a href="#" class="waa-btn waa-btn-sm waa-btn-default upload_file_button" data-choose="<?php _e( 'Choose file', 'waa' ); ?>" data-update="<?php _e( 'Insert file URL', 'waa' ); ?>"><?php echo str_replace( ' ', '&nbsp;', __( 'Choose file', 'waa' ) ); ?></a></td>
                                                                                            <td width="1%"><a href="#" class="waa-btn waa-btn-sm waa-btn-danger delete"><?php _e( 'Delete', 'waa' ); ?></a></td>
                                                                                        </tr>
                                                                                        <#
                                                                                    });
                                                                                }
                                                                            #>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr class="show_if_variation_downloadable" style="display:none">
                                                            <td>
                                                                <div>
                                                                    <label><?php _e( 'Download Limit:', 'waa' ); ?> <a class="tips" title="<?php _e( 'Leave blank for unlimited re-downloads.', 'waa' ); ?>" href="#">[?]</a></label>
                                                                    <input type="number" size="5" name="variable_download_limit[]" value="{{ ( !_.isUndefined( data._download_limit ) ) ? data._download_limit[0] : '' }}" placeholder="<?php _e( 'Unlimited', 'waa' ); ?>" step="1" min="0" />
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div>
                                                                    <label><?php _e( 'Download Expiry:', 'waa' ); ?> <a class="tips" title="<?php _e( 'Enter the number of days before a download link expires, or leave blank.', 'waa' ); ?>" href="#">[?]</a></label>
                                                                    <input type="number" size="5" name="variable_download_expiry[]" value="{{ ( !_.isUndefined( data._download_expiry ) ) ? data._download_expiry[0] : '' }}" placeholder="<?php _e( 'Unlimited', 'waa' ); ?>" step="1" min="0" />
                                                                </div>
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

                                                <label class="checkbox"><input type="checkbox" {{ ( !_.isUndefined( data._downloadable ) && data._downloadable == 'yes' ) ? 'checked="checked"': '' }} name="variable_is_downloadable[]" class="checkbox variable_is_downloadable"> Downloadable <a href="#" title="" class="tips" data-original-title="Enable this option if access is given to a downloadable file upon purchase of a product">[?]</a></label>

                                                <label class="checkbox"><input type="checkbox" {{ ( !_.isUndefined( data._virtual ) && data._virtual == 'yes' ) ? 'checked="checked"': '' }} name="variable_is_virtual[]" class="checkbox variable_is_virtual"> Virtual <a href="#" title="" class="tips" data-original-title="Enable this option if a product is not shipped or there is no shipping cost">[?]</a></label>

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
