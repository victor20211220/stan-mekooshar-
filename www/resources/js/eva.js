// $(function() {
// 	$.eva.__init();
// });
//
// (function($) {
// 	var b = {};
// 	b.content = {};
// 	b.eva = {};
// 	b.message = {};
// 	b.tooltip = {}
//
// 	var s = {};
// 	s.template =	'<div class="eva-container">'+
// 				'<div class="avatar" eva-content="Drag me!"></div>'+
// 				'<div class="message">'+
// 					'<div class="message-content">'+
// 						'<div class="title"></div>'+
// 						'<div class="text"></div>'+
// 						'<div class="cont"></div>'+
// 					'</div>'+
// 					'<div class="tooltip-content">'+
// 						'<div class="title"></div>'+
// 						'<div class="text"></div>'+
// 						'<div class="cont"></div>'+
// 					'</div>'+
// 				'</div>'+
// 			'</div>';
//
// 	s.confirmTemplate = '<div class="confirm-block"><a class="ok btn btn-ok" href="">OK</a><a class="cancel btn btn-close" href="">Cancel</a></div>';
// 	s.loaderTemplate = '<div class="crop-loader"><span></span></div>';
// 	s.timerShow = false;
// 	s.timerHide = false;
// 	s.showStatus = false;
// 	s.setConfirmLeave = false;
// 	s.showTooltip = true;
// 	s.writePosition = true;
// 	s.message = {};
// 	s.tooltip = {};
// 	s.mobile = false;
// 	s.ie = false;
//
// 	var eva = {
// 		__init : function() {
// 			eva.defineOptions();
// 			if($('body').hasClass('mobile')) {
// 				s.mobile = true;
// 			}
//
// 			b.content = $('.main-container');
//
// 			if(!b.content.find('.eva-container').length) {
// 				b.content.append(s.template);
// 			}
//
// 			b.eva = $('.eva-container');
// 			b.messagesBlock = $('.eva-container').children('.message');
// 			b.message = b.messagesBlock.find('.message-content');
// 			b.tooltip = b.messagesBlock.find('.tooltip-content');
//
// 			// init eva tooltips
// 			$('body').on('mouseover', '[eva-content]', function() {
// 				var $this = $(this);
// 				var title = $this.attr('eva-title');
// 				var message = $this.attr('eva-content');
//
// 				if(s.showTooltip) {
// 					if(b.message.find($this).length > 0) {
// 						if(s.timerHide) {
// 							eva.clearTimers();
// 						}
// 						if(s.showStatus) {
// 							eva.message(message, title);
// 						} else {
// 							s.timerShow = window.setTimeout(function() {
// 								eva.clearTimers();
// 								s.showStatus = true;
//
// 								eva.message(message, title);
// 							}, 300);
// 						}
// 					} else {
// 						if(b.tooltip.find($this).length == 0 && s.tooltip.content) {
// 							return false;
// 						}
// 						if(s.timerHide) {
// 							eva.clearTimers();
// 						}
//
// 						if(s.showStatus) {
// 							eva.say(message, title);
// 						} else {
// 							s.timerShow = window.setTimeout(function() {
// 								eva.clearTimers();
// 								s.showStatus = true;
//
// 								eva.say(message, title);
// 							}, 300);
// 						}
// 					}
// 				}
// 			});
// 			$('body').on('mouseout', '[eva-content]', function() {
// 				var inTooltip = b.tooltip.find($(this)).length > 0;
//
// 				if(s.showStatus && s.showTooltip) {
// 					s.timerHide = window.setTimeout(function() {
// 						s.showStatus = false;
//
// 						eva.clearTimers();
//
// 						if(inTooltip || s.tooltip.content) {
// 							eva.say();
// 						} else {
// 							eva.message();
// 						}
// 					}, 500);
// 				} else {
// 					eva.clearTimers();
// 				}
// 			});
//
// 			// simple confirm
// 			$('[eva-confirm]').click(function() {
// 				var $this = $(this);
// 				$.eva.confirm(function() {
// 					window.location.href = $this.get(0).hasAttribute('eva-remove') ? $this.attr('eva-remove') : $this.attr('href');
// 				}, $this.attr('eva-confirm'));
//
// 				return false;
// 			});
//
// 			// confirm leave page
// 			if($('[confirm-leave]').length > 0 && !s.setConfirmLeave) {
// 				eva.confirmLeave();
// 			}
//
// 			var bodyWidth = 0;
// 			var evaW = 0;
//
// 			if(!s.mobile) {
// 				// drag
// 				$('.eva-container').draggable({
// 					handle: '.avatar',
// 					containment: '.main-container',
// 					appendTo: '.main-container',
// 					start: function() {
// 						bodyWidth = $('.main-container').width();
// 						evaW = $(this).width();
// 					},
// 					drag: function() {
// 						var $this = $(this);
//
// 						var offsetX = $this.offset().left;
//
// 						evaCoordXPos = offsetX;
// 					},
// 					stop: function() {
// 						var $this = $(this);
//
// 						if(s.message.position && typeof(s.message.position) == 'string') {
// 							switch(s.message.position) {
// 								case 'edit':
// 									$.cookie('eva-position-edit-x', $this.position().left, {'path': '/'});
// 									$.cookie('eva-position-edit-y', $this.position().top, {'path': '/'});
// 									break;
// 								default:
// 									break;
// 							}
// 						} else if(s.writePosition && !s.message.position) {
// 							$.cookie('eva-position-x', $this.position().left, {'path': '/'});
// 							$.cookie('eva-position-y', $this.position().top, {'path': '/'});
// 						}
// 					}
// 				});
// 			} else {
// 				var dx = false;
// 				var dy = false;
//
// 				$('.eva-container').find('.avatar').bind('touchstart', function(event) {
// 					bodyWidth = $('.main-container').width();
// 					evaW = $(this).width();
//
// 					var startOffset = $(this).offset();
// 					var startTouch = event.originalEvent.touches[0] || event.originalEvent.changedTouches[0];
//
// 					dx = startTouch.pageX - startOffset.left;
// 					dy = startTouch.pageY - startOffset.top;
//
// 					return false;
// 				});
// 				$('.eva-container').find('.avatar').bind('touchmove', function(event) {
// 					var touch = event.originalEvent.touches[0] || event.originalEvent.changedTouches[0];
// 					var newX = touch.pageX - dx;
// 					var newY = touch.pageY - dy;
//
// 					b.eva.css({'top': newY, 'left': newX});
//
// 					if(newX < (bodyWidth/2 - evaW/2)) {
// 						b.eva.addClass('left-side');
// 					} else {
// 						b.eva.removeClass('left-side');
// 					}
//
// 					return false
// 				});
// 				$('.eva-container').find('.avatar').bind('touchend', function(event) {
// 					var touch = event.originalEvent.touches[0] || event.originalEvent.changedTouches[0];
//
// 					if(s.message.position && typeof(s.message.position) == 'string') {
// 						switch(s.message.position) {
// 							case 'edit':
// 								$.cookie('eva-position-edit-x', touch.pageX - dx, {'path': '/'});
// 								$.cookie('eva-position-edit-y', touch.pageY - dy, {'path': '/'});
// 								break;
// 							default:
// 								break;
// 						}
// 					} else if(s.writePosition && !s.message.position) {
// 						$.cookie('eva-position-x', touch.pageX - dx, {'path': '/'});
// 						$.cookie('eva-position-y', touch.pageY - dy, {'path': '/'});
// 					}
//
// 					return false;
// 				});
// 			}
//
// 			eva.open();
// 		},
//
// 		open : function() {
// 			var o = s.message;
//
// 			b.message.css('height', o.height);
// 			b.messagesBlock.css('width', o.width);
//
// 			// init form application
// 			if($('.eva-slider-form').length) {
// 				eva.initForm($('.eva-slider-form'));
// 			} else if($('.eva-content').length) {
// 				o.content = $('.eva-content').children();
// 				b.message.children('.cont').html(o.content);
// 			}
//
// 			eva.message();
// 			eva.position();
// 			eva.show();
// 		},
//
// 		position : function(type) {
// 			var x = $.cookie('eva-position-x');
// 			var y = $.cookie('eva-position-y');
//
// 			type = typeof(type) == 'undefined' ? 'message' : 'tooltip';
// 			var options = (type == 'message') ? s.message : s.tooltip;
//
// 			if(options.position && typeof(options.position) == 'string') {
// 				switch(options.position) {
// 					case 'edit':
// 						x = $.cookie('eva-position-edit-x');
// 						y = $.cookie('eva-position-edit-y');
// 						break;
// 					default:
// 						break;
// 				}
// 			}
//
// 			b.eva.css({'display': 'block', 'visibility': 'hidden'});
// 			b.messagesBlock.css('width', options.width);
//
// 			var contentHeight = 0;
//
// 			if(type == 'message') {
// 				b.message.css('height', options.height);
// 				contentHeight = b.message.outerHeight();
// 			} else {
// 				b.tooltip.css('height', options.height);
// 				contentHeight = b.tooltip.outerHeight();
// 			}
//
// 			var evaW = b.eva.width();
// 			var evaH = b.eva.height();
//
// 			var contentW = b.messagesBlock.width();
// 			var contentH = b.messagesBlock.height();
//
// 			var bodyW = $('body').width();
// 			var bodyH = $('body').height();
//
// 			var maxX = bodyW - evaW - 5;
// 			var maxY = bodyH - evaH - 5;
//
// 			if(options.position) {
// 				// set predefined position
// 					switch(options.position) {
// 						case 'center':
// 							x = parseInt((bodyW - contentW)/2);
// 							y = parseInt((bodyH - evaH)/2 + contentHeight/2);
// 							break;
// 						case 'edit':
// 							if(options.error == false) {
// 								x = parseInt((bodyW - contentW)/2) - 80;
// 								y = parseInt((bodyH - evaH + contentHeight)/2);
// 								if(y < 420) {
// 									y = 420;
// 								}
//
// 								$.cookie('eva-position-edit-x', x, {'path': '/'});
// 								$.cookie('eva-position-edit-y', y, {'path': '/'});
// 							}
//
// 							break;
// 						case 'showButtons':
// 							var $block = $('.home-nav');
// 							var offset = $block.offset();
// 							y = offset.top + $block.height() + 25 + contentH;
// 							x = offset.left + parseInt(($block.width() - contentW)/2) + 190;
// 							break;
// 						default:
// 							if(typeof(options.position) == 'object') {
// 								var $block = options.position;
// 								var offset = $block.offset();
//
// 								y = offset.top + parseInt(($block.height() - evaH - contentH)/2) + contentH;
// 								x = offset.left + parseInt(($block.width() - contentW)/2) + 190;
// 							}
// 							break;
// 					}
// 			} else {
// 				// set from cookie or in position by default
// 				x = (x == null) ? maxX : (x > maxX ? maxX : x);
// 				y = (y == null) ? maxY : (y > maxY ? maxY : y);
//
// 				x = (x < 5) ? 5 : x;
// 				y = (y < 5) ? 5 : y;
// 			}
//
// 			// set side of eva
// 			if((bodyW - evaW)/2 >= x) {
// 				b.eva.addClass('left-side');
// 			} else {
// 				b.eva.removeClass('left-side');
// 			}
//
// 			y+='px';x+='px';
//
// 			b.eva.css({
// 				'top': y,
// 				'left': x
// 			});
// 		},
// 		show : function() {
// 			if(b.eva.css('display') != 'block' || b.eva.css('visibility') == 'hidden') {
// 				b.eva.css({'display': 'none', 'visibility': 'visible'});
// 				b.eva.stop().fadeTo(250, 1);
// 			}
// 		},
// 		message : function(message, title) {
// 			b.messagesBlock.css('width', s.message.width);
//
// 			var o = s.message;
//
// 			if(typeof(message) != 'undefined') {
// 				b.message.children('.text').text(message);
// 			} else {
// 				if(o.error !== false) {
// 					b.message.children('.text').html(o.error);
// 				} else {
// 					b.message.children('.text').html(o.message);
// 				}
// 			}
// 			if(typeof(title) != 'undefined') {
// 				b.message.children('.text').html(title);
// 			} else {
// 				b.message.children('.title').html(o.title ? o.title : '');
// 			}
//
// 			if(o.title || o.message || o.content || o.error) {
// 				if(o.error !== false) {
// 					b.eva.addClass('eva-error');
// 				} else {
// 					b.eva.removeClass('eva-error');
// 				}
// 				b.eva.addClass('has-message');
// 			} else {
// 				b.eva.removeClass('has-message');
// 			}
//
// 			b.tooltip.children('.text').html('');
// 			b.tooltip.children('.title').html('');
// 			b.tooltip.children('.cont').empty();
//
// 			b.tooltip.stop(true).fadeTo(0, 0, function() {
// 				$(this).hide();
// 				b.message.stop(true).fadeTo(0, 1);
// 			});
// 		},
//
// 		say : function(message, title, $content) {
// 			var o = s.tooltip;
//
// 			if(typeof(message) == 'undefined' || message === false) {
// 				message = o.message;
// 			}
// 			if(typeof(title) == 'undefined' || title === false) {
// 				title = o.title;
// 			}
//
// 			b.eva.removeClass('eva-error');
//
// 			if(typeof($content) != 'undefined') {
// 				o.content = $content;
// 				b.tooltip.children('.cont').html($content);
// 			}
//
// 			b.tooltip.children('.text').html(message);
// 			b.tooltip.children('.title').html(title);
//
// 			b.messagesBlock.css('width', s.tooltip.width);
// 			b.tooltip.css('height', s.tooltip.height);
//
// 			s.showStatus = true;
// 			b.eva.addClass('has-message');
// 			b.message.stop(true).fadeTo(0, 0, function() {
// 				$(this).hide();
// 				b.tooltip.stop(true).fadeTo(0, 1);
// 			});
// 		},
// 		isOpened : function() {
// 			if(b.eva.hasClass('has-message')) {
// 				if(b.message.attr('display') == 'block') {
// 					return 'message';
// 				} else if(b.tooltip.attr('display') == 'block') {
// 					return 'tooltip';
// 				}
// 			}
//
// 			return false;
// 		},
// 		overlay : function(action) {
// 			if(typeof(action) == 'undefined' || action == true) {
// 				if(b.content.find('.eva-overlay').length == 0) {
// 					b.content.append('<div class="eva-overlay isLoading"></div>');
// 					eva.clearTimers();
//
// 					var $overlay = b.content.find('.eva-overlay');
// 					$overlay.stop().fadeTo(200, 1);
// 					s.writePosition = false;
//
// 					$overlay.click(function() {
// 						if(b.content.find('.eva-overlay').hasClass('isLoading')) {
// 							return false;
// 						}
//
// 						s.showTooltip = true;
//
// 						s.tooltip.title = '';
// 						s.tooltip.message = '';
// 						s.tooltip.content = false;
// 						s.tooltip.height = 'auto';
// 						s.tooltip.width = 'auto';
//
// 						$(this).fadeTo(200, 0, function() {
// 							$(this).remove();
// 						});
// 						b.eva.fadeOut(200, function() {
// 							s.writePosition = true;
// 							eva.message();
// 							eva.position();
// 							eva.show();
// 						});
// 						return false;
// 					});
// 				} else {
// 					s.showTooltip = false;
// 					b.content.find('.eva-overlay').addClass('isLoading');
// 				}
// 			} else {
// 				b.content.find('.eva-overlay').removeClass('isLoading').click();
// 			}
// 		},
//
// 		/*
// 		 * Open eva-confirm window (with overlay)
// 		 *
// 		 * @param function callback Callback function, run after confirmation
// 		 * @param string text Confirm text
// 		 *
// 		 */
// 		confirm : function(callback, message) {
// 			if(typeof(message) == 'undefined' || message == '') {
// 				message = 'Are you sure you want to do this?';
// 			}
//
// 			eva.content(s.confirmTemplate, {'title': message}, function($content) {
// 				$content.find('.ok').click(function() {
// 					callback();
// 					eva.overlay(false);
// 					return false;
// 				});
// 				$content.find('.cancel').click(function() {
// 					eva.overlay(false);
// 					return false;
// 				});
// 			});
// 		},
//
// 		/*
// 		 * Open eva content as tooltip in modal window (with overlay)
// 		 *
// 		 * @param object $data Block to show in eva
// 		 * @param object options Tooltip options (width, height, title, message, content)
// 		 * @param function callback callback function, run after tooltip showed
// 		 *
// 		 */
// 		content : function($data, options, callback) {
// 			if(typeof($data) == 'undefined') {
// 				$.eva.overlay(false);
// 			} else {
// 				eva.overlay();
//
// 				if($data) {
// 					if(typeof($data) != 'object') {
// 						$data = $($data);
// 					}
// 					$data = $data.clone();
// 				} else {
// 					$data = s.loaderTemplate;
// 				}
//
// 				b.eva.fadeOut(200, function() {
// 					if(b.content.find('.eva-overlay').length) {
// 						if(typeof(options) != 'object') {
// 							options = {};
// 						}
//
// 						eva.setTooltipOptions(options);
//
// 						if(options.form) {
// 							eva.say(false, false, s.formTemplate);
// 							eva.initForm($data, true);
// 						} else {
// 							eva.say(false, false, $data);
// 						}
// 						if(options.showTooltip === false) {
// 							s.showTooltip = false;
// 						} else {
// 							s.showTooltip = true;
// 						}
//
// 						b.eva.css({'visibility': 'hidden', 'opacity': 0, 'display': 'block'});
//
// 						b.content.find('.eva-overlay').removeClass('isLoading');
// 						if(typeof(callback) != 'undefined') {
// 							callback($data);
// 						}
//
// 						eva.position('tooltip');
// 					}
//
// 					eva.show();
// 				});
// 			}
// 		},
// 		defineOptions : function() {
// 			s.message = {
// 				'title': (typeof(evaTitle) != 'undefined' ? evaTitle : ''),
// 				'message': (typeof(evaMessage) != 'undefined' ? evaMessage : ''),
// 				'error': (typeof(evaError) != 'undefined' ? evaError : false),
// 				'content': false,
// 				'width': typeof(evaWidth) != 'undefined' ? evaWidth : 'auto',
// 				'height': (typeof(evaHeight) != 'undefined') ? options.height : 'auto',
// 				'position': typeof(evaPosition) != 'undefined' ? evaPosition : false
// 			}
// 			s.tooltip = {
// 				'title': '',
// 				'message': '',
// 				'content': false,
// 				'width': 'auto',
// 				'height': 'auto',
// 				'position': 'center'
// 			}
// 		},
// 		setTooltipOptions : function(options) {
// 			s.tooltip.title = (typeof(options.title) != 'undefined') ? options.title : '';
// 			s.tooltip.message = (typeof(options.message) != 'undefined') ? options.message : '';
//
// 			s.tooltip.height = (typeof(options.height) != 'undefined') ? options.height : 'auto';
// 			s.tooltip.width = (typeof(options.width) != 'undefined') ? options.width : 'auto';
// 			s.tooltip.position = (typeof(options.position) != 'undefined') ? options.position : 'center';
// 		},
// 		confirmLeave : function() {
// 			if(s.setConfirmLeave) {
// 				return false;
// 			}
//
// 			$('a').click(function() {
// 				var $this = $(this);
// 				if(!$this.hasClass('content-box-edit') && !$this.hasClass('content-box-remove') && !$this.hasClass('zoom') && !$this.hasClass('download') && !$this.hasClass('remove') && !$this.hasClass('set-main') && !$this.hasClass('feedback-opener') && !$this.hasClass('add-building-link')) {
// 					if($('.eva-container').find($(this)).length == 0) {
// 						if($('[confirm-leave]').length > 0) {
// 							var text = $('[confirm-leave]').first().attr('confirm-leave');
// 						}
// 						if(typeof(text) == 'undefined' || text.length < 1){
// 							text = 'Do you realy want to leave? Changes will not be saved.';
// 						}
//
// 						var href = $this.attr('href');
// 						$.eva.confirm(function() {
// 							window.location.href = href;
// 						}, text);
//
// 						return false;
// 					}
// 				}
// 			});
//
// 			s.setConfirmLeave = true;
// 		},
// 		initScroll : function($block) {
// 			$block.jScrollPane({
// 				hideFocus: true
// 			}).bind(
// 				'jsp-scroll-y',
// 				function(event, scrollPositionY, isAtTop, isAtBottom) {
// 					if(isAtBottom) {
// 						$(this).addClass('isAtBottom');
// 					} else {
// 						$(this).removeClass('isAtBottom');
// 					}
// 					if(isAtTop) {
// 						$(this).addClass('isAtTop');
// 					} else {
// 						$(this).removeClass('isAtTop');
// 					}
// 				}
// 			);
// 		},
// 		clearTimers : function() {
// 			window.clearTimeout(s.timerShow);
// 			s.timerShow = false;
//
// 			window.clearTimeout(s.timerHide);
// 			s.timerHide = false;
// 		}
// 	}
//
// 	$.extend({
// 		eva:eva
// 	});
//
// 	/*
// 	 *   Global vars:
// 	 *
// 	 *   evaPosition (center, edit, showButtons, $object) - position eva on load: if $object - in center of the block; if edit - don`t use global coords
// 	 *   evaWidth - width of eva container
// 	 *   evaTitle - default title for page
// 	 *   evaMessage = default text for page
// 	 *   evaError - error text for page
// 	 *
// 	 **/
//
// 	$.cookie = function(key, value, options) {
// 		// key and at least value given, set cookie...
// 		if (arguments.length > 1 && (!/Object/.test(Object.prototype.toString.call(value)) || value === null || value === undefined)) {
// 			options = $.extend({}, options);
//
// 			if (value === null || value === undefined) {
// 				options.expires = -1;
// 			}
//
// 			if (typeof options.expires === 'number') {
// 				var days = options.expires, t = options.expires = new Date();
// 				t.setDate(t.getDate() + days);
// 			}
//
// 			value = String(value);
//
// 			return (document.cookie = [
// 				encodeURIComponent(key), '=', options.raw ? value : encodeURIComponent(value),
// 				options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
// 				options.path ? '; path=' + options.path : '',
// 				options.domain ? '; domain=' + options.domain : '',
// 				options.secure ? '; secure' : ''
// 			].join(''));
// 		}
//
// 		// key and possibly options given, get cookie...
// 		options = value || {};
// 		var decode = options.raw ? function(s) {return s;} : decodeURIComponent;
//
// 		var pairs = document.cookie.split('; ');
// 		for (var i = 0, pair; pair = pairs[i] && pairs[i].split('='); i++) {
// 			if (decode(pair[0]) === key) return decode(pair[1] || ''); // IE saves cookies with empty string as "c; ", e.g. without "=" as opposed to EOMB, thus pair[1] may be undefined
// 		}
// 		return null;
// 	};
//
// })(jQuery)
//
