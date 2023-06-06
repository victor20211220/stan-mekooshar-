CKEDITOR.plugins.add('gallery', {
	requires: 'widget',
	icons: 'gallery',
	init: function(editor) {
		editor.widgets.add('gallery', {
			button: editor.lang.gallery.addGallery,
			draggable: true,
			showCaption: true,
			template: '<ul class="gallery">' +
					'<li class="ui-state-disabled"><a href="#" class="addImage">+</a><span href="#" class="removeGallery">x</span></li>' +
				'</ul>',
			editables: {
				content: {
					selector: '.gallery',
					allowedContent: ''
				}
			},
			allowedContent: 'ul(!gallery); li[id]; ul[data-id]; div(gallery-image-actions); a(addImage); a(remove); a(edit); span(removeGallery); li(ui-state-disabled); p; img[data-id]; img[src]; img[width]; img[height]',
			requiredContent: 'ul(gallery)',
			upcast: function(element) {
				return element.name == 'ul' && element.hasClass('gallery');
			},
			init: function() {
				var $gallery = $(this.element.$);
				var widget = this;

				//get itemId
				var itemId = $('form').data('id');

				//get gallery id from server
				if (typeof ($gallery.data('id')) == 'undefined') {
					var xhr = $.ajax({
						type: 'POST',
						url: '/uploader/files/GetGalleryId/' + itemId + '/',
						dataType: 'json',
						data: {article_id: window.article_id}
					}).done(function(data) {
						if (data.id) {
							$gallery.attr('data-id', data.id);
							editorGallery.initGalleryEvents($gallery, widget);
						}
					});
				} else {
					editorGallery.initGalleryEvents($gallery, widget);
				}
				editorGallery.setDraggable($gallery);

				this.on('destroyGalley', function() {
					var gallery_id = $gallery.data('id');
					var xhr = $.ajax({
						type: 'POST',
						url: '/uploader/files/removeGallery/' + itemId + '/',
						dataType: 'json',
						data: {gallery_id: gallery_id}
					});

					var $gallery_wrap = $gallery.closest('.cke_widget_wrapper');
					$gallery.remove();
					$gallery_wrap.remove();
				});
			}
		});
	}
});

