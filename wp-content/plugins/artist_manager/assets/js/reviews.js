;(function($){

    var waa_Comments = {

        init: function() {
            $('#waa-comments-table').on('click', '.waa-cmt-action', this.setCommentStatus);
            $('#waa-comments-table').on('click', 'button.waa-cmt-close-form', this.closeForm);
            $('#waa-comments-table').on('click', 'button.waa-cmt-submit-form', this.submitForm);
            $('#waa-comments-table').on('click', '.waa-cmt-edit', this.populateForm);
            $('.waa-check-all').on('click', this.toggleCheckbox);
        },

        toggleCheckbox: function() {
            $(".waa-check-col").prop('checked', $(this).prop('checked'));
        },

        setCommentStatus: function(e) {
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


            $.post(waa.ajaxurl, data, function(resp){

                if(page_status === 1) {
                    if ( comment_status === 1 || comment_status === 0) {
                        tr.fadeOut(function() {
                            tr.replaceWith(resp.data['content']).fadeIn();
                        });

                    } else {
                        tr.fadeOut(function() {
                            $(this).remove();
                        });
                    }
                } else {
                    tr.fadeOut(function() {
                        $(this).remove();
                    });
                }

                if(resp.data['pending'] == null) resp.data['pending'] = 0;
                if(resp.data['spam'] == null) resp.data['spam'] = 0;
				if(resp.data['trash'] == null) resp.data['trash'] = 0;

                $('.comments-menu-pending').text(resp.data['pending']);
                $('.comments-menu-spam').text(resp.data['spam']);
				$('.comments-menu-trash').text(resp.data['trash']);
            });
        },

        populateForm: function(e) {
            e.preventDefault();

            var tr = $(this).closest('tr');

            // toggle the edit area
            if ( tr.next().hasClass('waa-comment-edit-row')) {
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


            tr.after( _.template(table_form, data) );
        },

        closeForm: function(e) {
            e.preventDefault();

            $(this).closest('tr.waa-comment-edit-row').remove();
        },

        submitForm: function(e) {
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
					'post_type' : parent.find('input.waa-cmt-post-type').val(),
                };

            $.post(waa.ajaxurl, data, function(res) {
                if ( res.success === true) {
                    parent.prev().replaceWith(res.data);
                    parent.remove();
                } else {
                    alert( res.data );
                }
            });
        }
    };

    $(function(){

        waa_Comments.init();
    });

})(jQuery);