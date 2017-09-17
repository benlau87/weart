jQuery(function($) {
	$( ".fields.library .columns .field" ).draggable({
		connectToSortable: '#documents .field-list.columns',
		helper: 'clone'
	});
	$( ".fields.library .totals .field" ).draggable({
		connectToSortable: '#documents .field-list.totals',
		helper: 'clone'
	});

	$( "#documents .field-list" ).sortable({
		// placeholder: 'sortable-placeholder',
		items: '.field',
		cursor: 'move',
		receive: function(event,ui) {
			// get dropped item
			var sortable_data = $(this).data();
			if (typeof sortable_data.uiSortable != 'undefined') {
				var dropped_item = sortable_data.uiSortable.currentItem;
			} else if (typeof sortable_data.sortable != 'undefined') {
				var dropped_item = sortable_data.sortable.currentItem;
			}

			// console.log(dropped_item);
			var uniquekey = new Date().getTime();
			var data = $(dropped_item).data();
			var option = $(this).data().option;
			var form_elements = $(dropped_item).find(':input');

			form_elements.each( function() {
				var key = $(this).data('key');
				$(this).attr('name', option+'['+uniquekey+']['+key+']')
			});

			dropped_item.removeAttr('style');
			// console.log(dropped_item);
			dropped_item.find('.ui-accordion-header-icon').remove();
			if (dropped_item.hasClass('options')) {
				dropped_item.accordion({
					header: '.field-title',
					collapsible: true,
					active: false
				});
			};

		}
	});

	$( document ).on( "click", ".delete-field", function() { 
		$(this).parent().remove();
	});

	// hide & disable input fields based on type selection
	$( '.custom-blocks' ).on( 'change', 'select.custom-block-type', function () {
		var option = $( this ).val();
		var $current_block = $( this ).closest('.custom-block');
		var $meta_key = $current_block.find('.meta_key');
		var $custom_text = $current_block.find('.custom_text');
		var $hide_if_empty = $current_block.find('.hide_if_empty');
		if ( option == 'custom_field' ) {
			$custom_text.find('textarea').val('').prop('disabled', true);
			$custom_text.hide();
			$meta_key.show().find('input').prop('disabled', false);
			$hide_if_empty.show().find('input').prop('disabled', false);
		} else {
			$meta_key.find('input').val('').prop('disabled', true);
			$meta_key.hide();
			$hide_if_empty.find('input').val('').prop('disabled', true);
			$hide_if_empty.hide();			
			$custom_text.show().find('textarea').prop('disabled', false);
		}
	})
	$( 'select.custom-block-type' ).change(); //ensure visible state matches initially


	$( '.document-content' ).on( "click", ".button.add-custom-block", function() { 
		// var section = $( this ).closest('.custom-block').data('section');
		var $current_doc = $( this ).closest('.document-content');
		var document_type = $current_doc.data('document-type');
		var data = {
			security:      wpo_wcpdf_templates.nonce,
			action:        'wcpdf_templates_add_custom_block',
			// section:       section,
			document_type: document_type,
		};

		xhr = $.ajax({
			type:		'POST',
			url:		wpo_wcpdf_templates.ajaxurl,
			data:		data,
			success:	function( data ) {
				// console.log( data );
				$current_doc.find('.custom-blocks').append( data );
				$current_doc.find('select.custom-block-type').change();
			}
		});
	});

	$( '.field.options' ).accordion({
		header: '.field-title',
		collapsible: true,
		active: false
	});

	$( '.fields.library' ).accordion({
		header: 'h4'
	});


	$( '#documents' ).tabs();
});
		