crop = {
	jcrop_api: false,
	previewHeight: false,
	previewWidth: false,
	cropAreaHeight: false,
	cropAreaWidth: false,
//	sizesToCrop: ['tiny', 'preview','preview_ad','profile','avatar','avatar_small'], // OLD
	sizesToCrop: ['tiny', 'userava_44', 'userava_52', 'userava_94', 'userava_100', 'userava_174', 'cover_580', 'cover_500'],
	mode: 'default',
	url: false,
	resp: false,
	$img: false,
	$block: false,
	$preview: false,
	template: ' <div class="pbox-title">' +
					'<div class="pbox-title-name">Crop image</div>' +
						'<div class="pbox-close btn-close" onclick="box.close();"></div>' +
					'</div>' +
					'<div class="pbox-content">' +
						'<div id="crop_area" style="width: 550px !important; margin: 0 auto; border-top: none;">'+
							'<div class="cropPreview" style="display:none" >'+
								'<img src="" id="cropPreviewImg" class="cropPreviewImg"/>'+
							'</div>'+
							'<div class="forCropBlock" style="margin: 0 auto; ">'+
								'<img src="" class="forCrop" />'+
							'</div>'+
							'<a style="display: block; position: relative; margin: 10px auto; bottom: auto; right: auto;" class="btn-blue btn btn-ok save-crop-image" onclick="return crop.save(this);" title="Save image" href="">save</a>'+
							//'<a class="btn btn-upload download-image"  title="Download image" href="">download</a>'+
						'</div>' +
					'</div>' +
				'</div>',
// OLD
//	template:   '<div id="crop_area" style="width: 550px !important; margin: 0 auto;">'+
//					'<div class="cropPreview" style="display:none" >'+
//						'<img src="" id="cropPreviewImg" class="cropPreviewImg"/>'+
//					'</div>'+
//					'<div class="forCropBlock" style="margin: 0 auto; margin-top: 30px; ">'+
//						'<img src="" class="forCrop" />'+
//					'</div>'+
//					'<a style="right: -20px !important" class="btn btn-ok save-crop-image" onclick="return crop.save(this);" title="Save image" href="">save</a>'+
//					//'<a class="btn btn-upload download-image"  title="Download image" href="">download</a>'+
//				'</div>',

	open: function (target, event, mode) {

		if(typeof mode != 'undefined') {
		 	crop.mode = mode;
		}

		//crop.$img = $(target).parent().parent().find($(target).data('class'));
		crop.$img = $(target).closest('.user-banner, .userAvatar, .Avatar').find('img');

		crop.url = $(target).attr('href');

		crop.loadContent(crop.url, false, function(resp) {
			crop.resp = resp;
			crop.load();
		});

		return false;
	},

	load: function() {

		//box.loader(true);

		box.open(crop.template, '640');

		crop.$block = $('body').find('.pbox #crop_area');

		crop.$block.find('.download-image').attr('href', crop.resp.download).show();

		crop.$block.find('.forCrop').attr('src', crop.resp.url).bind('load', function() {
			crop.$block.find('.cropPreviewImg').attr('src', crop.resp.url).load(function() {
				// disable loader
				crop.init();
			});
		});

	},
	init: function() {

		switch (crop.mode) {
			case 'default':
				aspectRatio = 1;
				break;
			case 'banner':
				aspectRatio = 580/245;
				break;
			case 'banner_ad' :
				aspectRatio = 709/205;
				break;
			default:
				aspectRatio = 1;
				break;
		}

		crop.$block.find('.forCrop').attr('id', 'forCrop');

		crop.jcrop_api = $.Jcrop('#forCrop',{
			onChange: crop.showPreview,
			onSelect: crop.showPreview,
			//aspectRatio: 796/268
			aspectRatio: aspectRatio
		});

		crop.$preview = crop.$block.find('#cropPreviewImg');

		crop.previewHeight = crop.$block.find('.cropPreview').height();
		crop.previewWidth = crop.$block.find('.cropPreview').width();

		crop.cropAreaHeight = crop.$block.find('.jcrop-holder').height();
		crop.cropAreaWidth = crop.$block.find('.jcrop-holder').width();

		crop.$preview.css('height', crop.cropAreaHeight);
		crop.$preview.css('width', crop.cropAreaWidth);

		var resp = crop.resp;

		if(resp.cropArea != false)
		{
			crop.jcrop_api.setSelect([resp.cropArea.x, resp.cropArea.y, resp.cropArea.x2,  resp.cropArea.y2]);
			var coords = {
				x : resp.cropArea.x,
				y : resp.cropArea.y,
				w : Math.abs(resp.cropArea.x - resp.cropArea.x2),
				h : Math.abs(resp.cropArea.y - resp.cropArea.y2)
			};

			crop.showPreview(coords);
		}
	},
	save: function(target) {

		var urlRequest = crop.url + '1/';
		var x = crop.jcrop_api.tellSelect().x;
		var w = crop.jcrop_api.tellSelect().w;
		var h = crop.jcrop_api.tellSelect().h;
		var y = crop.jcrop_api.tellSelect().y;

		if(crop.jcrop_api.tellSelect().h == 0 || crop.jcrop_api.tellSelect().w == 0) {
			alert('Please select crop are');
			return false;
		}

		// console.log(x);
		// console.log(y);
		// console.log('--');
		// console.log(w);
		// console.log(h);

		$.ajax({
			type: 'POST',
			url: urlRequest,
			dataType: 'json',
			data: {
				sizes          : crop.sizesToCrop,
				cropAreaWidth  : crop.cropAreaWidth,
				x : x,
				y : y,
				w : w,
				h : h
			},
			success : function(response) {
				var src = false, oldSrc = false;

//				console.log(crop.$img);
				log(crop.$img);
				oldSrc = src = crop.$img.attr('src');
				var pos = src.indexOf('?');
				if (pos >= 0) {
					src = src.substr(0, pos);
				}
				var date = new Date();
				$('body').find('img[src="'+oldSrc+'"]').each(function() {
					$(this).attr('src', src+'?v='+date.getTime());
				});
				box.close();

			}
		});

		return false;
	},
	showPreview : function(coords) {
		var rx = crop.previewWidth / coords.w;
		var ry = crop.previewHeight / coords.h;

		crop.$preview.css({
			width: Math.round(rx * crop.cropAreaWidth) + 'px',
			height: Math.round(ry * crop.cropAreaHeight) + 'px',
			marginLeft: '-' + Math.round(rx * coords.x) + 'px',
			marginTop: '-' + Math.round(ry * coords.y) + 'px'
		});
	},
	loadContent: function(url, options, callback) {

		// loader

		$.ajax({
			type: 'get',
			dataType: 'json',
			data: {fromBox: 1},
			url: url
		}).success(function(result) {

			if(typeof callback != 'undefined') {
				callback(result);
			}
		}).error(function() {
			// error
		});

		return false;
	},
}

