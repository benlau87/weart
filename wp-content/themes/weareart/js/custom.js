// jQuery
jQuery(document).ready(function ($) {

    // masonry-grid
    var $container = $('ul.products');
    $container.imagesLoaded(function () {
        $container.masonry({
            itemSelector: 'li.product',
            columnWidth: '.grid-sizer',
            gutter: '.gutter-sizer'
        });
    });

    var $masonry_container = $('.masonry-grid')
    $masonry_container.imagesLoaded(function () {
        $masonry_container.masonry({
            itemSelector: 'article.type-post',
            columnWidth: '.grid-sizer-masonry',
            gutter: '.gutter-sizer-masonry'
        });
    });

    var $li_product = $container.find('li.product');
    $li_product.click(function () {
        window.location = $(this).find("a").attr("href");
        return false;
    });
    $li_product.find("a").click(function (e) {
        e.stopPropagation();
        //show tooltip
    });

    // resize avatar picture, if art image to small
    /* $('#artists-page li.product.type-product').each(function() {
     if($(this).outerHeight() < 175) {
     $(this).find('.bottom').hide();
     $('.entry_author_image', this).css('width', '75px');
     $('.entry_author_image', this).css('height', '75px');
     }
     }); */

    $('.woocommerce .shop_table dd.variation-Gre p, .woocommerce .shop_table dd.variation-Material p').each(function () {
        if ($(this).text() == 'Original') {
            $(this).parent().hide();
            $(this).parent().prev('dt').hide();
        }
    });

    var $slider = $('.art-slider');
    if (window.chrome) {
        $slider.find('li').css('background-size', '100% 100%');
    }
    var unslider = $slider.unslider({
        fluid: true,
        dots: true,
        keys: true,
        delay: 7000
    });

    $('img').bind('contextmenu', function(e) {
        return false;
    });

    $('.tips').tooltip();


});

jQuery(document).load(function($) {
	var heights = $slider.map(function ()
		{
			return $(this).height();
		}).get(),

		maxHeight = Math.max.apply(null, heights);

	//$slider.height('initial');

	//$slider.height($slider.find('li:first-child').find('img').height());
	$slider.div.style('height', maxHeight, 'important');
});