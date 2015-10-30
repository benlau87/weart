// jQuery
 jQuery(function($){
 
    var $container = $('ul.products');   
    $container.imagesLoaded( function(){
      $container.masonry({
        itemSelector : 'li.product',
				columnWidth: '.grid-sizer',
				gutter: '.gutter-sizer'
      });
    });
		
		var $masonry_container = $('.masonry-grid')   
    $masonry_container.imagesLoaded( function(){
      $masonry_container.masonry({
        itemSelector : 'article.type-post',
				columnWidth: '.grid-sizer-masonry',
				gutter: '.gutter-sizer-masonry'
      });
    });
		
		$("ul.products li.product").click(function() {
			window.location = $(this).find("a").attr("href"); 
			return false;
		});
		$("ul.products li.product a").click(function(e){
			e.stopPropagation();
			//show tooltip
		});
  
		$('#artists-page .product.type-product').each(function() {
			if($(this).height() < 175) {
				$(this).find('.bottom').hide();
				$(this).find('.page .entry_author_image').css('width', '75px');
				$(this).find('.page .entry_author_image').css('height', '75px');
		});		
	
});