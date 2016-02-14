/**
 * Admin helper functions
 *
 * @package WAA Framework
 */
jQuery(function ($) {

    window.WAA_Admin = {

        /**
         * Image Upload Helper Function
         **/
        imageUpload: function (e) {
            e.preventDefault();

            var self = $(this),
                inputField = self.siblings('input.image_url');

            tb_show('', 'media-upload.php?post_id=0&amp;type=image&amp;TB_iframe=true');

            window.send_to_editor = function (html) {
                var url = $(html).attr('href');

                //if we find an image, get the src
                if ($(html).find('img').length > 0) {
                    url = $(html).find('img').attr('src');
                }

                inputField.val(url);

                var image = '<img src="' + url + '" alt="image" />';
                image += '<a href="#" class="remove-image"><span>Remove</span></a>';

                self.siblings('.image_placeholder').empty().append(image);
                tb_remove();
            }
        },

        removeImage: function (e) {
            e.preventDefault();
            var self = $(this);

            self.parent('.image_placeholder').siblings('input.image_url').val('');
            self.parent('.image_placeholder').empty();
        }
    }
});
jQuery(document).ready(function ($) {
    $('#sell-original-btn').on('click', function(e) {
        setWaaProductTypeOriginal();
        e.preventDefault();
    });

    function setWaaProductTypeOriginal() {
        $('.product-set-type').hide();
        $('.product-edit-new-container').show();
        $('.hide-if-sell-original').addClass('display-none');
        $('.hide_if_only_print').show();
        $('#waa_only_print').attr('checked', true);
        $('#_sold_individually').attr('checked', true);
        $('#_manage_stock').attr('checked', true);
        $('#_stock').val(1);
        $('#waa_product_type').val('sell-original');
        $('#_regular_price').attr('required', true);
        $('#_has_attribute').attr('checked', false).trigger("change");
        $('#_overwrite_shipping').attr('checked', true).trigger("change");
    }

    $('#sell-prints-btn').on('click', function (e) {
        setWaaProductTypePrints();
        e.preventDefault();
    });

    function setWaaProductTypePrints() {
        $('.product-set-type').hide();
        $('.product-edit-new-container').show();
        $('.hide-if-sell-prints').css('display', 'none !important');
        $('#waa_only_print').attr('checked', false);
        $('#_sold_individually').attr('checked', false);
        $('#waa_product_type').val('sell-prints');
        $('#_regular_price').attr('required', false);
        $('#_has_attribute').attr('checked', true).trigger("change");
        $('#_overwrite_shipping').attr('checked', true).trigger("change");
    }

    $('#sell-both-btn').on('click', function (e) {
        setWaaProductTypeBoth();
        e.preventDefault();
    });

    function setWaaProductTypeBoth() {
        $('.product-set-type').hide();
        $('.product-edit-new-container').show();
        $('.hide-if-sell-both').hide();
        $('#waa_product_type').val('sell-both');
        $('#_has_attribute').attr('checked', true).trigger("change");
        $('#_overwrite_shipping').attr('checked', true).trigger("change");
    }

    $('.tips').tooltip();
    $('select.grant_access_id').chosen();

    $('ul.order-status').on('click', 'a.waa-edit-status', function (e) {
        $(this).addClass('waa-hide').closest('li').next('li').removeClass('waa-hide');

        return false;
    });

    $('ul.order-status').on('click', 'a.waa-cancel-status', function (e) {
        $(this).closest('li').addClass('waa-hide').prev('li').find('a.waa-edit-status').removeClass('waa-hide');

        return false;
    });

    $('form#waa-order-status-form').on('submit', function (e) {
        e.preventDefault();

        var self = $(this),
            li = self.closest('li');

        li.block({
            message: null,
            overlayCSS: {background: '#fff url(' + waa.ajax_loader + ') no-repeat center', opacity: 0.6}
        });

        $.post(waa.ajaxurl, self.serialize(), function (response) {
            li.unblock();

            var prev_li = li.prev();

            li.addClass('waa-hide');
            prev_li.find('label').replaceWith(response);
            prev_li.find('a.waa-edit-status').removeClass('waa-hide');
        });
    });

    $('form#add-order-note').on('submit', function (e) {
        e.preventDefault();

        if (!$('textarea#add-note-content').val()) return;

        $('#waa-order-notes').block({
            message: null,
            overlayCSS: {background: '#fff url(' + waa.ajax_loader + ') no-repeat center', opacity: 0.6}
        });

        $.post(waa.ajaxurl, $(this).serialize(), function (response) {
            $('ul.order_notes').prepend(response);
            $('#waa-order-notes').unblock();
            $('#add-note-content').val('');
        });

        return false;

    })

    $('#waa-order-notes').on('click', 'a.delete_note', function () {

        var note = $(this).closest('li.note');

        $('#waa-order-notes').block({
            message: null,
            overlayCSS: {background: '#fff url(' + waa.ajax_loader + ') no-repeat center', opacity: 0.6}
        });

        var data = {
            action: 'woocommerce_delete_order_note',
            note_id: $(note).attr('rel'),
            security: $('#delete-note-security').val()
        };

        $.post(waa.ajaxurl, data, function (response) {
            $(note).remove();
            $('#waa-order-notes').unblock();
        });

        return false;

    });

    $('.order_download_permissions').on('click', 'button.grant_access', function () {
        var self = $(this),
            product = $('select.grant_access_id').val();

        if (!product) return;

        $('.order_download_permissions').block({
            message: null,
            overlayCSS: {background: '#fff url(' + waa.ajax_loader + ') no-repeat center', opacity: 0.6}
        });

        var data = {
            action: 'waa_grant_access_to_download',
            product_ids: product,
            loop: $('.order_download_permissions .panel').size(),
            order_id: self.data('order-id'),
            security: self.data('nonce')
        };

        $.post(waa.ajaxurl, data, function (response) {

            if (response) {

                $('#accordion').append(response);

            } else {

                alert('Could not grant access - the user may already have permission for this file or billing email is not set. Ensure the billing email is set, and the order has been saved.');

            }

            $('.datepicker').datepicker();
            $('.order_download_permissions').unblock();

        });

        return false;
    });

    $('.order_download_permissions').on('click', 'button.revoke_access', function (e) {
        e.preventDefault();
        var answer = confirm('Are you sure you want to revoke access to this download?');

        if (answer) {

            var self = $(this),
                el = self.closest('.panel');

            var product = self.attr('rel').split(",")[0];
            var file = self.attr('rel').split(",")[1];

            if (product > 0) {

                $(el).block({
                    message: null,
                    overlayCSS: {background: '#fff url(' + waa.ajax_loader + ') no-repeat center', opacity: 0.6}
                });

                var data = {
                    action: 'woocommerce_revoke_access_to_download',
                    product_id: product,
                    download_id: file,
                    order_id: self.data('order-id'),
                    security: self.data('nonce')
                };

                $.post(waa.ajaxurl, data, function (response) {
                    // Success
                    $(el).fadeOut('300', function () {
                        $(el).remove();
                    });
                });

            } else {
                $(el).fadeOut('300', function () {
                    $(el).remove();
                });
            }

        }

        return false;
    });

});
;(function ($) {

    var variantsHolder = $('#variants-holder');
    var product_gallery_frame;
    var product_featured_frame;
    var $image_gallery_ids = $('#product_image_gallery');
    var $product_images = $('#product_images_container ul.product_images');

    var waa_Editor = {

        /**
         * Constructor function
         */
        init: function () {

            product_type = 'simple';

            $('.product-edit-container').on('click', 'input[type=checkbox]#_downloadable', this.downloadable);
            $('.product-edit-container').on('change', '#_product_type', this.onChangeProductType);
            $('.product-edit-container').on('click', 'a.sale-schedule', this.showDiscountSchedule);

            // New Product Desing js
            $('.product-edit-new-container,.product-edit-container').on('click', '._discounted_price', this.newProductDesign.showDiscount);
            $('.product-edit-new-container').on('change', 'input[type=checkbox].sale-schedule', this.newProductDesign.showDiscountSchedule);
            $('.product-edit-new-container').on('change', 'input[type=checkbox]#_manage_stock', this.newProductDesign.showManageStock);
            $('.product-edit-new-container').on('change', 'input[type=checkbox]#_required_shipping', this.newProductDesign.showShippingWrapper);
            $('.product-edit-new-container').on('change', 'input[type=checkbox]#_required_tax', this.newProductDesign.showTaxWrapper);
            $('.product-edit-new-container').on('change', 'input[type=checkbox]#_downloadable', this.newProductDesign.downloadable);
            $('.product-edit-new-container').on('change', 'input[type=checkbox]#_has_attribute', this.newProductDesign.showVariationSection);

            // variants
            $('#product-attributes').on('click', '.add-variant-category', this.variants.addCategory);
            $('#variants-holder').on('click', '.box-header .row-remove', this.variants.removeCategory);

            $('#variants-holder').on('click', '.item-action a.row-add', this.variants.addItem);
            $('#variants-holder').on('click', '.item-action a.row-remove', this.variants.removeItem);


            $('body, #variable_product_options').on('click', '.sale_schedule', this.variants.saleSchedule);
            $('body, #variable_product_options').on('click', '.cancel_sale_schedule', this.variants.cancelSchedule);
            $('#variable_product_options').on('woocommerce_variations_added', this.variants.onVariantAdded);
            this.variants.dates();
            this.variants.initSaleSchedule();

            // save attributes
            $('.save_attributes').on('click', this.variants.save);

            // gallery
            $('#waa-product-images').on('click', 'a.add-product-images', this.gallery.addImages);
            $('#waa-product-images').on('click', 'a.action-delete', this.gallery.deleteImage);
            $('#waa-product-images').on('click', 'a.delete', this.gallery.deleteImage);
            this.gallery.sortable();

            // featured image
            $('.product-edit-new-container, .product-edit-container').on('click', 'a.waa-feat-image-btn', this.featuredImage.addImage);
            $('.product-edit-new-container, .product-edit-container').on('click', 'a.waa-remove-feat-image', this.featuredImage.removeImage);

            // download links

            // post status change
            $('.waa-toggle-sidebar').on('click', 'a.waa-toggle-edit', this.sidebarToggle.showStatus);
            $('.waa-toggle-sidebar').on('click', 'a.waa-toggle-save', this.sidebarToggle.saveStatus);
            $('.waa-toggle-sidebar').on('click', 'a.waa-toggle-cacnel', this.sidebarToggle.cancel);

            // new product design variations

            $('.waa-variation-container').on('click', 'a.add_attribute_option', this.newProductDesign.addAttributeOption);
            $('.waa-variation-container').on('click', 'button.remove_attribute', this.newProductDesign.removeAttributeOption);
            $('.product-edit-new-container').on('change', 'input[type=checkbox]#_has_attribute', this.newProductDesign.createVariationSection);
            // $('.product-edit-new-container').on('change', 'input[type=checkbox].waa_create_variation', this.newProductDesign.createVariationWarning);
            $('.product-edit-new-container').on('click', 'a.edit_variation', this.newProductDesign.editSingleVariation);
            $('.product-edit-new-container').on('click', 'a.btn-remove-print', this.newProductDesign.removeSingleVariation);
            $('body').on('click', '.upload_image_button', this.newProductDesign.loadVariationImage);
            $('body, .product-edit-container').on('click', 'a.upload_file_button', this.fileDownloadable);
            $('body').on('click', 'a.add_single_attribute_option', this.newProductDesign.addSingleAttributeOption);
            $('body').on('click', 'button.remove_single_attribute', this.newProductDesign.removeSingleAttributeOption);
            $('body').on('click', 'a.waa_add_new_variation', this.newProductDesign.addSingleVariationOption);

            $('body').on('submit', 'form#doakn-single-attribute-form', this.newProductDesign.saveProductAttributes);
            $('body').on('submit', 'form#waa-single-variation-form', this.newProductDesign.saveProductVariations);

            $('body').on('change', 'input.variable_is_downloadable', this.newProductDesign.showHideDownload);
            $('body').on('change', 'input.variable_manage_stock', this.newProductDesign.showHideMangeStock);
            $('body').on('change', 'input.variable_is_virtual', this.newProductDesign.showHideVirtual);

            $('.product-edit-new-container').on('click', 'a.waa_add_new_attribute', this.newProductDesign.addExtraAttributeOption)

            // // shipping
            $('.product-edit-new-container, #product-shipping').on('change', 'input[type=checkbox]#_overwrite_shipping', this.newProductDesign.shipping.showHideOverride);
            $('.product-edit-new-container').on('change', 'input[type=checkbox]#_disable_shipping', this.newProductDesign.shipping.disableOverride);
            $('#product-shipping').on('click', '#_disable_shipping', this.shipping.disableOverride);


            // File inputs
            $('body').on('click', 'a.insert-file-row', function () {
                $(this).closest('table').find('tbody').append($(this).data('row'));
                return false;
            });

            $('body').on('click', 'a.delete', function () {
                $(this).closest('tr').remove();
                return false;
            });

            this.loadTagChosen();
            this.newProductDesign.shipping.showHideOverride();
            this.newProductDesign.shipping.disableOverride();
            this.shipping.disableOverride();
            $('#_disable_shipping').trigger('change');
            $('#_overwrite_shipping').trigger('change');

            this.loadTagIt();

        },

        loadTagChosen: function () {
            $('select.product_tags').chosen( { width:"95%" });
        },

        loadTagIt: function () {
            if (!jQuery.fn.tagit) {
                return;
            }

            $('.waa-attribute-option-values').tagit({
                afterTagAdded: waa_Editor.tagIt.afterTagAdded,
                afterTagRemoved: waa_Editor.tagIt.afterTagRemoved,
            });
        },

        downloadable: function () {
            if ($(this).prop('checked')) {
                $(this).closest('aside').find('.waa-side-body').removeClass('waa-hide');
            } else {
                $(this).closest('aside').find('.waa-side-body').addClass('waa-hide');
            }
        },

        showDiscountSchedule: function (e) {
            e.preventDefault();

            $('.sale-schedule-container').slideToggle('fast');
        },

        onChangeProductType: function () {
            var selected = $('#_product_type').val();
            if (selected === 'simple') {
                product_type = 'simple';
                $('aside.downloadable').removeClass('waa-hide');
                $('.show_if_variable').addClass('waa-hide');
                $('.show_if_simple').removeClass('waa-hide');

            } else {
                // this is a variable type product
                product_type = 'variable';
                $('aside.downloadable').addClass('waa-hide');
                $('.show_if_variable').removeClass('waa-hide');
                $('.show_if_simple').addClass('waa-hide');
            }
        },


        tagIt: {

            afterTagAdded: function (event, ui) {
                waa_Editor.reArrangeVariations();
            },

            afterTagRemoved: function (event, ui) {
                waa_Editor.reArrangeVariations();
            }
        },

        makeVariation: function () {
            var combination = [],
                arg = arguments[0],
                max = arg.length - 1;

            function helper(arr, j) {
                for (var i = 0, l = arg[j].length; i < l; i++) {

                    var a = arr.slice(0); // clone arr
                    a.push(arg[j][i]);
                    if (j == max) {
                        combination.push(a);
                    } else {
                        helper(a, j + 1);
                    }
                }
            }

            helper([], 0);

            return combination;
        },

        reArrangeVariations: function () {

            if ($('input[type=checkbox]#_create_variation').is(':checked')) {
                var data = [], data_val = [], arg;

                $('tr.waa-attribute-options').each(function (i) {
                    var attr = $(this).find('.waa-attribute-option-values').tagit("assignedTags");
                    var attr_name = $(this).find('.waa-attribute-option-name').val();

                    if (attr.length) {
                        data_val.push(attr_name);
                        data.push(attr);
                    } else {
                        //$(this).find( '.waa-attribute-option-name' ).val('');
                    }
                });

                if (data.length) {
                    var variations = {
                        variation_item: waa_Editor.makeVariation(data),
                        variation_title: data_val
                    };

                    var variants_template = wp.template('waa-variations');
                    $('.waa-variation-content-wrapper').html(variants_template(variations));
                } else {
                    $('.waa-variation-content-wrapper').html('');
                }
                ;
            }

        },

        newProductDesign: {

            showDiscount: function () {
                var self = $(this),
                    checked = self.is(':checked'),
                    container = $('.special-price-container');

                if (checked) {
                    container.removeClass('waa-hide');
                } else {
                    container.addClass('waa-hide');
                }
            },

            showDiscountSchedule: function (e) {
                if ($(this).is(':checked')) {
                    $('.sale-schedule-container').slideDown('fast');
                } else {
                    $('.sale-schedule-container').slideUp('fast');
                }
            },

            showManageStock: function (e) {
                if ($(this).is(':checked')) {
                    $('.show_if_stock').slideDown('fast');
                } else {
                    $('.show_if_stock').slideUp('fast');
                }
            },

            showShippingWrapper: function (e) {
                if ($(this).is(':checked')) {
                    $('.show_if_needs_shipping').slideDown('fast');
                } else {
                    $('.show_if_needs_shipping').slideUp('fast');
                }
            },

            showTaxWrapper: function () {
                if ($(this).is(':checked')) {
                    $('.show_if_needs_tax').slideDown('fast');
                } else {
                    $('.show_if_needs_tax').slideUp('fast');
                }
            },

            downloadable: function () {
                if ($(this).prop('checked')) {
                    if ($('.waa-product-shipping-tax').hasClass('woocommerce-no-tax')) {
                        $('.waa-product-shipping-tax').addClass('waa-hide');
                    }
                    ;

                    $('.hide_if_downloadable').hide();
                    $(this).closest('div').find('.waa-side-body').removeClass('waa-hide');
                } else {

                    if ($('.waa-product-shipping-tax').hasClass('woocommerce-no-tax')) {
                        $('.waa-product-shipping-tax').removeClass('waa-hide');
                    }
                    ;

                    $('.hide_if_downloadable').show();
                    $(this).closest('div').find('.waa-side-body').addClass('waa-hide');
                }
            },

            showVariationSection: function () {
                if ($(this).is(':checked')) {
                    $(this).closest('.waa-variation-container').find('.waa-side-body').removeClass('waa-hide');
                } else {
                    $(this).closest('.waa-variation-container').find('.waa-side-body').addClass('waa-hide');
                }
            },

            addAttributeOption: function (e) {
                e.preventDefault();
                var self = $(this),
                    attr_wrap = self.closest('.waa-attribute-content-wrapper').find('select#predefined_attribute');

                if (attr_wrap.val() == '') {

                    attribute_option = self.closest('.waa-attribute-content-wrapper')
                        .find('tr.waa-attribute-options')
                        .first()
                        .clone();

                    attribute_option.find('input').val('');

                    if (attribute_option.find('input.waa-attribute-option-name-label').length == 1) {
                        var $attrName = attribute_option.find('input.waa-attribute-option-name'),
                            $attrNameLabel = attribute_option.find('input.waa-attribute-option-name-label');

                        $attrName.remove();
                        $attrNameLabel.removeAttr('disabled data-attribute_name')
                            .attr('name', 'attribute_names[]')
                            .addClass('waa-attribute-option-name')
                            .removeClass('waa-attribute-option-name-label');
                        attribute_option.find('input[name="attribute_is_taxonomy[]"]').val('0');
                    }

                    attribute_option.insertBefore($('table.waa-attribute-options-table').find('tr.waa-attribute-is-variations'));
                    attribute_option.find('ul.tagit').remove();
                    attribute_option.find('input.waa-attribute-option-values').tagit({
                        afterTagAdded: waa_Editor.tagIt.afterTagAdded,
                        afterTagRemoved: waa_Editor.tagIt.afterTagRemoved,
                    });
                } else {

                    var data = {
                            action: 'waa_get_pre_attribute',
                            name: attr_wrap.val()
                        },
                        flag = true;

                    self.closest('.waa-attribute-content-wrapper').find('.waa-attribute-option-name-label').each(function (i, val) {
                        if ($(val).data('attribute_name') == attr_wrap.val()) {
                            alert(waa.duplicates_attribute_messg);
                            flag = false;
                            attr_wrap.val('');
                        }
                    });

                    if (!flag) {
                        return;
                    }
                    self.closest('.waa-attribute-content-wrapper').find('span.waa-loading').removeClass('waa-hide');
                    $.post(waa.ajaxurl, data, function (resp) {
                        if (resp.success) {
                            var wrap_data = (resp.data).trim();
                            attr_wrap.val('');
                            $(wrap_data).insertBefore($('table.waa-attribute-options-table').find('tr.waa-attribute-is-variations'));
                            $('input.waa-attribute-option-values').tagit({
                                afterTagAdded: waa_Editor.tagIt.afterTagAdded,
                                afterTagRemoved: waa_Editor.tagIt.afterTagRemoved,
                            });
                            self.closest('.waa-attribute-content-wrapper').find('span.waa-loading').addClass('waa-hide');
                        }
                        ;
                    });
                }
            },

            removeAttributeOption: function (e) {
                e.preventDefault();
                var self = $(this),
                    row = self.closest('tbody').find('tr.waa-attribute-options').length;

                if (row < 2) {
                    return false;
                }

                self.closest('tr').remove();
                waa_Editor.reArrangeVariations();
            },

            createVariationSection: function () {
                if ($(this).is(':checked')) {
                    $('.hide_if_variation').hide();
                    $('#_manage_stock').trigger('change');
                    waa_Editor.reArrangeVariations();
                    if ($(this).hasClass('waa_create_variation')) {
                        $('.waa-variation-remove-warning').slideUp();
                    }
                    ;
                } else {
                    $('.hide_if_variation').show();
                    $('#_manage_stock').trigger('change');
                    $('.waa-variation-content-wrapper').html('');

                    if ($(this).hasClass('waa_create_variation')) {
                        var alert = '<div class="waa-alert waa-alert-warning waa-variation-remove-warning"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + waa.variation_unset_warning + '</div>';
                        $(this).closest('label').before(alert).slideDown();
                    }
                    ;
                }
            },

            editSingleVariation: function (e) {
                e.preventDefault();

                var self = $(this),
                    variation_data = self.closest('tr').data('variation_data');

                var variant_single_template = wp.template('waa-single-variations');
                var variation_single = variant_single_template(variation_data);

                $.magnificPopup.open({
                    items: {
                        src: variation_single.trim(),
                        type: 'inline'
                    },
                    callbacks: {
                        open: function () {
                            waa_Editor.variants.dates();
                            $('.sale_schedule').trigger('click');
                            $('input.variable_is_downloadable, input.variable_is_virtual, input.variable_manage_stock').change();
                            $('body').find('select.variation_select_fileld').each(function (i, el) {
                                var data_selected_val = $(el).data('selected_data');

                                if (data_selected_val) {
                                    $(el).find('option').each(function (key, el_option) {

                                        if ($(el_option).attr('value') == data_selected_val) {
                                            $(el_option).attr('selected', 'selected');
                                        }
                                    });
                                }
                            });
                            $('.tips').tooltip();
                        }
                    }
                });

            },

            removeSingleVariation: function (e) {
                e.preventDefault();

                if (confirm(waa.delete_confirm)) {
                    var self = $(this),
                        data = {
                            action: 'waa_remove_single_variation_item',
                            variation_id: self.data('variation_id')
                        },
                        loadUrl = window.location.href;


                    $('.waa-variation-container').addClass('waa-blur-effect');
                    $('.waa-variation-container').append('<div class="waa-variation-loader"></div>');

                    $.post(waa.ajaxurl, data, function (resp) {
                        if (resp.success) {
                            $('.waa-variation-container').load(loadUrl + ' .waa-variation-container', function () {
                                $('#_create_variation').trigger('change');
                                $('.waa-variation-container').removeClass('waa-blur-effect');
                                $('.waa-variation-container').remove('.waa-variation-loader');
                            });
                        }
                    });
                }
            },

            showHideDownload: function () {

                $(this).closest('.woocommerce_variation').find('.show_if_variation_downloadable').hide();

                if ($(this).is(':checked')) {
                    $(this).closest('.woocommerce_variation').find('.show_if_variation_downloadable').show();
                }

            },

            showHideMangeStock: function () {

                $(this).closest('.woocommerce_variation').find('.show_if_variation_manage_stock').hide();

                if ($(this).is(':checked')) {
                    $(this).closest('.woocommerce_variation').find('.show_if_variation_manage_stock').show();
                }

            },

            showHideVirtual: function () {

                $(this).closest('.woocommerce_variation').find('.hide_if_variation_virtual').show();

                if ($(this).is(':checked')) {
                    $(this).closest('.woocommerce_variation').find('.hide_if_variation_virtual').hide();
                }

            },

            loadVariationImage: function (e) {
                e.preventDefault();
                var variable_image_frame;
                var $button = $(this);
                var post_id = $button.attr('rel');
                var $parent = $button.closest('.upload_image');
                setting_variation_image = $parent;
                placeholder_iamge = $parent.find('span.variation_placeholder_image').data('placeholder_image');
                setting_variation_image_id = post_id;

                e.preventDefault();

                if ($button.is('.waa-img-remove')) {

                    setting_variation_image.find('.upload_image_id').val('');
                    setting_variation_image.find('img').attr('src', placeholder_iamge);
                    setting_variation_image.find('.upload_image_button').removeClass('waa-img-remove');

                } else {

                    // If the media frame already exists, reopen it.
                    if (variable_image_frame) {
                        variable_image_frame.uploader.uploader.param('post_id', setting_variation_image_id);
                        variable_image_frame.open();
                        return;
                    } else {
                        wp.media.model.settings.post.id = setting_variation_image_id;
                        wp.media.model.settings.type = 'waa';
                    }

                    // Create the media frame.
                    variable_image_frame = wp.media.frames.variable_image = wp.media({
                        // Set the title of the modal.
                        title: 'Choose an image',
                        button: {
                            text: 'Set variation image'
                        }
                    });

                    // When an image is selected, run a callback.
                    variable_image_frame.on('select', function () {

                        attachment = variable_image_frame.state().get('selection').first().toJSON();

                        setting_variation_image.find('.upload_image_id').val(attachment.id);
                        setting_variation_image.find('.upload_image_button').addClass('waa-img-remove');
                        setting_variation_image.find('img').attr('src', attachment.url);

                        wp.media.model.settings.post.id = wp_media_post_id;
                    });

                    // Finally, open the modal.
                    variable_image_frame.open();
                }

            },

            addExtraAttributeOption: function (e) {
                e.preventDefault();
                var self = $(this),
                    data = {
                        attribute_data: self.data('product_attributes'),
                        attribute_taxonomies: self.data('predefined_attr')
                    },
                    attribute_option = wp.template('waa-single-attribute'),
                    attribute_single = attribute_option(data);

                $.magnificPopup.open({

                    items: {
                        src: attribute_single.trim(),
                        type: 'inline',
                    },
                    callbacks: {
                        open: function () {
                            $('.tips').tooltip();
                            $('body').find('.waa-single-attribute-option-values').tagit();
                        }
                    },

                });
            },

            addSingleAttributeOption: function (e) {
                e.preventDefault();

                var self = $(this),
                    attr_wrap = self.closest('table.waa-single-attribute-options-table').find('select#predefined_attribute');

                if (attr_wrap.val() == '') {
                    attribute_option = self.closest('table.waa-single-attribute-options-table')
                        .find('tr.waa-single-attribute-options')
                        .first()
                        .clone();

                    attribute_option.find('input').val('');

                    if (attribute_option.find('input.waa-single-attribute-option-name-label').length == 1) {
                        var $attrName = attribute_option.find('input.waa-single-attribute-option-name'),
                            $attrNameLabel = attribute_option.find('input.waa-single-attribute-option-name');
                        $attrName.remove();
                        $attrNameLabel.removeAttr('disabled data-attribute_name')
                            .attr('name', 'attribute_names[]')
                            .addClass('waa-single-attribute-option-name')
                            .removeClass('waa-single-attribute-option-name-label');
                        attribute_option.find('input[name="attribute_is_taxonomy[]"]').val('0');
                    }

                    $('table.waa-single-attribute-options-table').find('tbody').append(attribute_option);
                    attribute_option.find('ul.tagit').remove();
                    attribute_option.find('input.waa-single-attribute-option-values').tagit();

                } else {

                    var data = {
                            action: 'waa_get_pre_attribute',
                            name: attr_wrap.val(),
                            from: 'popup'
                        },
                        flag = true;

                    self.closest('table.waa-single-attribute-options-table').find('.waa-single-attribute-option-name-label').each(function (i, val) {
                        if ($(val).data('attribute_name') == attr_wrap.val()) {
                            alert(waa.duplicates_attribute_messg);
                            flag = false;
                            attr_wrap.val('');
                        }
                    });

                    if (!flag) {
                        return;
                    }
                    self.closest('table.waa-single-attribute-options-table').find('span.waa-loading').removeClass('waa-hide');
                    $.post(waa.ajaxurl, data, function (resp) {
                        if (resp.success) {
                            var wrap_data = (resp.data).trim();
                            attr_wrap.val('');
                            $('table.waa-single-attribute-options-table').find('tbody').append(wrap_data);
                            $('input.waa-single-attribute-option-values').tagit();
                            self.closest('.waa-single-attribute-options-table').find('span.waa-loading').addClass('waa-hide');
                        }
                        ;
                    });
                }
            },

            removeSingleAttributeOption: function (e) {
                e.preventDefault();

                e.preventDefault();
                var self = $(this);

                var row = self.closest('tbody').find('tr').length;

                if (row < 2) {
                    return false;
                }

                self.closest('tr').remove();
            },

            saveProductAttributes: function (e) {
                e.preventDefault();
                var self = $(this),
                    data = {
                        action: 'waa_save_attributes_options',
                        formdata: $(this).serialize()
                    },
                    loadUrl = window.location.href;

                self.find('.waa-save-single-attr-loader').removeClass('waa-hide');
                $.post(waa.ajaxurl, data, function (resp) {
                    if (resp.success) {
                        $('.waa-variation-container').addClass('waa-blur-effect');
                        $('.waa-variation-container').append('<div class="waa-variation-loader"></div>');

                        $.magnificPopup.close();

                        $('.waa-variation-container').load(loadUrl + ' .waa-variation-container', function () {
                            $('#_create_variation').trigger('change');
                            $('.waa-variation-container').removeClass('waa-blur-effect');
                            $('.waa-variation-container').remove('.waa-variation-loader');
                        });
                    }
                });
            },

            addSingleVariationOption: function (e) {
                e.preventDefault();

                var self = $(this),
                    data = {
                        action: 'waa_add_new_variations_options',
                        post_id: self.data('post_id'),
                        menu_order: self.closest('.waa-variation-container').find('table.waa-variations-table tbody tr').size(),
                    },
                    loadUrl = window.location.href;

                self.closest('.waa-variation-action-wrapper').find('.waa-loading').removeClass('waa-hide');
                $('.waa-variation-container').addClass('waa-blur-effect');
                $('.waa-variation-container').append('<div class="waa-variation-loader"></div>');
                $.post(waa.ajaxurl, data, function (resp) {
                    $('.waa-variation-container').load(loadUrl + ' .waa-variation-container', function () {
                        $('#_create_variation').trigger('change');
                        self.closest('.waa-variation-action-wrapper').find('.waa-loading').addClass('waa-hide');
                        $('.waa-variation-container').removeClass('waa-blur-effect');
                        $('.waa-variation-container').remove('.waa-variation-loader');
                    });
                });

            },

            saveProductVariations: function (e) {
                e.preventDefault();

                var self = $(this),
                    data = {
                        action: 'waa_save_variations_options',
                        formdata: $(this).serialize()
                    },
                    loadUrl = window.location.href;

                self.find('.waa-loading').removeClass('waa-hide');

                $.post(waa.ajaxurl, data, function (resp) {
                    if (resp.success) {
                        $('.waa-variation-container').addClass('waa-blur-effect');
                        $('.waa-variation-container').append('<div class="waa-variation-loader"></div>');

                        $.magnificPopup.close();

                        $('.waa-variation-container').load(loadUrl + ' .waa-variation-container', function () {
                            $('#_create_variation').trigger('change');
                            $('.waa-variation-container').removeClass('waa-blur-effect');
                            $('.waa-variation-container').remove('.waa-variation-loader');
                        });
                    }
                });
            },

            shipping: {
                showHideOverride: function () {
                    if ($('#_overwrite_shipping').is(':checked')) {
                        $('.show_if_override').show();
                    } else {
                        $('.show_if_override').hide();
                    }
                },

                disableOverride: function () {
                    if ($('#_disable_shipping').is(':checked')) {
                        $('.show_if_needs_shipping').show();
                        $('#_overwrite_shipping').trigger('change')
                    } else {
                        $('.show_if_needs_shipping').hide();
                    }
                }
            }
        },

        variants: {
            addCategory: function (e) {
                e.preventDefault();

                var product_types = $('#_product_type').val();
                var check = $(this).closest('p.toolbar').find('select.select-attribute').val();
                var row = $('.inputs-box').length;

                if (check == '') {

                    var category = _.template($('#tmpl-sc-category').html(), {row: row});

                    variantsHolder.append(category).children(':last').hide().fadeIn();

                } else {

                    var data = {
                        row: row,
                        name: check,
                        type: product_types,
                        action: 'waa_pre_define_attribute',
                    };

                    $('#product-attributes .toolbar').block({
                        message: null,
                        overlayCSS: {background: '#fff', opacity: 0.6}
                    });

                    $.post(waa.ajaxurl, data, function (resp) {
                        if (resp.success) {
                            variantsHolder.append(resp.data).children(':last').hide().fadeIn();
                        }
                        $('#product-attributes .toolbar').unblock();

                    });
                }

                if (product_type === 'simple') {
                    variantsHolder.find('.show_if_variable').hide();
                }

            },

            removeCategory: function (e) {
                e.preventDefault();

                if (confirm('Sure?')) {
                    $(this).parents('.inputs-box').fadeOut(function () {
                        $(this).remove();
                    });
                }
            },

            addItem: function (e) {
                e.preventDefault();

                var self = $(this),
                    wrap = self.closest('.inputs-box'),
                    list = self.closest('ul.option-couplet');

                var col = list.find('li').length,
                    row = wrap.data('count');


                var template = _.template($('#tmpl-sc-category-item').html());
                self.closest('li').after(template({'row': row, 'col': col}));
            },

            removeItem: function (e) {
                e.preventDefault();

                var options = $(this).parents('ul').find('li');

                // don't remove if only one option is there
                if (options.length > 1) {
                    $(this).parents('li').fadeOut(function () {
                        $(this).remove();
                    });
                }
            },

            save: function () {

                var data = {
                    post_id: $(this).data('id'),
                    data: $('.woocommerce_attributes').find('input, select, textarea').serialize(),
                    action: 'waa_save_attributes'
                };

                var this_page = window.location.toString();

                // $('#variants-holder').block({ message: 'saving...' });
                $('#variants-holder').block({message: null, overlayCSS: {background: '#fff', opacity: 0.6}});
                $.post(waa.ajaxurl, data, function (resp) {

                    $('#variable_product_options').block({
                        message: null,
                        overlayCSS: {background: '#fff', opacity: 0.6}
                    });
                    $('#variable_product_options').load(this_page + ' #variable_product_options_inner', function () {
                        $('#variable_product_options').unblock();
                    });

                    // fire change events for varaiations
                    $('input.variable_is_downloadable, input.variable_is_virtual, input.variable_manage_stock').trigger('change');

                    $('#variants-holder').unblock();
                });
            },

            initSaleSchedule: function () {
                // Sale price schedule
                $('.sale_price_dates_fields').each(function () {

                    var $these_sale_dates = $(this);
                    var sale_schedule_set = false;
                    var $wrap = $these_sale_dates.closest('div, table');

                    $these_sale_dates.find('input').each(function () {
                        if ($(this).val() != '')
                            sale_schedule_set = true;
                    });

                    if (sale_schedule_set) {

                        $wrap.find('.sale_schedule').hide();
                        $wrap.find('.sale_price_dates_fields').show();

                    } else {

                        $wrap.find('.sale_schedule').show();
                        $wrap.find('.sale_price_dates_fields').hide();

                    }

                });
            },

            saleSchedule: function () {
                var $wrap = $(this).closest('div, table');

                $(this).hide();
                $wrap.find('.cancel_sale_schedule').show();
                $wrap.find('.sale_price_dates_fields').show();

                return false;
            },

            cancelSchedule: function () {
                var $wrap = $(this).closest('div, table');

                $(this).hide();
                $wrap.find('.sale_schedule').show();
                $wrap.find('.sale_price_dates_fields').hide();
                $wrap.find('.sale_price_dates_fields').find('input').val('');

                return false;
            },

            dates: function () {
                var dates = $(".sale_price_dates_fields input").datepicker({
                    defaultDate: "",
                    dateFormat: "yy-mm-dd",
                    numberOfMonths: 1,
                    onSelect: function (selectedDate) {
                        var option = $(this).is('#_sale_price_dates_from, .sale_price_dates_from') ? "minDate" : "maxDate";

                        var instance = $(this).data("datepicker"),
                            date = $.datepicker.parseDate(
                                instance.settings.dateFormat ||
                                $.datepicker._defaults.dateFormat,
                                selectedDate, instance.settings);
                        dates.not(this).datepicker("option", option, date);
                    }
                });
            },

            onVariantAdded: function () {
                waa_Editor.variants.dates();
            }
        },

        gallery: {

            addImages: function (e) {
                e.preventDefault();

                var attachment_ids = $image_gallery_ids.val();

                if (product_gallery_frame) {
                    product_gallery_frame.open();
                    return;
                }

                // Create the media frame.
                product_gallery_frame = wp.media.frames.downloadable_file = wp.media({
                    // Set the title of the modal.
                    title: 'Add Images to Product Gallery',
                    button: {
                        text: 'Add to gallery',
                    },
                    multiple: true
                });

                // When an image is selected, run a callback.
                product_gallery_frame.on('select', function () {

                    var selection = product_gallery_frame.state().get('selection');

                    selection.map(function (attachment) {

                        attachment = attachment.toJSON();

                        if (attachment.id) {
                            attachment_ids = attachment_ids ? attachment_ids + "," + attachment.id : attachment.id;

                            $product_images.append('\
                                <li class="image" data-attachment_id="' + attachment.id + '">\
                                    <img src="' + attachment.url + '" />\
                                    <a href="#" class="action-delete">&times;</a>\
                                </li>');
                        }

                    });

                    $image_gallery_ids.val(attachment_ids);
                });

                product_gallery_frame.open();
            },

            deleteImage: function (e) {
                e.preventDefault();

                $(this).closest('li.image').remove();

                var attachment_ids = '';

                $('#product_images_container ul li.image').css('cursor', 'default').each(function () {
                    var attachment_id = $(this).attr('data-attachment_id');
                    attachment_ids = attachment_ids + attachment_id + ',';
                });

                $image_gallery_ids.val(attachment_ids);

                return false;
            },

            sortable: function () {
                // Image ordering
                $product_images.sortable({
                    items: 'li.image',
                    cursor: 'move',
                    scrollSensitivity: 40,
                    forcePlaceholderSize: true,
                    forceHelperSize: false,
                    helper: 'clone',
                    opacity: 0.65,
                    placeholder: 'waa-sortable-placeholder',
                    start: function (event, ui) {
                        ui.item.css('background-color', '#f6f6f6');
                    },
                    stop: function (event, ui) {
                        ui.item.removeAttr('style');
                    },
                    update: function (event, ui) {
                        var attachment_ids = '';

                        $('#product_images_container ul li.image').css('cursor', 'default').each(function () {
                            var attachment_id = jQuery(this).attr('data-attachment_id');
                            attachment_ids = attachment_ids + attachment_id + ',';
                        });

                        $image_gallery_ids.val(attachment_ids);
                    }
                });
            }
        },

        featuredImage: {

            addImage: function (e) {
                e.preventDefault();

                var self = $(this);

                if (product_featured_frame) {
                    product_featured_frame.open();
                    return;
                }

                product_featured_frame = wp.media({
                    // Set the title of the modal.
                    title: 'Lade ein Foto hoch',
                    button: {
                        text: 'Foto auswählen',
                    }
                });

                product_featured_frame.on('select', function () {
                    var selection = product_featured_frame.state().get('selection');

                    selection.map(function (attachment) {
                        attachment = attachment.toJSON();

                        // set the image hidden id
                        self.siblings('input.waa-feat-image-id').val(attachment.id);

                        // set the image
                        var instruction = self.closest('.instruction-inside');
                        var wrap = instruction.siblings('.image-wrap');

                        // wrap.find('img').attr('src', attachment.sizes.thumbnail.url);
                        wrap.find('img').attr('data-height', attachment.height);
                        wrap.find('img').attr('data-width', attachment.width);

                        if ($('.image-wrap img').attr('data-height') < 800 || $('.image-wrap img').attr('data-width') < 800) {
                            $('#image_size_warning').show();
                            $('.image-wrap').removeClass('no_warning');
                        } else {
                            wrap.find('img').attr('src', attachment.url);
                            $('#image_size_warning').hide();
                            $('.image-wrap').addClass('no_warning');
                        }

                        instruction.addClass('waa-hide');
                        wrap.removeClass('waa-hide');
                    });
                });

                product_featured_frame.open();
            },

            removeImage: function (e) {
                e.preventDefault();

                var self = $(this);
                var wrap = self.closest('.image-wrap');
                var instruction = wrap.siblings('.instruction-inside');

                instruction.find('input.waa-feat-image-id').val('0');
                wrap.addClass('waa-hide');
                instruction.removeClass('waa-hide');
            }
        },

        fileDownloadable: function (e) {
            e.preventDefault();

            var self = $(this),
                downloadable_frame;

            if (downloadable_frame) {
                downloadable_frame.open();
                return;
            }

            downloadable_frame = wp.media({
                title: 'Choose a file',
                button: {
                    text: 'Insert file URL',
                },
                multiple: true
            });

            downloadable_frame.on('select', function () {
                var selection = downloadable_frame.state().get('selection');

                selection.map(function (attachment) {
                    attachment = attachment.toJSON();

                    self.closest('tr').find('input.wc_file_url').val(attachment.url);
                });
            });

            downloadable_frame.on('ready', function () {
                downloadable_frame.uploader.options.uploader.params = {
                    type: 'downloadable_product'
                };
            });

            downloadable_frame.open();
        },

        sidebarToggle: {
            showStatus: function (e) {
                var container = $(this).siblings('.waa-toggle-select-container');

                if (container.is(':hidden')) {
                    container.slideDown('fast');

                    $(this).hide();
                }

                return false;
            },

            saveStatus: function (e) {
                var container = $(this).closest('.waa-toggle-select-container');

                container.slideUp('fast');
                container.siblings('a.waa-toggle-edit').show();

                // update the text
                var text = $('option:selected', container.find('select.waa-toggle-select')).text();
                container.siblings('.waa-toggle-selected-display').html(text);

                return false;
            },

            cancel: function (e) {
                var container = $(this).closest('.waa-toggle-select-container');

                container.slideUp('fast');
                container.siblings('a.waa-toggle-edit').show();

                return false;
            }
        },

        shipping: {
            disableOverride: function () {
                if ($('#_disable_shipping').is(':checked')) {
                    $('.hide_if_disable').hide();
                } else {
                    $('.hide_if_disable').show();
                    waa_Editor.newProductDesign.shipping.showHideOverride();
                }

            }
        }
    };

    // On DOM ready
    $(function () {
        waa_Editor.init();
        $('#_product_type').trigger('change');
        $('.sale-schedule').trigger('change');
        $('#_manage_stock').trigger('change');
        $('#_required_shipping').trigger('change');
        $('#_disable_shipping').trigger('change');
        $('#_required_tax').trigger('change');
        $('#_has_attribute').trigger('change');
        $('#_create_variation').trigger('change');
        $('input[type=checkbox].waa_create_variation').trigger('change');
        $('#_downloadable').trigger('change');
        $('input.variable_is_downloadable, input.variable_is_virtual, input.variable_manage_stock').change();
    });

})(jQuery);
;(function ($) {

    var waa_Comments = {

        init: function () {
            $('#waa-comments-table').on('click', '.waa-cmt-action', this.setCommentStatus);
            $('#waa-comments-table').on('click', 'button.waa-cmt-close-form', this.closeForm);
            $('#waa-comments-table').on('click', 'button.waa-cmt-submit-form', this.submitForm);
            $('#waa-comments-table').on('click', '.waa-cmt-edit', this.populateForm);
            $('.waa-check-all').on('click', this.toggleCheckbox);
        },

        toggleCheckbox: function () {
            $(".waa-check-col").prop('checked', $(this).prop('checked'));
        },

        setCommentStatus: function (e) {
            e.preventDefault();

            var self = $(this),
                comment_id = self.data('comment_id'),
                comment_status = self.data('cmt_status'),
                page_status = self.data('page_status'),
                post_type = self.data('post_type'),
                curr_page = self.data('curr_page'),
                tr = self.closest('tr'),
                data = {
                    'action': 'waa_comment_status',
                    'comment_id': comment_id,
                    'comment_status': comment_status,
                    'page_status': page_status,
                    'post_type': post_type,
                    'curr_page': curr_page,
                    'nonce': waa.nonce
                };


            $.post(waa.ajaxurl, data, function (resp) {

                if (page_status === 1) {
                    if (comment_status === 1 || comment_status === 0) {
                        tr.fadeOut(function () {
                            tr.replaceWith(resp.data['content']).fadeIn();
                        });

                    } else {
                        tr.fadeOut(function () {
                            $(this).remove();
                        });
                    }
                } else {
                    tr.fadeOut(function () {
                        $(this).remove();
                    });
                }

                if (resp.data['pending'] == null) resp.data['pending'] = 0;
                if (resp.data['spam'] == null) resp.data['spam'] = 0;
                if (resp.data['trash'] == null) resp.data['trash'] = 0;

                $('.comments-menu-pending').text(resp.data['pending']);
                $('.comments-menu-spam').text(resp.data['spam']);
                $('.comments-menu-trash').text(resp.data['trash']);
            });
        },

        populateForm: function (e) {
            e.preventDefault();

            var tr = $(this).closest('tr');

            // toggle the edit area
            if (tr.next().hasClass('waa-comment-edit-row')) {
                tr.next().remove();
                return;
            }

            var table_form = $('#waa-edit-comment-row').html(),
                data = {
                    'author': tr.find('.waa-cmt-hid-author').text(),
                    'email': tr.find('.waa-cmt-hid-email').text(),
                    'url': tr.find('.waa-cmt-hid-url').text(),
                    'body': tr.find('.waa-cmt-hid-body').text(),
                    'id': tr.find('.waa-cmt-hid-id').text(),
                    'status': tr.find('.waa-cmt-hid-status').text(),
                };


            tr.after(_.template(table_form, data));
        },

        closeForm: function (e) {
            e.preventDefault();

            $(this).closest('tr.waa-comment-edit-row').remove();
        },

        submitForm: function (e) {
            e.preventDefault();

            var self = $(this),
                parent = self.closest('tr.waa-comment-edit-row'),
                data = {
                    'action': 'waa_update_comment',
                    'comment_id': parent.find('input.waa-cmt-id').val(),
                    'content': parent.find('textarea.waa-cmt-body').val(),
                    'author': parent.find('input.waa-cmt-author').val(),
                    'email': parent.find('input.waa-cmt-author-email').val(),
                    'url': parent.find('input.waa-cmt-author-url').val(),
                    'status': parent.find('input.waa-cmt-status').val(),
                    'nonce': waa.nonce,
                    'post_type': parent.find('input.waa-cmt-post-type').val(),
                };

            $.post(waa.ajaxurl, data, function (res) {
                if (res.success === true) {
                    parent.prev().replaceWith(res.data);
                    parent.remove();
                } else {
                    alert(res.data);
                }
            });
        }
    };

    $(function () {

        waa_Comments.init();
    });

})(jQuery);
jQuery(function ($) {

    $('.datepicker').datepicker({
        dateFormat: 'dd.mm.yy'
    });

    $('.tips').tooltip();

    // set dashboard menu height
    var dashboardMenu = $('ul.waa-dashboard-menu'),
        contentArea = $('.waa-dashboard-content');

    if ($(window).width() > 767) {
        if (contentArea.height() > dashboardMenu.height()) {
            dashboardMenu.css({height: contentArea.height()});
        }
    }

    function showTooltip(x, y, contents) {
        jQuery('<div class="chart-tooltip">' + contents + '</div>').css({
            top: y - 16,
            left: x + 20
        }).appendTo("body").fadeIn(200);
    }

    var prev_data_index = null;
    var prev_series_index = null;

    jQuery(".chart-placeholder").bind("plothover", function (event, pos, item) {
        if (item) {
            if (prev_data_index != item.dataIndex || prev_series_index != item.seriesIndex) {
                prev_data_index = item.dataIndex;
                prev_series_index = item.seriesIndex;

                jQuery(".chart-tooltip").remove();

                if (item.series.points.show || item.series.enable_tooltip) {

                    var y = item.series.data[item.dataIndex][1];

                    tooltip_content = '';

                    if (item.series.prepend_label)
                        tooltip_content = tooltip_content + item.series.label + ": ";

                    if (item.series.prepend_tooltip)
                        tooltip_content = tooltip_content + item.series.prepend_tooltip;

                    tooltip_content = tooltip_content + y;

                    if (item.series.append_tooltip)
                        tooltip_content = tooltip_content + item.series.append_tooltip;

                    if (item.series.pie.show) {

                        showTooltip(pos.pageX, pos.pageY, tooltip_content);

                    } else {

                        showTooltip(item.pageX, item.pageY, tooltip_content);

                    }

                }
            }
        } else {
            jQuery(".chart-tooltip").remove();
            prev_data_index = null;
        }
    });

});

