var pages = {
	pageSelectAllCountries: function(target)
	{
		var $target = $(target);

			var $countries = $('input:checkbox');
			$countries.each(function(key, value){
				var $value = $(value);
				if(typeof $value.data('country') !== 'undefined' && $value.data('country') !== 'all'){
						if($target.is(':checked')) {
							$value.attr('checked', 'checked');
						} else {
							$value.removeAttr('checked');
						}
				}
			});
	}
}


function popup($this, url) {
	if(typeof url === 'undefined') {
		url = $this.attr('href');
	}
	$('div:first-child').blur();
	$.fancybox({
//			type: 'iframe',
		type: 'ajax',
		href: url + '?modal=1',
		padding: 8,
		width: 800,
		height: 500,
		autoSize: false,
//		afterShow : function() {
//			var $content = $('.fancybox-inner');
//		}
	});

	return false;
}


function pagesubmit(target) {
	var url = $(target).attr('action');
	var data = $(target).serialize();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: url,
		data: data,
		success: function(data) {
			if(typeof data.status !== 'undefined' && data.status === true) {
				$.fancybox.close();
			} else {
				$('.fancybox-inner').html = data.content;
			}
		}
	});

	return false;
}


$(document).ready(function(){
	$('.ckeditor').each(function(key, value){
		$selector = $(value);
		if(!$selector.hasClass('isCKEditor')) {

			$selector.addClass('isCKEditor');
			var isSimple = isSimple || false;
			var editorConfig = {
				title: false,
				removePlugins: isSimple ? 'image' : '',
				toolbar: [['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo', 'Save']], //isInlineWithEditor ? 'SimpleText' : null,
				language: 'en',
				extraPlugins: 'widget',
				extraPlugins: 'save',
				floatSpaceDockedOffsetY: 10
			}

			if (!isSimple) {
				var extraPlugins = 'gallery,save,oembed,youtube';
				var gallery_button = ['Gallery'];
				if (typeof $selector.attr('data-disableGallery') != 'undefined') {
					extraPlugins = 'save,oembed';
					gallery_button = [];
				}

				editorConfig = {
					title: false,
					language: 'en',
					div_wrapTable: true,
					extraPlugins: 'widget',
					extraPlugins: extraPlugins,
					removePlugins: 'magicline',
					contentsCss: CKEDITOR.basePath + 'plugins/gallery/css/style.css',
					toolbar: [
						{name: 'document', groups: ['mode', 'document', 'doctools'], items: ['Source', '-', 'NewPage', 'Preview', 'Print', '-', 'Templates']},
						{name: 'clipboard', groups: ['clipboard', 'undo'], items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo']},
						{name: 'editing', groups: ['find', 'selection'], items: ['Find', 'Replace', '-', 'SelectAll', '-', 'Scayt']},
						'/',
						{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
						{name: 'paragraph', groups: ['list', 'blocks', 'align', 'bidi'], items: ['NumberedList', 'BulletedList', '-','Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']},
						{name: 'links', items: ['Link', 'Unlink', 'Anchor']},
						{name: 'insert', items: ['Youtube']},
						'/',
						{ name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
						'/',
						{name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize']},
						{name: 'colors', items: ['TextColor', 'BGColor']},
						{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
						{name: 'others', items: ['-']},
						{name: 'gallery', items: gallery_button}
					],
					toolbarGroups: [
						{name: 'clipboard', groups: ['clipboard', 'undo']},
						{name: 'editing', groups: ['find', 'selection']},
						{name: 'links'},
						{name: 'document', groups: ['mode', 'document', 'doctools']},
						{name: 'others'},
						'/',
						{ name: 'insert' },
						'/',
						{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
						{name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align']},
						{ name: 'tools' },
						{name: 'styles'},
						{name: 'colors'},
					]
				}
			}

			editorConfig.on = {
				blur: function(event) {
					var isModified = event.editor.checkDirty(),
						element = event.editor.element.$,
						$element = $(element);
					//check if content was changed
//						if (isModified !== false) {
//							var data = event.editor.getData();

//						console.log(data);
//							data.replace(/["']/g, "");
//						console.log(data);

//							cabinet.CKsave($element, data);
//							event.editor.resetDirty();
//						} else {
//							isEditing--;
//						}

//						cabinet.__parseEmptyObject($element, data);
				},
				key: function(event) {
					if (isSimple && event.data.keyCode == 13) {
						event.cancel();
					}
				},
				focus: function(event) {
//						$(event.editor.element.$).removeClass(cfg.emptyElemClass);
				}
			}

			//if editor in list
//				if ($selector.closest('.homepage-item-text').length) {
//					editorConfig.on.paste = function(event) {
//						var el = event.editor.element.$;
//						event.data.dataValue = cabinet.check_maximum_chars(el, event, true, event.data.dataValue);
//					}
//				}
			//}

			$selector.ckeditor(editorConfig);
		}
	})
});



