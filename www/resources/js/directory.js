$(function() {
	$(document).ready(function() {
		$.system.__init();
	});
});
(function($){
	var system = {
		__init : function() {
			// opeb image edit window
			$('.item-edit-image, .item-edit-attachment, .item-edit-video, .item-edit-audio').click(function() {
				var $this = $(this);
				$.fancybox({
					type: 'iframe',
					href: $this.attr('href') + '?modal=1',
					padding: 8,
					width: 800,
					height: 500,
					afterShow : function() {
						var $content = $('.fancybox-inner');
						if($content.find('.elrte').length) {
							$content.find('.elrte').each(function() {
								system.editor($(this), {'size': 'small'});
							});
						}
					}
				});

				return false;
			});

			if($('.sortable, .sortable-y').length) {
				$('.sortable, .sortable-y').each(function() {
					var axis = false;
					if($(this).hasClass('sortable-y')) {
						axis = 'y';
					}
					system.sortable($(this), {'axis': axis});
				});
			}

			if($('.elrte').length) {
				if($('body').hasClass('body-iframe')) {
					$('.elrte').each(function() {
						system.editor($(this), {size: 'small'});
					});
				} else {
					$('.elrte').each(function() {
						system.editor($(this));
					});
				}
			}
			if($('.DatePicker').length) {
				$('.DatePicker').datepicker({
					duration: '',
					showTime: false,
					constrainInput: true,
					dateFormat: 'yy-mm-dd',
					time24h: true
				});
			}
			if($('.TimePicker').length) {
				$('.TimePicker').datepicker({
					duration: '',
					showTime: true,
					constrainInput: true,
					dateFormat: 'yy-mm-dd',
					time24h: true
				});
			}

			$('[confirm]').click(function() {
				var text = $(this).attr('confirm');
				if(text.length == 0) {
					text = 'Are you sure you want to do this?';
				}
				return confirm(text);
			});

			if($('.gallery').length) {
				$('.gallery').fancybox();
			}
			if($('.video').length) {
				$('.video').fancybox({
					type: 'iframe'
				});
			}
		},
		sortable : function($item, options) {
			var axis = false;
			if(typeof(options) != 'undefined') {
				axis = typeof(options.axis) != 'undefined' ? options.axis : false;
			}

			$item.sortable({
				'axis': axis,
				handle: $('.sortable-handler'),
				update : function()
				{
					var serial = $(this).sortable("serialize");
					serial += '&sorting=1';
					$.ajax({
						type: "POST",
						data: serial,
						dataType: "json",
						success: function(data) {
							$.admin.message(data.answer, 1000);
						},
						error: function() {
							$.admin.message('Error');
						}
					});
				}
			});
		},

		editor : function($item, options) {
			var toolbars = {
				newToolbar : ['cp', 'heading', 'font', 'style', 'indent', 'alignment', 'lists', 'colors', 'mediae', 'links', 'elementse', 'tables']
			};
			var panels = {
				cp : ['pastetext','removeformat'],
				heading : ['formatblock'],
				font : ['fontsize'],
				elementse : ['horizontalrule'],
				mediae : []
			};

			var height = 300;
			var fullSize = true;

			if(typeof(options) != 'undefined') {
				if(typeof(options.size != 'undefined') && options.size == 'small') {
					fullSize = false;
					height = 200;
					toolbars = {
						newToolbar : ['cp', 'heading', 'font', 'style', 'indent', 'alignment', 'lists', 'colors', 'links', 'elementse']
					};
				}
			}

			var fm = false;
			if(fullSize) {
				if(typeof($item.attr('data-images')) != 'undefined' || typeof($item.attr('data-attachments')) != 'undefined') {
					if($item.attr('data-images') != 'undefined') {
						panels.mediae = ['image'];
					}

					fm = function(callback) {
						$.fancybox({
							type: 'ajax',
							href: $item.attr('data-images'),
							width: 800,
							height: 450,
							padding: 8,
							autoSize: false,
							afterShow : function() {
								var $content = $('.fancybox-inner').children();
								var $filesBlock = $content.find('#files-list');
								var $navBlock = $content.find('.content-nav');
								var $closeBtn = $content.find('.selectButton');
								var closeBtnHint = $closeBtn.attr('eva-content');
								$content.find('.element').click(function() {
									$content.find('.element').not($(this)).removeClass('active');
									$(this).toggleClass('active');
									if($(this).hasClass('active')) {
										$closeBtn.removeClass('disabled').attr('eva-content', 'Click to insert file');
									} else {
										$closeBtn.addClass('disabled').attr('eva-content', closeBtnHint);
									}

									return false;
								});
								$content.find('.content-nav').children('a').click(function() {
									$navBlock.children('a').not($(this)).removeClass('active');
									$(this).addClass('active');
									$filesBlock.children().not($(this).attr('href')).removeClass('active');
									$filesBlock.children($(this).attr('href')).addClass('active');
								});
								$content.find('.btn-ok').click(function() {
									if($(this).hasClass('disabled')) {
										return false;
									}
									var $active = $content.find('.element.active');
									callback($active.attr('data-href'));
									$.fancybox.close();

									return false;
								})
							}
						});
					}
				}
			}

			$item.elrte({
				height : height,
				cssfiles : ['/resources/css/libs/elrte/editor.css'],
				panels : panels,
				toolbars : toolbars,
				toolbar : 'newToolbar',
				absoluteURLs : false,
				fmOpen : fm
			});
		},

		uploader : function($id, exts, limit) {
			var uploader = new qq.FileUploader({
				element: $id.get(0),
				action: actionLink,
				sizeLimit: 10485760,
				allowedExtensions: ['jpg', 'jpeg', 'png', 'gif', 'rar', 'zip', 'doc', 'docx', 'xls', 'xlsx', 'pdf', 'txt'],
				template: '<div class="qq-uploader">' +
						'<div class="qq-upload-button">' +
						'</div>' +
						'<div class="qq-upload-drop-area"><span>Drop files here to upload</span></div>' +
						'<ul class="qq-upload-list"></ul>' +
					'</div>',
				fileTemplate:   '<li>' +
							'<span class="qq-upload-file"></span>' +
							'<span class="qq-upload-spinner"></span>' +
							'<span class="qq-upload-size"></span>' +
							'<a class="qq-upload-cancel" href="#">Cancel</a>' +
							'<span class="qq-upload-failed-text">Failed</span>' +
						'</li>',
				onSubmit: function(id, actionLink) {
					$(this.element).closest('form').attr('disabled', 'disabled');
					$('.qq-uploader').find('.qq-upload-list').show();
				},
				onComplete: function(id, fileName, responseJSON){
					if(typeof(onComplete) != 'undefined') {
						onComplete(id, fileName, responseJSON);
					}

					var item = uploader._getItemByFileId(id);

					if(typeof(responseJSON['success']) != 'undefined' && responseJSON['success']) {
						$(item).append('<a class="qq-upload-remove" href="'+removeLink+responseJSON['token']+'/">Remove</a>');
						var uploaderForm = $(item).closest('form').append('<input type="hidden" class="attachments_ids" name="attachments[]" value="'+responseJSON.id+'" />');
					}

					$(this.element).closest('form').attr('disabled', false);
				},
				onError: function(){
					$(this.element).closest('form').attr('disabled', false);
				}
			});

			return uploader;
		}
	}

	$.extend({
		system:system
	});
})(jQuery)

function closeIframe() {
	$.fancybox.close();
	window.location.reload();
}