// waa Register

jQuery(function ($) {
    $('.user-role input[type=radio]').on('change', function () {
        var value = $(this).val();

        if (value === 'seller') {
            $('.show_if_seller').slideDown();
            $('input[type="submit"]').val('Künstlerkonto anlegen');
        } else {
            $('.show_if_seller').slideUp();
            $('input[type="submit"]').val('Kundenkonto anlegen');
        }
    });

    $('#last-name').on('focusout', function () {
        var value = $('#first-name').val() + ' ' + $(this).val();
        $('#company-name').val(value);
        $('#company-name').focus();
    });


    $('#company-name').on('focusout', function () {
        var value = $(this).val().toLowerCase().replace(/-+/g, '').replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '');
        $('#seller-url').val(value);
        $('#url-alart').text(value);
        $('#seller-url').focus();
    });

    $('#seller-url').keydown(function (e) {
        var text = $(this).val();

        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 91, 109, 110, 173, 189, 190]) !== -1 ||
                // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) ||
                // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
            // let it happen, don't do anything
            return;
        }

        if ((e.shiftKey || (e.keyCode < 65 || e.keyCode > 90) && (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    $('#seller-url').keyup(function (e) {
        $('#url-alart').text($(this).val());
    });

    $('#shop-phone').keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 91, 107, 109, 110, 187, 189, 190]) !== -1 ||
                // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) ||
                // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
            // let it happen, don't do anything
            return;
        }

        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    $('#seller-url').on('focusout', function () {
        var self = $(this),
            data = {
                action: 'shop_url',
                url_slug: self.val(),
                _nonce: waa.nonce,
            };

        if (self.val() === '') {
            return;
        }

        var row = self.closest('.form-row');
        row.block({
            message: null,
            overlayCSS: {background: '#fff url(' + waa.ajax_loader + ') no-repeat center', opacity: 0.6}
        });

        $.post(waa.ajaxurl, data, function (resp) {

            if (resp == 0) {
                $('#url-alart').removeClass('text-success').addClass('text-danger');
                $('#url-alart-mgs').removeClass('text-success').addClass('text-danger').text(waa.seller.notAvailable);
            } else {
                $('#url-alart').removeClass('text-danger').addClass('text-success');
                $('#url-alart-mgs').removeClass('text-danger').addClass('text-success').text(waa.seller.available);
            }

            row.unblock();

        });

    });
});

