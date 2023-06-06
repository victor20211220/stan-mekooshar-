uploader = {
	initializedCount: 0,
	init: function() {
		$(function() {
			$('.fileUploader.unInitialized').each(function() {
				var $this = $(this);
				var id = 'fileuploader-' + ++uploader.initializedCount;
				$this.attr('id', id);
				uploader.createUploader({
					id: $this.attr('id'),
					action: $this.attr('action'),
					maxSize: $this.attr('maxSize'),
					exts: $this.attr('exts').split(','),
					buttonName: $this.attr('buttonName').split(',')
				});
				$this.removeClass('unInitialized');
			});
		})
	},
	createUploader: function(params) {
		var uploader = new qq.FileUploader({
			element: document.getElementById(params.id),
			action: params.action,
			sizeLimit: params.maxSize,
			allowedExtensions: params.exts,
			template: '<div class="qq-uploader">' +
				'<div class="qq-upload-button-outer"><div title="Upload files" class="qq-upload-button btn btn-upload">' +
				'<div class="qq-upload-drop-area"><span>Drop files here to upload</span></div>' +
				params.buttonName +
				'</div></div>' +
				'<ul class="qq-upload-list"></ul>' +
				'</div>',
			fileTemplate: '<li>' +
				'<span class="qq-upload-file"></span>' +
				'<span class="qq-upload-spinner"></span>' +
				'<span class="qq-upload-size"></span>' +
				'<a class="qq-upload-cancel" href="#">Cancel</a>' +
				'<span class="qq-upload-failed-text">Failed</span>' +
				'</li>',
			onSubmit: function(id, fileName) {
//				$(this.element).closest('form').attr('disabled', 'disabled');
				$(this.element).find('.qq-upload-list').show();
				$(this.element).find('.qq-upload-button-outer').addClass('attached');
//				$(this.element).closest('form').attr('disabled', 'disabled');
//				$(this.element).find('.qq-upload-status').show().find('.loaded').css('width', 0);
			},
//			onProgress: function(id, fileName, loaded, total) {
			//$(this.element).find('.loaded').css('width', (100*(loaded/total))+'%');
//			},
			onComplete: function(id, fileName, responseJSON) {
				if (typeof(responseJSON['success']) != 'undefined' && responseJSON['success']) {
					var item = uploader._getItemByFileId(id);
					$(item).hide();
//					$(this.element).closest('.ajax-data');
//					console.log($('.uploaded_files'));
					$('.uploaded_files').append(responseJSON.form);
					if (typeof(responseJSON['style']) != 'undefined') {
						$.each(responseJSON['style'], function(key, value) {
							Files.LoadFile(key);
						});
					}
					if (typeof(responseJSON['scrypt']) != 'undefined') {
						$.each(responseJSON['scrypt'], function(key, value) {
							Files.LoadFile(value);
						});
					}
					Files.checkAllUpload();


//					console.log(responseJSON);
//					if(responseJSON['type'] == 'images') {
//						var imageUrl = responseJSON.url;
//
//						if(imageUrl == undefined) {
//							imageUrl = responseJSON.url_preview;
//						}
//						$(this.element).closest('.value').find('.images-content').append("<span class='images-field-value file-attachment' value='"+responseJSON.id+"'><span class='settings' ><a class='download' href='/files/download/"+responseJSON.token+"/'></a><a class='remove' href='/files/removeById/"+responseJSON.id+"/'>&times;</a></span><a class='img' href='"+responseJSON.url_fullsize+"'><img src='"+imageUrl+"'/></a></span>");
//					} else {
//						$(this.element).closest('.value').find('.files-content').append("<span class='file-field-value file-attachment' value='"+responseJSON.id+"'><span class='settings'><a class='remove' href='/files/removeById/"+responseJSON.id+"/'>&times;</a></span><a class='download' href='/files/download/"+responseJSON.token+"/'>"+responseJSON.name+"</a></span>");
//					}
				}
//				$(this.element).closest('form').attr('disabled', false);
			}
		});
	},
	upload: function(target, event) {
		$(target).closest('.thumb').find('.qq-uploader input[type=file]').trigger('click');
		return false;
	},
	set: function($uploader, fileId, src) {
		var $thumb = $uploader.closest('.thumb');
		var oldSrc = $thumb.find('img').attr('src');
		$thumb.find('img[src="' + oldSrc + '"]').each(function() {
			$(this).attr('src', src);
		});
		if ($thumb.find('.settings').hasClass('active')) {
			$('body').find('img[src="' + oldSrc + '"]').each(function() {
				$(this).attr('src', src);
			});
		}

		$thumb.find('.settings').addClass('active');
		$thumb.find('.crop-img').attr('href', '/files/crop/' + fileId + '/');
	},
	remove: function(target, event) {
		var $this = $(target);
		var $avaInput = $this.closest('form').find('input.avaIdInput');
		$.ajax({
			type: 'POST',
			url: $this.attr('href'),
			dataType: 'json',
			async: false,
			success: function(resp) {
				$this.closest('.thumb').find('img').attr('src', resp.image_preview);
				$this.closest('.thumb').find('.settings').removeClass('active');
			}
		});
		if ($avaInput.length) {
			$avaInput.val('');
		}

		return false;
	},
	submitItem: function(target) {
		$.ajax({
			type: 'POST',
			url: $(target).attr('action'),
			dataType: 'json',
			async: false,
			data: $(target).serialize(),
			success: function(responseJSON) {
				if (typeof(responseJSON['success']) != 'undefined' && responseJSON['success']) {
					var form = '#' + responseJSON.form;
					$(form).remove();
				} else if (typeof(responseJSON['success']) != 'undefined' && responseJSON['success'] === false) {
					var form = '#' + responseJSON.form;
					var form_data = responseJSON.form_data;
					$(form).before(form_data).remove();
				}
				uploader.checkAllSubmit();
			}
		});
		return false;
	},
	submitHeadForm: function() {
		var forms = $('.uploaded_files form');
		if ($(forms).size() < 1) {
			this.checkAllSubmit();
		} else {
			$.each(forms, function(key, value) {
				$(value).submit();
			})
		}

		return false;
	},
	checkAllSubmit: function() {
		var forms = $('.uploaded_files form');
		if ($(forms).size() < 1) {
			parent.location.reload();
		}
	},
	removeUploadFile: function(target) {
		$.ajax({
			type: 'POST',
			url: $(target).attr('href'),
			dataType: 'json',
			async: false,
			success: function(responseJSON) {
				if (typeof(responseJSON['success']) != 'undefined' && responseJSON['success']) {
					var form = '#' + responseJSON.form;
					$(form).remove();
				} else {
					alert('Erorr');
				}
			}
		});
		return false;
	}
}

