function log(data)
{
	console.log(data);
}

String.prototype.replaceAll = function(search, replace){
	return this.split(search).join(replace);
}

var box = {
	$element: false,
	$content: false,
	request: false,
	template: '<div class="pbox-overlay"><div class="pbox-outer"><div class="box-closer" onclick="box.close();">&times;</div><div class="pbox"><div class="pbox-inner"></div></div></div></div>',
	templateLoader: '<div class="box-loader"></div>',
	isOpened: false,
	closeOnOverlay: true,
	closeTimer: false,
	removeTimer: false,
	width: 300,
	height: 'auto',
	skin: false,
	closeCallback: false,
	imgWidth: false,
	imgHeight: false,

	load: function(href, size, closeOnOverlay, callback) {
		if(this.$element) {
			this.loader(this.$content);
		} else {
			this.open(this.templateLoader, size, true);
		}

		this.request = $.ajax({
			type: 'get',
			data: { fromBox: 1 },
			url: href,
			success: function(result) {
				box.request = false;

				var html = (typeof result === 'object') ? result.content : result;
				box.open(html, size, false, closeOnOverlay, callback);
				if(typeof web !== 'undefined' && typeof web.afterUpdatePage === 'function') {
					web.afterUpdatePage();
				}

				if(typeof result.function_name !== 'undefined' && typeof system.callFunction === 'function') {
					if(typeof result.data === 'undefined') {
						var data = {};
					} else {
						var data = result.data;
					}
					system.callFunction(result.function_name, data);
				}
			},
			error: function() {
				console.log(['Error of delete!']);
			}
		});

		return false;
	},

	message: function(title, message){
		if($('.Message').size() !== 0){
			template = $('.Message').html();
			template = template.replace('%title', title);
			template = template.replace('%content', '<div class="pbox-message">' +
				'<div class="message-text">' + message + '</div>' +
				'<a class="btn-roundblue" href="#" onclick="box.close();">OK</a>' +
			'</div>');
//			template = template.replace('%content', '<div class="pbox-message">' + message + '</div>');
			this.content(template, 'message');
		} else {
			console.log('No message template in body!');
		}
	},

	previewImage: function(target){
		var $target = $(target);
		var img = $target.data('img');

		if($('.Message').size() !== 0){
			template = $('.Message').html();
			template = template.replace('%title', 'IMAGE PREVIEW');
			template = template.replace('%content', '<img class="pbox-image" src="' + img + '"/>');
			if(typeof $target.data('imgwidth') !== 'undefined' && typeof $target.data('imgheight') !== 'undefined') {
				this.imgWidth = $target.data('imgwidth');
				this.imgHeight = $target.data('imgheight');
				this.content(template, 'fromData');
			} else {
				this.content(template, 'w912');
			}
		}
	},

	confirm: function(target, isAjax){
		var $target = $(target);
		if(typeof $target.data('confirm') === 'undefined') {
			var confirmText = 'Are you sure you want to do this?'
		} else {
			var confirmText = $target.data('confirm');
		}
		var url = $target.attr('href');

		if(isAjax === true) {
			var href = 'box.close(); return web.ajaxGet(this); ';
		} else {
			var href = '';
		}

		if($('.Message').size() !== 0){
			template = $('.Message').html();
			template = template.replace('%title', '');
			template = template.replace('%content', '<div class="pbox-confirm">' +
					 '<div class="confirm-text">' + confirmText + '</div>' +
					 '<a class="btn-roundblue" href="' + url + '" onclick="' + href + '">Yes </a>' +
					 '<a class="btn-roundbrown" href="#" onclick="box.close();">No</a>' +
				 '</div>');
			this.content(template, 'message');
		} else {
			console.log('No message template in body!');
		}
		return false;
	},

	loadImage : function(target) {
		$.fancybox({
			type: 'image',
			href: $(target).attr('href')
		});

		return false;
	},

	submit : function(form, callback, autoLoadBox) {
		if(autoLoadBox === true) {
			if(this.$element) {
				this.loader(this.$content);
			} else {
				this.open(this.templateLoader, 'edit', true, false);
			}
		}

		var $form = $(form);
		var $box = $form.closest('.pbox');
		var values = $form.serializeArray();

		box.loader(true, $box);

		this.request = $.ajax({
			type: 'POST',
			dataType: 'json',
			data: values,
			url: $form.attr('action'),
			success : function(resp) {
  				if(resp.status) {
					if (typeof resp.redirect_url !== 'undefined') {
						window.location.replace(resp.redirect_url);
					}
					if(typeof callback === 'function') {
						// if (resp && resp.data && resp.data.content) {
							// box.content(resp.data.content, 'edit');
							// callback(resp.content);
						// } else {
							// console.log(resp.content)
							callback(resp.content);
						// }
					} else if(resp.content) {
						box.content(resp.content, 'edit');
					} else {
						box.close();
						box.close();
						box.close();
					}

					if(resp.message) {
						box.content(resp.message, 'message');
//						eva.say([resp.message]);
					}
				} else {
					if(typeof resp.popupsize !== undefined) {
						var popupsize = resp.popupsize;
					} else {
						var popupsize = 'edit';
					}
					box.content(resp.content, popupsize);
					if(resp.message) {
						box.content(resp.message, 'message');
//						eva.message([resp.message]);
					}
				}

				if(typeof web !== 'undefined' && typeof web.afterUpdatePage === 'function') {
					web.afterUpdatePage();
				}

				if(typeof resp.function_name !== 'undefined' && typeof system.callFunction === 'function') {
					if(typeof resp.data === 'undefined') {
						var data = {};
					} else {
						var data = resp.data;
					}
					system.callFunction(resp.function_name, data);
				}

				box.loader(false, $box);

			},
			error : function() {
				window.location.reload();
				console.log("Error of add!");
			}
		});

		return false;
	},

	content: function(content, size) {
		if(content instanceof jQuery) {
			content = content.clone();
		}
		this.open(content, size);

		return false;
	},

	/**
	 * Open box
	 *
	 * @param {object} options Can have {href, content}
	 * @param {small|full} size Size of block
	 * @returns Box instance
	 */
	open : function(content, size, minHeight, closeOnOverlay, callback, skin) {

		this.defineSize(size, minHeight);
		this.skin = skin;
		this.closeOnOverlay = typeof closeOnOverlay !== 'undefined' ? closeOnOverlay : true;


		if(!this.$element) {
			$('body').children('.main-container, .body-content-inner').addClass('filter-blur-3');
			$('body').append(this.template);

			this.$element = $('body').children('.pbox-overlay').find('.pbox-outer');
			this.$content = this.$element.find('.pbox-inner');

			this.$element.closest('.pbox-overlay').bind('click', function(e) {
				if(box.isOpened && $(e.target).hasClass('pbox-overlay')) {
					if(box.closeOnOverlay) {
						box.close();
					} else {
						box.$element.children('.pbox').addClass('anim');
						if(!box.closeTimer) {
							box.closeTimer = setTimeout(function() {
								box.closeTimer = false;
								box.$element.children('.pbox').removeClass('anim');
							}, 500);
						}
					}
				}
			});

			$(window).bind('resize.box', function() {
				box.position();
			});

		}

		this.closeCallback = false;
		this.$content.empty().html(content);

		box.position();

		if(typeof callback === 'function') {
			callback(this.$content);
		}

		return this;
	},
	position: function() {
		var bh = $('body').height();

		this.$element.css({
			width: box.width,
			height: box.height
		});

		if(this.skin) {
			this.$element.attr('data-skin', this.skin);
		} else {
			this.$element.attr('data-skin', 'default');
		}

		if(this.$element.outerHeight() < bh) {
			this.$element.css('margin-top', (bh - this.$element.outerHeight())/2);
		} else {
			this.$element.css('margin-top', 0);
		}

		this.$element.offset();
		this.$element.addClass('anim');

		this.show();
	},
	resize: function() {
		this.position();
	},
	show: function() {
		clearTimeout(box.removeTimer);
		box.removeTimer = false;

		this.isOpened = true;
		this.$element.closest('.pbox-overlay').addClass('opened');
		//$('body').css('overflow', 'hidden');
	},
	back : function() {
		alert('Use back()! remove it!');
	},
	close : function() {
		if(this.isOpened) {
			if(this.request !== false) {
				this.request.abort();
				this.request = false;
			}
			this.$element.closest('.pbox-overlay').removeClass('opened');
			//$('body').css('overflow', 'auto');
			if(!box.removeTimer) {
				box.removeTimer = setTimeout(function() {
					box.removeTimer = false;
					box.$element.closest('.pbox-overlay').remove();
					box.$element = false;
				}, 400);
			}

			$(window).unbind('resize.box');
			$('body').children('.main-container, .body-content-inner').removeClass('filter-blur-3');
			this.isOpened = false;

			if(typeof(this.closeCallback) == 'function') {
				this.closeCallback();
			}

			this.closeCallback = false;
		}
		return false;
	},
	loader : function(status, $block) {
		$block = typeof $block !== 'undefined' ? $block : this.$content;

		if(typeof(status) == 'undefined' || status) {
			if(!$block.children('.box-loader-helper').length) {
				$block.append('<div class="box-loader-helper"></div>');
			}
			$block.children('.box-loader-helper').stop(true).fadeTo(200, 1);
		} else {
			$block.children('.box-loader-helper').stop(true).fadeTo(100, 0, function() {
				$(this).remove();
			});
		}
	},
	defineSize: function (size, minHeight) {
		this.width = 500;
		this.height = 'auto';

		switch (size) {
			case 'message':
				this.width = 500;
				break;
			case 'edit':
				this.width = 850;
				break;
			case 'likesPeople':
				this.width = 560;
				break;
			case 'sharesPeople':
				this.width = 560;
				break;
			case 'medium':
				this.width = 550;
				break;
			case 'small':
				this.width = 500;
				this.height = 300;
				break;
			case 'large2':
				this.width = 804;
				this.height = 550;
				break;
			case 'fo':
				this.width = 850;
				this.height = 550;
				break;
			case 'large':
				this.width = 700;
				this.height = 500;
				break;
			case 'huge':
				this.width = 800;
				break;
			case '640':
				this.width = 640;
				break;
			case 'video':
				this.width = 650;
				this.height = 370;
				break;
			case 'videoHelp':
				this.width = 640;
				this.height = 418;
				break;
			case 'warning':
				this.width = 395;
				this.height = 125;
				break;
			case 'cubecontent' :
				this.width = 910;
				break;
			case 'w870' :
				this.width = 870;
				break;
			case 'w912' :
				this.width = 912;
				break;
			case 'fromData':
				this.width = this.imgWidth + 12;
				this.height = this.imgHeight + 12;
				break;
			default:
				break;
		}

		if (minHeight && this.height == 'auto') {
			this.height = this.width * 3 / 4;
		}
	}
};