//waa settings

(function ($) {

    $.validator.setDefaults({ignore: ":hidden"});

    var validatorError = function (error, element) {
        var form_group = $(element).closest('.form-group');
        form_group.addClass('has-error').append(error);
    };

    var validatorSuccess = function (label, element) {
        $(element).closest('.form-group').removeClass('has-error');
    };

    var waa_Settings = {
        init: function () {
            var self = this;

            //image upload
            $('a.waa-banner-drag').on('click', this.imageUpload);
            $('a.waa-remove-banner-image').on('click', this.removeBanner);

            $('a.waa-gravatar-drag').on('click', this.gragatarImageUpload);
            $('a.waa-remove-gravatar-image').on('click', this.removeGravatar);

            this.validateForm(self);

            return false;
        },


        imageUpload: function (e) {
            e.preventDefault();

            var file_frame,
                self = $(this);

            // If the media frame already exists, reopen it.
            if (file_frame) {
                file_frame.open();
                return;
            }

            // Create the media frame.
            file_frame = wp.media.frames.file_frame = wp.media({
                title: jQuery(this).data('uploader_title'),
                button: {
                    text: jQuery(this).data('uploader_button_text')
                },
                multiple: false
            });

            // When an image is selected, run a callback.
            file_frame.on('select', function () {
                var attachment = file_frame.state().get('selection').first().toJSON();

                var wrap = self.closest('.waa-banner');
                wrap.find('input.waa-file-field').val(attachment.id);
                wrap.find('img.waa-banner-img').attr('src', attachment.url);
                self.parent().siblings('.image-wrap', wrap).removeClass('waa-hide');

                self.parent('.button-area').addClass('waa-hide');
            });

            // Finally, open the modal
            file_frame.open();

        },
        gragatarImageUpload: function (e) {
            e.preventDefault();

            var file_frame,
                self = $(this);

            // If the media frame already exists, reopen it.
            if (file_frame) {
                file_frame.open();
                return;
            }

            // Create the media frame.
            file_frame = wp.media.frames.file_frame = wp.media({
                title: jQuery(this).data('uploader_title'),
                button: {
                    text: jQuery(this).data('uploader_button_text')
                },
                multiple: false
            });

            // When an image is selected, run a callback.
            file_frame.on('select', function () {
                var attachment = file_frame.state().get('selection').first().toJSON();

                var wrap = self.closest('.waa-gravatar');
                wrap.find('input.waa-file-field').val(attachment.id);
                wrap.find('img.waa-gravatar-img').attr('src', attachment.url);
                self.parent().siblings('.gravatar-wrap', wrap).removeClass('waa-hide');
                self.parent('.gravatar-button-area').addClass('waa-hide');

            });

            // Finally, open the modal
            file_frame.open();

        },

        submitSettings: function (form_id) {

            if (typeof tinyMCE != 'undefined') {
                tinyMCE.triggerSave();
            }

            var self = $("form#" + form_id),
                form_data = self.serialize() + '&action=waa_settings&form_id=' + form_id;

            self.find('.ajax_prev').append('<span class="waa-loading"> </span>');
            $.post(waa.ajaxurl, form_data, function (resp) {

                self.find('span.waa-loading').remove();
                $('html,body').animate({scrollTop: 100});

                if (resp.success) {
                    // Harcoded Customization for template-settings function
                    $('.waa-ajax-response').html($('<div/>', {
                        'class': 'waa-alert waa-alert-success',
                        'html': '<p>' + resp.data.msg + '</p>',
                    }));

                    $('.waa-ajax-response').append(resp.data.progress);

                } else {
                    $('.waa-ajax-response').html($('<div/>', {
                        'class': 'waa-alert waa-alert-danger',
                        'html': '<p>' + resp.data + '</p>'
                    }));
                }
            });
        },

        validateForm: function (self) {

            $("form#settings-form, form#profile-form, form#store-form, form#payment-form").validate({
                //errorLabelContainer: '#errors'
                submitHandler: function (form) {
                    self.submitSettings(form.getAttribute('id'));
                },
                errorElement: 'span',
                errorClass: 'error',
                errorPlacement: validatorError,
                success: validatorSuccess
            });

        },

        removeBanner: function (e) {
            e.preventDefault();

            var self = $(this);
            var wrap = self.closest('.image-wrap');
            var instruction = wrap.siblings('.button-area');

            wrap.find('input.waa-file-field').val('0');
            wrap.addClass('waa-hide');
            instruction.removeClass('waa-hide');
        },

        removeGravatar: function (e) {
            e.preventDefault();

            var self = $(this);
            var wrap = self.closest('.gravatar-wrap');
            var instruction = wrap.siblings('.gravatar-button-area');

            wrap.find('input.waa-file-field').val('0');
            wrap.addClass('waa-hide');
            instruction.removeClass('waa-hide');
        },
    };

    var waa_Withdraw = {

        init: function () {
            var self = this;

            this.withdrawValidate(self);
        },

        withdrawValidate: function (self) {
            $('form.withdraw').validate({
                //errorLabelContainer: '#errors'

                errorElement: 'span',
                errorClass: 'error',
                errorPlacement: validatorError,
                success: validatorSuccess
            })
        }
    };

    var waa_Coupons = {
        init: function () {
            var self = this;
            this.couponsValidation(self);
        },

        couponsValidation: function (self) {
            $("form.coupons").validate({
                errorElement: 'span',
                errorClass: 'error',
                errorPlacement: validatorError,
                success: validatorSuccess
            });
        }
    };

    var waa_Seller = {
        init: function () {
            this.validate(this);
        },

        validate: function (self) {
            // e.preventDefault();

            $('form#waa-form-contact-seller').validate({
                errorPlacement: validatorError,
                success: validatorSuccess,
                submitHandler: function (form) {

                    $(form).block({
                        message: null,
                        overlayCSS: {background: '#fff url(' + waa.ajax_loader + ') no-repeat center', opacity: 0.6}
                    });

                    var form_data = $(form).serialize();
                    $.post(waa.ajaxurl, form_data, function (resp) {
                        $(form).unblock();

                        if (typeof resp.data !== 'undefined') {
                            $(form).find('.ajax-response').html(resp.data);
                        }

                        $(form).find('input[type=text], input[type=email], textarea').val('').removeClass('valid');
                    });
                }
            });
        }
    };

    var waa_Add_Seller = {
        init: function () {
            this.validate(this);
        },

        validate: function (self) {

            $('form.register').validate({
                errorPlacement: validatorError,
                success: validatorSuccess,
                submitHandler: function (form) {
                    form.submit();
                }
            });
        }
    };

    $(function () {
        waa_Settings.init();
        waa_Withdraw.init();
        waa_Coupons.init();
        waa_Seller.init();
        waa_Add_Seller.init();
    });

})(jQuery);