var Files = {
	LoadFile: function(file) {
		var ext = file.match(/[^.]+$/); // розширення файла, після точки
		if (ext == 'css') {
			var link = document.createElement("link");
			link.setAttribute("rel", "stylesheet");
			link.setAttribute("type", "text/css");
			link.setAttribute("href", file);
		}
		if (ext == 'js') {
			var link = document.createElement("script");
			link.setAttribute("type", "text/javascript");
			link.setAttribute("src", file);
		}
		document.getElementsByTagName("head")[0].appendChild(link)
	},
	checkAllUpload: function() {
		var elements = $(".qq-upload-list li");
		$rez = true;
		$.each(elements, function(key, value) {
			if ($(value).css('display') != 'none') {
				$rez = false;
				return;
			}
		});
		if ($rez == true) {
			$(".qq-upload-list").css({'display': 'none'});
		}
	}
}


var contentUploader = {
	initializedCount: 0,
	init: function() {
		$(function() {
			$('.contentFileUploader.unInitialized').each(function() {
				var $this = $(this);
				var id = 'image-' + ++contentUploader.initializedCount;
				var $avaInput = $this.closest('form').find('input.avaIdInput');
				var dataType = $this.data('type');

				$this.attr('id', id);

				contentUploader.createUploader({
					id: $this.attr('id'),
//					formId: $this.attr('for'),
					action: $this.attr('action'),
					maxSize: $this.attr('maxSize'),
					exts: $this.attr('exts').split(','),
					multiple: $this.attr('multiple'),
					dataType: dataType
				});

				$this.removeClass('unInitialized');

				if ($avaInput.val()) {
					$.get('/files/details/' + $avaInput.val() + '/' + $this.attr('type') + '/', function(responseJSON) {
						contentUploader.set($this, responseJSON.id, responseJSON.url_preview);
					}, 'json').fail(function() {
						$avaInput.val('');
					});
				}
			});
		})
	},
	createUploader: function(params) {
		var upload = new qq.FileUploader({
			element: document.getElementById(params.id),
			action: params.action,
			sizeLimit: params.maxSize,
			allowedExtensions: params.exts,
			multiple: params.multiple,
			template: '<div class="qq-uploader single-image">' +
				'<div title="Click to upload image/photo" class="qq-upload-button"></div>' +
				'<div class="qq-upload-status"><div class="loader"><div class="loaded"></div></div></div>' +
				'<div class="qq-upload-drop-area"><span></span></div>' +
				'<ul class="qq-upload-list"></ul>' +
				'</div>',
			onSubmit: function(id, fileName) {
				//$(this.element).closest('form').attr('disabled', 'disabled');
				$(this.element).find('.qq-upload-status').show().find('.loaded').css('width', 0);
				if (params.dataType == 'mainImage') {
					upload.setParams({
						isMainImage: 1
					});
				}
			},
			onProgress: function(id, fileName, loaded, total) {
				$(this.element).find('.loaded').css('width', (100 * (loaded / total)) + '%');
				//$clickedEl.find('.loader').css('width', (100 * (loaded / total)) + '%');
			},
			onComplete: function(id, fileName, responseJSON) {
				if (typeof (responseJSON['success']) != 'undefined' && responseJSON['success']) {
					if (responseJSON['setMainUrl']) {
						$.ajax({
							type: 'POST',
							url: responseJSON['setMainUrl'] + responseJSON.id + '/',
							dataType: 'json',
							success: function(resp) {
							},
							error: function() {
							}
						});
					} else {
						$(this.element).closest('form').find('input.avaIdInput').val(responseJSON.id);
					}

					if (params.dataType == 'mainImage' && typeof $clickedEl != 'undefined') {
						$clickedEl.find('.imgwrap').html('<img src="' + responseJSON.url_preview + '"/>');
						$('#article-imageId').val(responseJSON.id);
					}

					contentUploader.set($(this.element), responseJSON.id, responseJSON.url_crop);


					if(typeof responseJSON['function_name'] !== 'undefined') {
						if(typeof responseJSON['data'] !== 'undefined') {
							var data2 = responseJSON['data'];
						} else {
							var data2 = false;
						}
						system.callFunction(responseJSON['function_name'], data2, false);
					}
				}

				$(this.element).find('.qq-upload-status').hide();
			}
		});
	},
	upload: function(target, event) {
		$(target).closest('.thumb').find('.qq-uploader input[type=file]').trigger('click');
		return false;
	},
	set: function($uploader, fileId, src) {
		var $thumb = $uploader.closest('.thumb'),
			oldSrc = $thumb.find('img').attr('src');

		$thumb.find('img[src="' + oldSrc + '"]').each(function() {
			$(this).attr('src', src);
		});

		if ($thumb.find('.settings').hasClass('active')) {
			$('body').find('img[src="' + oldSrc + '"]').each(function() {
				$(this).attr('src', src);
			});
		}

		$thumb.find('.settings').addClass('active');
		$thumb.find('.remove').attr('href', '/files/cubeRemove/' + fileId + '/');
		$thumb.find('.edit-image').attr('href', '/template/imageInfo/' + fileId + '/');

	},
	remove: function(target, event) {
		var $this = $(target);
		var $avaInput = $this.closest('form').find('input.avaIdInput');

		$.ajax({
			type: 'POST',
			url: $this.attr('href'),
			dataType: 'json',
			async: false,
			success: function(resp) {
				$this.closest('.thumb').find('img').attr('src', resp.image_crop);
				$this.closest('.thumb').find('.settings').removeClass('active');
			}
		});

		if ($avaInput.length) {
			$avaInput.val('');
		}

		return false;
	},
	removeFromList: function($target) {
		var $parent = $target.closest('li');

		//todo: if XHR was not established?
		$parent.addClass('none');

		//log($this);
		var xhr = $.ajax({
			type: 'POST',
			url: $target.attr('href'),
			dataType: 'json'
		})
			.done(function(resp) {
				$parent.remove();
			})
			.fail(function(msg) {
				$parent.removeClass('none');
			}).always(function() {
			});

		return false;
	},
	addMainImg: function(el) {
		$clickedEl = $(el);
		$('body').find('.mainImageUploader').find('input').click();
	}
}

