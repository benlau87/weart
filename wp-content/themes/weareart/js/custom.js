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
  
});