var system = {
	ajaxGet:function(target, callback, data, retunObj, method){
		if(typeof method === 'undefined') {
			method = 'GET';
		}

		if(typeof data === 'undefined') {
			data = {};
		}
		if(typeof target !== 'string') {
			var $target = $(target);
			var url = $target.attr('href');
		} else {
			var url = target;
		}



		var $ajax = $.ajax({
			type: method,
			dataType: 'json',
			url: url,
			data: data,
			success : function(resp) {
				if(resp.status) {
					if(typeof callback === 'function') {
						callback(resp);
					}

					if(resp.message) {
						box.content(resp.message, 'message');
					}
				} else {
					if(resp.message) {
						box.content(resp.message, 'message');
					}
				}
				if(typeof resp.function_name !== 'undefined' && typeof system.callFunction === 'function') {
					if(typeof resp.data === 'undefined') {
						var data = {};
					} else {
						var data = resp.data;
					}
					system.callFunction(resp.function_name, data, $target);
				}
			}
		});

		if(retunObj) {
			return $ajax;
		}
		return false;
	},
	autoScroll: function(element) {
		$(element).each(function(){
			var $this = $(this);

			if($('.body-content-inner').size() > 0) {
				// for admin panel
				var $element = $('.body-content-inner');
			} else {
				var $element = $('.content-outer');
			}
			if(!$this.hasClass('isPaginatorAutoScroll')) {
				$this.addClass('isPaginatorAutoScroll');

				var func = function() {
					var height = $element.find('.content-inner').outerHeight(true) + 600;
					var scrollBottom = height - $element.height() - $element.scrollTop();

					if(scrollBottom <= 400) {
						$element.unbind('scroll', func);
						$this.click();
					}
				};

				$element.bind('scroll', func);
			}
		});
	},
	callFunction:function (name, data, target)
	{
		switch (name) {
			case 'removeClass':
				var $targets = $(data.target);
				$targets.removeClass(data.class);
				break;
			case 'addClass':
				var $targets = $(data.target);
				$targets.addClass(data.class);
				break;
			case 'initGallery':
				web.initUserGallery(data.gallery);
				break;
			case 'updateAddFileLink':
				web.updateAddFileLink(data.url, data.name);
				break;
			case 'updateClear':
				web.updateClear($('#addupdate'));
				break;
			case 'removeItem':
				var $targets = $(data.target);
				$.each($targets, function(key, value){
					$(value).remove();
				});
				break;
			case 'removeParentItem':
				$(data.target).closest(data.parent).remove();
				break;
			case 'setCount':
				var $target = $(data.target);
				web.setCountTo($target, data.content);
				break;
				break;
			case 'negativeCount':
				var $target = $(data.target);
				if(typeof data.num === 'undefined') {
					web.negativeCountTo($target);
				} else {
					for(i = 1; i <= data.num; i++) {
						web.negativeCountTo($target);
					}
				}
				break;
			case 'addCount':
				var $target = $(data.target);
				if(typeof data.num === 'undefined') {
					web.addCountTo($target);
				} else {
					for(i = 1; i <= data.num; i++) {
						web.addCountTo($target);
					}
				}
				break;
			case 'negativeComments':
				var $target = $(data.target);
				web.negativeCountTo($target);
				break;
			case 'afterUpdatePage':
				web.afterUpdatePage();
				break;
			case 'clearFields':
				var $targets = $(data.target);
				$.each($targets, function(key, value){
					$(value).val('').change();
					$(value).closest('.autoform-element').find('.autoform-element-errors').remove();
				});
				break;
			case 'clearUploaderList':
				var $target = $(data.target);
				$target.find('li:not(.hidden)').remove();
				break;
			case 'redirect':
				window.location.href = data.url;
				break;
			case 'editBlock':
				if(typeof data.content !== 'undefined' && typeof data.target !== 'undefined') {
					var $target = $(data.target);
					$target.addClass('hidden');
					$(data.content).insertAfter($target);
					web.afterUpdatePage();
				}
				break;
			case 'changeContent':
				if(typeof data.content !== 'undefined' && typeof data.target !== 'undefined') {
					var $target = $(data.target);
					$(data.content).insertBefore($target);
					$target.remove();
					if(typeof web !== 'indefined') {
						web.blockProfileEdit(false);
						web.afterUpdatePage();
					}
				}
				break;
			case 'changeInnerContent':
				if(typeof data.content !== 'undefined' && typeof data.target !== 'undefined') {
					var $target = $(data.target);
					$target.html(data.content);
					web.blockProfileEdit(false);
					web.afterUpdatePage();
				}
				break;
			case 'submitError':
				if(typeof data.content !== 'undefined' && typeof data.target !== 'undefined') {
					var $target = $(data.target);
					$(data.content).insertBefore($target);
					$target.remove();
					web.afterUpdatePage();
				}
				break;
			case 'addBlock':
				if(typeof data.content !== 'undefined' && typeof data.target !== 'undefined') {
					var $target = $(data.target);
					$(data.content).insertAfter($target);
					web.afterUpdatePage();
				}
				break;
			case 'popupShow':
				if(typeof data.content !== 'undefined') {
					box.message(data.title, data.content);
				}
				break;
			case 'cancelEditBlock':
				web.cancelEditBlock();
				break;
			default:
				break;
		}
		//web.iniAutoResizeWindow();

		if(typeof data.function_name !== 'undefined') {
			if(typeof data.data !== 'undefined') {
				var data2 = data.data;
			} else {
				var data2 = false;
			}
			system.callFunction(data.function_name, data2, false);
		}
		$(window).resize();
	}
}