var contentUploaderList = {
	initializedCount: 0,
	init: function() {
		$('.contentUploaderList.unInitialized').each(function() {

			var $this = $(this);
			var id = 'fileuploader-' + ++contentUploaderList.initializedCount;

			$this.attr('id', id);

			contentUploaderList.createUploader({
				id: $this.attr('id'),
				action: $this.attr('action'),
				maxSize: $this.attr('maxSize'),
				exts: $this.attr('exts').split(',')
			});

			$this.removeClass('unInitialized');
		});
	},
	createUploader: function(params) {
		var $list = $('.list-images');
		var l = $list;

		var uploader = new qq.FileUploader({
			element: document.getElementById(params.id),
			action: params.action,
			sizeLimit: params.maxSize,
			allowedExtensions: params.exts,
			template: '<div class="qq-uploader">' +
				'<div class="qq-upload-button-outer"><div title="' + l.get('upload_files') + '" class="qq-upload-button">' + l.get('upload_files') + '</div><i class="cube-gallery-plus"></i></div>' +
				'<div class="qq-upload-drop-area"><span>' + l.get('upload_files_hint') + '</span></div>' +
				'<ul class="qq-upload-list"></ul>' +
				'</div>',
			fileTemplate: '<li>' +
				'<span class="qq-upload-file"></span>' +
				'<span class="qq-upload-spinner"></span>' +
				'<span class="qq-upload-size"></span>' +
				'<a class="qq-upload-cancel" href="#">' + l.get('cancel') + '</a>' +
				'<span class="qq-upload-failed-text">' + l.get('failed') + '</span>' +
				'</li>',
			onSubmit: function(id, fileName) {

				if(typeof window.galleryId !== 'undefined') {
					//send gallery id to server
					uploader.setParams({
						gallery_id: window.galleryId
					});
				}

				$template = '<li class="image-wrap active" data-id="' + id + '">' +
					'<div class="loader"></div>' +
					'</li>';

				window.addImgLink.closest('li').after($template);
				if(window.addImgLink.closest('.upload-images').size() > 0) {
					window.addImgLink.closest('.upload-images').removeClass('hidden');
				}

				//remove gallery placeholder
				editorGallery.toggleGalleryPlaceholder(window.addImgLink.closest('ul'));
			},
			onProgress: function(id, fileName, loaded, total) {
				var $loader = window.addImgLink.closest('ul').find('[data-id="' + id + '"]').find('.loader');
//				$loader.css('width', (100 * (loaded / total)) + '%');
			},
			onComplete: function(id, fileName, responseJSON) {
				if (typeof (responseJSON['success']) != 'undefined' && responseJSON['success']) {
					if (responseJSON['html']) {
						var $item = window.addImgLink.closest('ul').find('[data-id="' + id + '"]');
						$item.find('.loader').hide();
						if(typeof responseJSON['html'] === 'object') {
							$.each(responseJSON['html'], function(key, value){
								var $clone = $item.clone();
								$clone.html(value['html']);
								$newItem = $clone.insertAfter($item);
								$newItem.attr('id', 'images_' + value['id']);
							});
							$item.remove();
						} else {
							$item.html(responseJSON['html']);
							$item.attr('id', 'images_' + responseJSON['id']);
						}

						editorGallery.setDraggable($item);
					}
				}

				if(typeof responseJSON['function_name'] !== 'undefined') {
					if(typeof responseJSON['data'] !== 'undefined') {
						var data2 = responseJSON['data'];
					} else {
						var data2 = false;
					}
					system.callFunction(responseJSON['function_name'], data2, false);
				}

			}
		});
	}
}