// Shipping tab js
(function ($) {
    $(document).ready(function () {

        $('.waa-shipping-location-wrapper').on('change', '.dps_country_selection', function () {
            var self = $(this),
                data = {
                    country_id: self.find(':selected').val(),
                    action: 'dps_select_state_by_country'
                };

            if (self.val() == '' || self.val() == 'everywhere') {
                self.closest('.dps-shipping-location-content').find('table.dps-shipping-states tbody').html('');
            } else {
                $.post(waa.ajaxurl, data, function (response) {
                    if (response.success) {
                        self.closest('.dps-shipping-location-content').find('table.dps-shipping-states tbody').html(response.data);
                    }
                });
            }
        });

    });
})(jQuery);

// For Announcement scripts;
(function ($) {

    $(document).ready(function () {
        $('.waa-announcement-wrapper').on('click', 'a.remove_announcement', function (e) {
            e.preventDefault();

            if (confirm(waa.delete_confirm)) {

                var self = $(this),
                    data = {
                        'action': 'waa_announcement_remove_row',
                        'row_id': self.data('notice_row'),
                        '_wpnonce': waa.nonce
                    };
                self.closest('.waa-announcement-wrapper-item').append('<span class="waa-loading" style="position:absolute;top:2px; right:15px"> </span>');
                var row_count = $('.waa-announcement-wrapper-item').length;
                $.post(waa.ajaxurl, data, function (response) {
                    if (response.success) {
                        self.closest('.waa-announcement-wrapper-item').find('span.waa-loading').remove();
                        self.closest('.waa-announcement-wrapper-item').fadeOut(function () {
                            $(this).remove();
                            if (row_count == 1) {
                                $('.waa-announcement-wrapper').html(response.data);
                            }
                        });
                    } else {
                        alert(waa.wrong_message);
                    }
                });
            }

        });
    });

})(jQuery);
//waa store seo form submit
(function ($) {

    var wrapper = $('.waa-dashboard-content.waa-settings-content.waa-store-seo-wrapper');
    var waa_Store_SEO = {

        init: function () {
            wrapper.on('click', 'input#waa-store-seo-form-submit', this.form.validate);
        },
        form: {

            validate: function () {
                var self = $(this),
                    data = {
                        action: 'waa_seo_form_handler',
                        data: self.closest('#waa-store-seo-form').serialize(),
                    };
                console.log(data.data);
                waa_Store_SEO.form.submit(data);

                return false;
            },

            submit: function (data) {
                var feedback = $('#waa-seo-feedback');
                feedback.fadeOut();

                $.post(waa.ajaxurl, data, function (resp) {
                    if (resp.success == true) {
                        feedback.html(resp.data);
                        feedback.removeClass('waa-hide');
                        feedback.addClass('waa-alert-success');
                        feedback.fadeIn();
                    } else {
                        feedback.html(resp.data);
                        feedback.addClass('waa-alert-danger');
                        feedback.removeClass('waa-hide');
                        feedback.fadeIn();
                    }
                })
            }

        },
    };

    $(function () {
        waa_Store_SEO.init();
    });

})(jQuery);