var editorGallery = {
	addImg: function($el) {
		var $gallery = $el.closest('ul');
		window.addImgLink = $el;
		window.galleryId = $gallery.data('id');
		$('#contentUploaderList').click();
	},
	removeImage: function($el) { // remove image from list and server
		$el.closest('li').animate({'width': 0}, 200, function() {
			$gallery = $el.closest('ul');
			contentUploader.removeFromList($el);
			$(this).remove();
			editorGallery.toggleGalleryPlaceholder($gallery);
		});
	},
	setDraggable: function($el) {
		var $gallery = ($el.hasClass('gallery')) ? $el : $el.closest('.gallery');
//			console.log($gallery);

		//get itemId
		var itemId = $('form').data('id');
		var gallery_id = $gallery.data('id');

		if(typeof $.sortable === 'function'){
			$gallery.sortable({
				cursor: 'pointer',
				items: $gallery.find('li:not(.ui-state-disabled)'),
				update: function(event, ui)
				{
					var serial = '';
					$gallery.find('li:not(.ui-state-disabled)').each(function(){
						$data = $(this).attr('id').split('_');
						serial +=  '&' + $data[0] + '[]=' + $data[1];
					});
					serial = serial.substr(1);

					var xhr = $.ajax({
						type: 'POST',
						url: '/uploader/files/sortImages/' + gallery_id + '/',
						dataType: 'json',
						data: serial
					});
				}
			});
		}
	},
	initGalleryEvents: function($gallery, widget) {
		$gallery.on('click', 'a.addImage', function(e) {
			editorGallery.addImg($(this))
			e.preventDefault();
		});

		$gallery.on('click', 'a.edit', function(e) {
			popup($(this), $(this).attr('data-cke-saved-href'));
			e.preventDefault();
		});

		$gallery.on('click', 'a.remove', function(e) {
			var $this = $(this);
			$.eva.confirm(function() {
				editorGallery.removeImage($this);
				e.preventDefault();
			}, $this.attr('eva-confirm'));

			return false;
		});

		if(typeof widget !== 'undefined') {
			$gallery.on('click', '.removeGallery', function(e) {
				if (confirm('Ви справді хочете видалити галерею?')) {
					$gallery.slideUp(function() {
						widget.fire('destroyGalley');
					});
				}
			});
		}
	},
	toggleGalleryPlaceholder: function($gallery) {
		if ($gallery.find('li').filter(':not(.ui-state-disabled)').length == 0) {
			$gallery.closest('.add-photo-gallery-wrap').addClass('showPlaceholder');
		} else {
			$gallery.closest('.add-photo-gallery-wrap').removeClass('showPlaceholder');
		}
	}
}