//localize Validation messages
(function ($) {
    var waa_messages = waaValidateMsg;

    waa_messages.maxlength = $.validator.format(waa_messages.maxlength_msg);
    waa_messages.minlength = $.validator.format(waa_messages.minlength_msg);
    waa_messages.rangelength = $.validator.format(waa_messages.rangelength_msg);
    waa_messages.range = $.validator.format(waa_messages.range_msg);
    waa_messages.max = $.validator.format(waa_messages.max_msg);
    waa_messages.min = $.validator.format(waa_messages.min_msg);

    $.validator.messages = waa_messages;

    $(document).on('click', '#waa_store_tnc_enable', function (e) {
        if ($(this).is(':checked')) {
            $('#waa_tnc_text').show();
        } else {
            $('#waa_tnc_text').hide();
        }
    }).ready(function (e) {
        if ($('#waa_store_tnc_enable').is(':checked')) {
            $('#waa_tnc_text').show();
        } else {
            $('#waa_tnc_text').hide();
        }
    });

    jQuery("#pa_stil").attr("required", true);


    // add variation row for prints
    var $button = $('#add-row'),
        $row = $('.print-variation').clone();

    $button.click(function (e) {
        e.preventDefault();
        $row.clone().insertBefore($('.add-print-btn'));
    });

    $('.btn-remove-new-print').live('click', function (e) {
        e.preventDefault();
        $(this).parent().parent().remove();
    });

    $('#print-size').keydown(function (e) {
        var text = $(this).val();

        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 91, 88]) !== -1 ||
                // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) ||
                // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
            // let it happen, don't do anything
            return;
        }

        if ((e.keyCode < 48 || e.keyCode > 57 || e.keyCode == 88) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
            $('#print-size-alert').css('display', 'block');
        } else {

        }
    });

    $('#_has_attribute').on('change', function () {
        if ($(this).is(':checked')) {
            $('#_create_variation').val('yes');
            $('#waa_create_new_variations').val('yes');
            $('.hide_if_no_variation').show();
        }
        else {
            $('#_create_variation').val('no');
            $('#waa_create_new_variations').val('no');
            $('#waa_only_print').attr('checked', false);
            $('.hide_if_no_variation').hide();
        }
    });

    $('#waa_only_print').on('change', function () {
        if ($(this).is(':checked')) {
            $('.hide_if_only_print').show();
        }
        else {
            $('.hide_if_only_print').hide();
        }
    });

    $('.waa-input-group-addon').click(function () {
        $(this).next('input').focus();
    });

    $('#_regular_price').focusout(function () {
        $('#original-price').val($(this).val());
    });

    $('.waa-product-edit-form').submit(function (e) {
        var image_val = $('.waa-feat-image-id').val();
        if (image_val == 0) {
            alert('Bitte lade ein Foto von deinem Kunstwerk hoch.');
            return false;
        }
        var image_height = $('.image-wrap img').height();
        var image_width = $('.image-wrap img').height();
    });


})(jQuery);
