$(function() {
	$(document).ready(function() {
		iniToolTips();
		web.iniAutoResizeWindow();
		web.initBootstrap();
		web.initInfuture();
		web.initFormElements();
		web.initAutosizeTewxtarea();
		web.initDatePicker();
		web.startGalleryBackground();
		web.reinitCheckboxControl();
//		web.initCheckboxControl();
		web.sortYearInDatepicker();
		web.initSelectize();
	});
});


function iniToolTips () {
	$(document).tooltip({
		items: ".autoform-element-errors",
		content: function() {
			var $element = $(this).find('label'); return $element.html();
		},
		position: {
			my: "center bottom-20",
			at: "center top",
			using: function( position, feedback ) {
				$( this ).css( position );
				$( "<div>" )
					.addClass( "arrow" )
					.addClass( feedback.vertical )
					.addClass( feedback.horizontal )
					.appendTo( this );
			}
		}
	});
}

var myResize = setInterval(function(){web.iniAutoResizeWindow()}, 1000);

var web = {
	isLoadingUpdateUrl: false,
	iniAutoResizeWindow: function()
	{
		//$(document).ready(function(){
			var $func = function(){
				var height2 = $('.content-outer').outerHeight() - 117;
				if(height2 < 500) {
					var $body = $('body');
					if(!$body.hasClass('page_index') && !$body.hasClass('page_support') && !$body.hasClass('page_about') && !$body.hasClass('page_policy') && !$body.hasClass('page_advertisewithus')) {
						height2 = 500;
					}
				}
				$('.content-inner').css('min-height', height2);

				var height = $('.content-inner').outerHeight();
				$('.content-with-bottomwhite').css('min-height', height - 500 + 82);

			};
			$(window).bind('resize', $func);
			$func();
		//});

	},
	registration:function(content)
	{
		$('.home-form').html(content);
		web.afterUpdatePage();
	},
	submitForm:function(object, data)
	{
		alert('435');
		var content;

		if (data.data) {
			content = data.data.content;
		} else {
			content = data.content;
		}

		$(object).html(content);

		web.afterUpdatePage();
	},
	login:function(content)
	{
		$('.panelLogin').html(content);
		web.afterUpdatePage();
	},
	showHideLogin: function(target)
	{
		var $element = $('.panelLogin');

		if(!$element.hasClass('active')) {
			setTimeout(function() {
				$element.addClass('active');
				$(target).addClass('active');
			}, 100);
		}



		var $docClick2 = function(event)
		{
			if($(event.target).closest('.panelLogin').size() === 0 && $element.hasClass('active') && $(event.target).closest('.pbox-overlay').size() === 0) {
				$element.removeClass('active');
				$(target).removeClass('active');
				$(document).unbind('click', $docClick2);
			}
		};
		$(document).bind('click', $docClick2);

		return false;
	},
	changeFind: function (target)
	{
		var $target = $(target);

		$target.closest('ol').find(' > li:not(:first-child)').css('display', 'none');
		var number = parseFloat($target.val()) + 1;
		$action = $target.closest('ol').find(' > li:nth-child(' + number + ') input').data('action');
		$target.closest('ol').find(' > li:nth-child(' + number + ')').css('display', 'block').find('input').focus();
		$target.closest('form').attr('action', $action);
	},
	initDatePicker: function()
	{
		$( ".datepicker" ).each(function(){
			var $this = $(this);
			var d = new Date();
			var year = d.getFullYear();


			if(!$this.hasClass('isDatePicker')){
				$this.addClass('isDatePicker');
				$('#' + $this.attr('id')).datepicker({
					changeMonth: true,
					changeYear: true,
					yearRange: '-80:-0'
				});
			}
		});

	},
	initFormElements: function()
	{
		$('form').each(function(){
			var $this = $(this);
			$this.find('li input.form-checkbox').removeClass('form-checkbox').next().addClass('form-checkbox').closest('li').addClass('form-checkbox');
			$this.find('li input.form-radio').removeClass('form-radio').next().addClass('form-radio').closest('li').addClass('form-radio');
			$this.find('.form-checkbox input[type="checkbox"].form-period').each(function(){
				web.showHidePeriodTo(this);
				// web.showHidePeriodToIfCurrentWorkHereTrue(this);
			});
			$this.find('.form-html').closest('li').addClass('form-html');

			$this.find('[required]').closest('li').addClass('form-required');
			$this.find('[disabled]').closest('li').addClass('form-disabled');

			$this.find('.form-local-search:not(.isLocalSearch)').addClass('isLocalSearch').keyup(function(e){
				if (e.keyCode == 38 || e.keyCode == 40) {
					web.localSearchKeyCode(e.keyCode, this);
					return false;
				} else {
					return web.localSearchKeyPress(this);
				}

			}).focus(function(){
				return web.localSearchFocus(this);
			}).focusout(function(){
				return web.localSearchFocusOut(this);
			});

			$this.find('textarea.max-800, textarea.max-1000, textarea.max-5000, textarea.max-10000').each(function(){
				var $element = $(this);
				if($element.hasClass('max-800')) {
					$element.parent().addClass('counterTextarea').attr('data-max', '800').attr('data-current', 'max 800 symbols');
					$element.removeClass('max-800');
				}
				if($element.hasClass('max-1000')) {
					$element.parent().addClass('counterTextarea').attr('data-max', '1000').attr('data-current', 'max 1000 symbols');
					$element.removeClass('max-1000');
				}
				if($element.hasClass('max-5000')) {
					$element.parent().addClass('counterTextarea').attr('data-max', '5000').attr('data-current', 'max 5000 symbols');
					$element.removeClass('max-5000');
				}
				if($element.hasClass('max-10000')) {
					$element.parent().addClass('counterTextarea').attr('data-max', '10000').attr('data-current', 'max 10000 symbols');
					$element.removeClass('max-10000');
				}
			});

			web.updateDeleteMultilist($this);
		});

		web.initCounterTextarea();
	},
	initCounterTextarea: function()
	{
		$('.autoform-element-inner.counterTextarea:not(.isCounterTextarea)').each(function(){
			var $this = $(this);
			var $textarea = $this.find('textarea');
			$this.addClass('isCounterTextarea');
			var $func = function(){
				if($textarea.val().length > 0) {
					$this.attr('data-current', 'max ' + $this.data('max') + ' symbols ' + '(current ' + $textarea.val().length + ')');
				} else {
					$this.attr('data-current', 'max ' + $this.data('max') + ' symbols');
				}
			}
			var $func2 = function(e) {
				if($textarea.val().length > $this.data('max')) {
					if (e.keyCode != 38 && e.keyCode != 40) {
						return false;
					}
				}
			}

			$this.find('textarea').bind('keydown', $func2).keydown();
			$this.find('textarea').bind('keyup', $func).keyup();
		});
	},
	initAutosizeTewxtarea: function()
	{
		var $textarea = $('textarea');
		$textarea.autosize();
		setTimeout(function(){
			$textarea.trigger('autosize.resize')
		}, 100);
	},
	initInfuture: function()
	{
		$('.infuture').attr('onclick', '');
		$('.infuture').click(function(){
			box.message('Message', 'Comming Soon');
			return false;
		});
	},
	initBootstrap: function()
	{
		$('.bootstripe').each(function(){
			var $this = $(this);
			if(!$this.hasClass('isBootstripe') && $this.closest('.form-template').size() === 0) {
				$this.addClass('isBootstripe');
				$('<div class="bootstrap-custom"></div>').insertBefore($(this));
				$(this).prependTo($(this).parent().find('.bootstrap-custom'));
				$this.selectpicker();

				setTimeout(function(){
					$this.next().find('.caret').addClass('icon-down').append('<span></span>');
				},100);
			}
		});
	},
	initSelectize: function()
	{
		$('select.selectize').each(function(){
			var $this = $(this);
			if($this.closest('.form-template').size() !== 0){
				return;
			}
			var values = $(this).val();
			if(!$this.hasClass('isSelectize')) {
				$this.addClass('isSelectize');
				$('<div class="selectize-custom"></div>').insertBefore($(this));
				$(this).prependTo($(this).parent().find('.selectize-custom'));

				if(typeof $this.data('selectize-order') !== 'undefined' && $this.data('selectize-order') === true) {
					var order = 'order';
				} else {
					var order = 'text';
				}

				if(typeof $this.data('selectize-add-new-position') !== 'undefined' && $this.data('selectize-add-new-position') === true) {
					var $createFunc = function(input) {
						return {
							value: ('new%' + input),
							text: input,
							title: input
						}
					};
				} else {
					var $createFunc = false;
				}

				var $select = $this.selectize({
					create: $createFunc,
					sortField: order,
					labelField: 'title',
					render: {
						option: function(item, escape) {
							return '<div data-value="' + item.value + '" data-selectable class="option">' + item.text + '</div>';
						}
					},
					load: function(query, callback) {
						$url = $this.data('selectize-url');
						if($url !== '') {
							if (query.length) {
								$url = $url + query + '/';
							}

							var $selectize = $select[0].selectize;
							var $func = function(data){
								web.updateSelectize(data, $selectize, $this);
							}
							web.ajaxGet($url, $func);
						}
					}
				});
				var $selectize = $select[0].selectize;

				if(typeof $this.data('selectize-customitems') !== 'undefined') {
					$selectize.clearOptions();
					var $customList = $($this.data('selectize-customitems'));
					$customList.find('> li').each(function(key, value){
						var $item = $(this);
						if(order === 'order') {
							var textorder = $item.data('itemorder');
						} else {
							textorder = false;
						}
						var itemtitle = $item.data('itemtitle');

						$selectize.addOption({value:$item.data('itemid'), text:$item.html(), order:textorder, title:itemtitle});
					});
				}

				if(values !== '') {
					$selectize.setValue(values);
				}

				// Autoload content
				if(typeof $this.data('selectize-url-next-page') !== 'undefined') {
					var $scroll = function(){
						var url_nextpage = $this.attr('data-selectize-url-next-page');

						if(typeof url_nextpage !== 'undefined'){
							if(url_nextpage.length) {
								var height = $(this).outerHeight(true);
								var iScrollHeight = $(this).prop("scrollHeight");
								var scrollBottom = iScrollHeight - height - $(this).scrollTop();

								if(scrollBottom <= 200) {
									var $func = function(data){
										web.updateSelectize(data, $selectize, $this);
										$this.next().find('.selectize-dropdown-content').bind('scroll', $scroll);
									}

									$this.next().find('.selectize-dropdown-content').unbind('scroll', $scroll);
									web.ajaxGet(url_nextpage, $func);
								}
							}
						}
					}
					$this.next().find('.selectize-dropdown-content').bind('scroll', $scroll);
				}


				if($this.is(':required')) {
					var $input = $this.parent().find('input');
					var $boxselect = $input.closest('.selectize-control');
					$item = $boxselect.find('div.item');

					var $submit = $this.closest('form').find('input:submit');

					$submit.click(function(){
						var $item = $boxselect.find('div.item');
						if($item.size() !== 0 && $item.html().length > 0) {
							$input.removeAttr('required');
						} else {
							$input.attr('required', 'required');
						}
					});

					if($item.size() !== 0 && $item.html().length > 0) {
						$input.removeAttr('required');
					} else {
						$input.attr('required', 'required');
					}
				}
			}
		});
	},
	updateSelectize: function(data, $selectize, $select)
	{
		var $data = data.selectize;
		if($data.clear_data === 'true') {
			$selectize.clearOptions();
		}

		$($data.data).each(function(key, value){
			$selectize.addOption({value:value.value, text:value.text, order:value.itemorder, title:value.itemtitle});
		});

		$select.attr('data-selectize-url-next-page', $data.next_page);

		$selectize.refreshOptions();
	},
	initUserGallery: function(element)
	{
		var $elements = $(element);
		$elements.each(function(){
			var $this = $(this);
			if(!$this.hasClass('userGallery')){
				$this.addClass('userGallery');
				if($this.find('li:first-child').hasClass('hidden')) {
					$this.find('li:nth-child(2)').addClass('active');
				} else {
					$this.find('li:first-child').addClass('active');
				}

				if($this.find('li:not(.hidden)').size() > 1) {
					$this.find('.btn-next').addClass('active');
				}
			}
		});
	},
	reinitUserGallery: function(element)
	{
		var $elements = $(element);
		$elements.removeClass('userGallery');
		$elements.find('li').removeClass('active');
		$elements.find('.btn-next').removeClass('active');
		$elements.find('.btn-prev').removeClass('active');

		this.initUserGallery($elements);
	},
	nextUserGallery: function(target){
		var $elementNext = $(target);
		var $object = $elementNext.closest('.userGallery');
		var $elements = $object.find('li:not(.hidden)');

		var current = 0;
		$elements.each(function(key, value){
			if($(value).hasClass('active')){
				current = key;
				return;
			}
		});
		current++;

		if($elements.size() > current) {
			$object.find('.btn-prev').addClass('active');
			var correct = 0;
			if($object.find('li:first-child').hasClass('hidden')){
				correct = 1;
			}

			$object.find('li:nth-child(' + (current + correct) + ')').removeClass('active');
			current++;
			$object.find('li:nth-child(' + (current + correct) + ')').addClass('active');
		}

		if($elements.size() === current) {
			$elementNext.removeClass('active');
		}
	},
	prevUserGallery: function(target){
		var $elementPrev = $(target);
		var $object = $elementPrev.closest('.userGallery');
		var $elements = $object.find('li:not(.hidden)');

		var current = 0;
		$elements.each(function(key, value){
			if($(value).hasClass('active')){
				current = key;
				return;
			}
		});
		current++;

		if(current > 1) {
			$object.find('.btn-next').addClass('active');
			var correct = 0;
			if($object.find('li:first-child').hasClass('hidden')){
				correct = 1;
			}

			$object.find('li:nth-child(' + (current + correct) + ')').removeClass('active');
			current--;
			$object.find('li:nth-child(' + (current + correct) + ')').addClass('active');
		}

		if(1 === current) {
			$elementPrev.removeClass('active');
		}
	},
	initGalleryBackground: function()
	{
		var $gallery = $('.galleryBackground');

		if(!$gallery.hasClass('isInited')){
			$gallery.addClass('isInited');

			var $elements = $gallery.find('img').each(function(){
				var $this = $(this);

				$this.load(function(){
					$(this).addClass('img-loaded');
				});
				$this.ready(function(){
					$(this).addClass('img-loaded');
				});
			});
		}

		web.resizeGalleryBackground();
		$(window).resize(function(){
			web.resizeGalleryBackground();
		});
	},
	resizeGalleryBackground:function()
	{
		var $gallery = $('.galleryBackground');
		var width = $gallery.innerWidth();
		var height = $gallery.innerHeight();

		var img_width = 1920;
		var img_height = 1020;

		var size = width/height;
		var size_img = img_width/img_height;


		if(size_img < size) {
			var gallery_width = width;
			var gallery_height = width / img_width * img_height;
			var gallery_left = 0;
			var gallery_top = (gallery_height - height) / 2 * -1;
		} else {
			var gallery_height = height;
			var gallery_width =  height / img_height * img_width;
			var gallery_top = 0;
			var gallery_left = (gallery_height - height) / 2 * -1;
		}

		$gallery.find('img').css('width', gallery_width).css('height', gallery_height).css('left', gallery_left).css('top', gallery_top);
	},
	startGalleryBackground: function(){
		var $gallery = $('.galleryBackground');
		var $elements = $gallery.find('img');

		var pos = 0;
		if($gallery.find('img.active').size() != 0) {
			var pos = $gallery.find('img.active').data('id');
		}

		if($elements.size() > 0) {
			if(pos === 0){
				pos++;
			} else if(pos === $elements.size()) {
				$($elements.get(pos - 1)).removeClass('active');
				pos = 1;
			} else if(pos < $elements.size()) {
				$($elements.get(pos - 1 )).removeClass('active');
				pos++;
			}

			$($elements.get(pos - 1)).addClass('active');

			setTimeout(function(){
				web.startGalleryBackground();
			}, 8000);
		}
	},
	blockProfileEdit:function(status){
		if(typeof status === 'undefined') {
			status = true;
		}
		if(status === true) {
			$('.userprofile, .updates, .block-discussions, .school, .schools_updates, .company, .companies_updates').addClass('isBlockEdit');
		} else {
			$('.userprofile, .updates, .block-discussions, .school, .schools_updates, .company, .companies_updates').removeClass('isBlockEdit');
		}
	},
	submitForm: function(target)
	{
		var $target = $(target);
		var data = $target.serialize();
		var url = $target.attr('action');
		var $func = function(){
			location.reload(true);
		};

		this.ajaxGet(url, $func, data, false, 'POST');
		return false;
	},
	ajaxGet:function(target, callback, data, retunObj, method){
		return system.ajaxGet(target, callback, data, retunObj, method);
	},
	cancelEditBlock:function(target){
		var $target = $(target).closest('form');
		$target.prev().removeClass('hidden')
		$target.remove();
		this.blockProfileEdit(false);

		return false;
	},
	showHidePeriodTo:function(target)
	{
		var $target = $(target);
		var $form = $target.closest('form');
		var id = $form.attr('id');

		if($target.is(':checked')){
			$form.find('#' + id + '-' + 'yearTo').removeAttr('required').closest('li').css('display', 'none');
			$form.find('#' + id + '-' + 'monthTo').removeAttr('required').closest('li').css('display', 'none').
				prev().css('display', 'none');
		} else {
			var $element1 = $form.find('#' + id + '-' + 'yearTo');
			var $element2 = $form.find('#' + id + '-' + 'monthTo');
			if(!$element1.hasClass('no-required')) {
				$element1.attr('required', 'required')
			}
			if(!$element2.hasClass('no-required')) {
				$element2.attr('required', 'required')
			}
			$element1.closest('li').css('display', 'inline-block');
			$element1.closest('li').css('display', 'inline-block').prev().css('display', 'inline-block');
		}
	},
	showHidePeriodToIfCurrentWorkHereTrue:function(target)
	{
		console.log('ffff');
		var $target = $(target);
		var $form = $target.closest('form');
		var id = $form.attr('id');

		$form.find('#' + id + '-' + 'yearTo').removeAttr('required').closest('li').css('display', 'none');
		$form.find('#' + id + '-' + 'monthTo').removeAttr('required').closest('li').css('display', 'none').
		prev().css('display', 'none');
	},
	multiList: function(target)
	{
		var $target = $(target);
		var $form = $target.closest('form');
		var $template = $form.find('.form-template').clone();
		var last_id = parseFloat($form.data('last_id'));

		var countEmpty = 0;
		$form.find('.isMiltiList:not(div)').each(function(){
			if($(this).val() === ''){
				countEmpty++;
			}
		});

		if(countEmpty <= 1){
			last_id++;

			var $id = $template.attr('id');
			var new_id = $id.substr(0, $id.length - 2) + last_id;
			$template.attr('id', new_id).removeClass('form-template');

			$template.html($template.html().replaceAll('%i', last_id));
			$template.html($template.html().replaceAll('isBootstripe', ''));
			$form.data('last_id', last_id);

			$template.insertBefore($form.find('.form-template'));
			web.initBootstrap();
			web.initSelectize();
			this.deleteListFromMultilist($form);
		}
	},
	updateDeleteMultilist:function($form)
	{
		var $elements = $form.find('fieldset:not(fieldset.form-template) .deleteMiltiList');
		$elements.css('display', 'block');
		var last = $elements.get($elements.size()-1);
		$(last).css('display', 'none');
	},
	deleteListFromMultilist: function(target)
	{
		var $target = $(target);
		var $form = $target.closest('form');

		$target.closest('fieldset').remove();
		this.updateDeleteMultilist($form);
	},
	showHideContactInfo: function(target){
		var $element = $('.userinfo-contactinfo');
		if($element.hasClass('opened')) {
			$element.removeClass('opened');
		} else {
			$element.addClass('opened');
		}
	},
	showHideConnectionsMenu: function(target)
	{
		var $target = $(target);
		var $element = $target.next();
		if($element.hasClass('active')) {
			$target.removeClass('icon-down');
			$element.addClass('icon-next');
			$element.removeClass('active');
		} else {
			$target.addClass('icon-down');
			$element.removeClass('icon-next');
			$element.addClass('active');
		}
		return false;
	},
	showHideFilterMenu: function(target)
	{
		var $target = $(target);
		var $element = $target.next();
		var cookie_name = $target.data('cookie');

		if($element.hasClass('active')) {
			$target.removeClass('icon-down');
			$target.addClass('icon-next');
			$element.removeClass('active');
			document.cookie = cookie_name + '=0 ; ; ';
		} else {
			$target.addClass('icon-down');
			$target.removeClass('icon-next');
			$element.addClass('active');
			document.cookie = cookie_name + '=1 ; ; ';
		}
		return false;
	},
	searchSubmit:function (target)
	{
		var action = $(target).closest('form').attr('action');

		var data = new Array();
		var connection = '';
		var region = '';
		var company = '';
		var industry = '';
		var school = '';
		var skill = '';
		$(target).closest('form').find('input[type="checkbox"]').each(function(key, value){
			var $this = $(this);
			if($this.is(':checked')){
				switch($this.data('key')){
					case 'connection':
						connection += ',' + $this.data('value');
						break;
					case 'region':
						region += ',' + $this.data('value');
						break;
					case 'company':
						company += ',' + $this.data('value');
						break;
					case 'industry':
						industry += ',' + $this.data('value');
						break;
					case 'school':
						school += ',' + $this.data('value');
						break;
					case 'skill':
						skill += ',' + $this.data('value');
						break;
				}
			}
		});
		var searchtext = $(target).closest('form').find('#filterpeople-searchpeople').val();

		var subAction = '';
		if(connection.length > 0) {
			connection = connection.substr(1);
			subAction += '&connection=' + connection;
		}
		if(region.length > 0) {
			region = region.substr(1);
			subAction += '&region=' + region;
		}
		if(company.length > 0) {
			company = company.substr(1);
			subAction += '&company=' + company;
		}
		if(industry.length > 0) {
			industry = industry.substr(1);
			subAction += '&industrypeople=' + industry;
		}
		if(school.length > 0) {
			school = school.substr(1);
			subAction += '&school=' + school;
		}
		if(skill.length > 0) {
			skill = skill.substr(1);
			subAction += '&skill=' + skill;
		}
		if(searchtext.length > 0) {
			subAction += '&searchpeople=' + searchtext;
		}


		if(subAction.length > 0) {
			subAction = subAction.substr(1);

			if(action.indexOf('?') >= 0) {
				action = action + subAction;
			} else {
				action = action + '?' + subAction;
			}
		}


		//$(target).closest('form').attr('action', action);
		//$(target).closest('form').find('input[type="submit"]').click();
		document.location.href = action;
	},
	searchCompanySubmit:function (target)
	{
		var action = $(target).closest('form').attr('action');

		var data = new Array();
		var industry = '';
		var type = '';
		var employer = '';
		$(target).closest('form').find('input[type="checkbox"]').each(function(key, value){
			var $this = $(this);
			if($this.is(':checked')){
				switch($this.data('key')){
					case 'industry':
						industry += ',' + $this.data('value');
						break;
					case 'type':
						type += ',' + $this.data('value');
						break;
					case 'employer':
						employer += ',' + $this.data('value');
						break;
				}
			}
		});
		var searchtext = $(target).closest('form').find('#filtercompany-searchcompany').val();


		var subAction = '';
		if(industry.length > 0) {
			industry = industry.substr(1);
			subAction += '&industrycompany=' + industry;
		}
		if(type.length > 0) {
			type = type.substr(1);
			subAction += '&typecompany=' + type;
		}
		if(employer.length > 0) {
			employer = employer.substr(1);
			subAction += '&employer=' + employer;
		}
		if(searchtext.length > 0) {
			subAction += '&searchcompany=' + searchtext;
		}


		if(subAction.length > 0) {
			subAction = subAction.substr(1);

			if(action.indexOf('?') >= 0) {
				action = action + subAction;
			} else {
				action = action + '?' + subAction;
			}
		}



		//$(target).closest('form').attr('action', action);
		//$(target).closest('form').find('input[type="submit"]').click();
		document.location.href = action;
	},
	searchGroupSubmit:function (target)
	{
		var action = $(target).closest('form').attr('action');

		var data = new Array();
		var access = '';
		$(target).closest('form').find('input[type="checkbox"]').each(function(key, value){
			var $this = $(this);
			if($this.is(':checked')){
				switch($this.data('key')){
					case 'access':
						access += ',' + $this.data('value');
						break;
				}
			}
		});
		var searchtext = $(target).closest('form').find('#filtergroup-searchgroup').val();

		var subAction = '';
		if(access.length > 0) {
			access = access.substr(1);
			subAction += '&access=' + access;
		}
		if(searchtext.length > 0) {
			subAction += '&searchgroup=' + searchtext;
		}

		if(subAction.length > 0) {
			subAction = subAction.substr(1);

			if(action.indexOf('?') >= 0) {
				action = action + subAction;
			} else {
				action = action + '?' + subAction;
			}
		}

		//$(target).closest('form').attr('action', action);
		//$(target).closest('form').find('input[type="submit"]').click();
		document.location.href = action;
	},
	searchSchoolSubmit:function (target)
	{
		var action = $(target).closest('form').attr('action');

		var data = new Array();
		var typeschool = '';
		$(target).closest('form').find('input[type="checkbox"]').each(function(key, value){
			var $this = $(this);
			if($this.is(':checked')){
				switch($this.data('key')){
					case 'type':
						typeschool += ',' + $this.data('value');
						break;
				}
			}
		});
		var searchtext = $(target).closest('form').find('#filterschool-searchschool').val();

		var subAction = '';
		if(typeschool.length > 0) {
			typeschool = typeschool.substr(1);
			subAction += '&typeschool=' + typeschool;
		}
		if(searchtext.length > 0) {
			subAction += '&searchschool=' + searchtext;
		}

		if(subAction.length > 0) {
			subAction = subAction.substr(1);

			if(action.indexOf('?') >= 0) {
				action = action + subAction;
			} else {
				action = action + '?' + subAction;
			}
		}

		//$(target).closest('form').attr('action', action);
		//$(target).closest('form').find('input[type="submit"]').click();
		document.location.href = action;
	},
	searchJobSubmit:function (target)
	{
		var action = $(target).closest('form').attr('action');

		var data = new Array();
		var regionjob = '';
		var industryjob = '';
		var skilljob = '';
		$(target).closest('form').find('input[type="checkbox"]').each(function(key, value){
			var $this = $(this);
			if($this.is(':checked')){
				switch($this.data('key')){
					case 'region':
						regionjob += ',' + $this.data('value');
						break;
					case 'industry':
						industryjob += ',' + $this.data('value');
						break;
					case 'skill':
						skilljob += ',' + $this.data('value');
						break;
				}
			}
		});
		var searchtext = $(target).closest('form').find('#filterjob-searchjob').val();

		var subAction = '';
		if(regionjob.length > 0) {
			regionjob = regionjob.substr(1);
			subAction += '&regionjob=' + regionjob;
		}
		if(industryjob.length > 0) {
			industryjob = industryjob.substr(1);
			subAction += '&industryjob=' + industryjob;
		}
		if(skilljob.length > 0) {
			skilljob = skilljob.substr(1);
			subAction += '&skilljob=' + skilljob;
		}
		if(searchtext.length > 0) {
			subAction += '&searchjob=' + searchtext;
		}

		if(subAction.length > 0) {
			subAction = subAction.substr(1);

			if(action.indexOf('?') >= 0) {
				action = action + subAction;
			} else {
				action = action + '?' + subAction;
			}
		}

		//$(target).closest('form').attr('action', action);
		//$(target).closest('form').find('input[type="submit"]').click();
		document.location.href = action;
	},
	searchJobInJob:function (target)
	{
		var action = $(target).closest('form').attr('action');

		var data = new Array();
		var skilljob = '';
		var industryjob = '';
		$(target).closest('form').find('input[type="checkbox"]').each(function(key, value){
			var $this = $(this);
			if($this.is(':checked')){
				switch($this.parent().data('key')){
					case 'skill':
						skilljob += ',' + $this.parent().data('id');
						break;
					case 'industry':
						industryjob += ',' + $this.parent().data('id');
						break;
				}
			}
		});
		var searchtext = $(target).closest('form').find('#searchjob-search').val();
		var searchcountry = $(target).closest('form').find('#searchjob-country').val();
		var searchstate = $(target).closest('form').find('#searchjob-state').val();
		var searchstate1 = $(target).closest('form').find('#searchjob-state1').val();
		var searchcity = $(target).closest('form').find('#searchjob-city').val();

		var subAction = '';
		subAction += '&search=' + searchtext;
		if(skilljob.length > 0) {
			skilljob = skilljob.substr(1);
			subAction += '&skill=' + skilljob;
		}
		if(industryjob.length > 0) {
			industryjob = industryjob.substr(1);
			subAction += '&industry=' + industryjob;
		}
		if(searchcountry.length > 0) {
			subAction += '&country=' + searchcountry;
		}
		if(searchstate.length > 0) {
			subAction += '&state=' + searchstate;
		}
		if(searchstate1.length > 0) {
			subAction += '&state1=' + searchstate1;
		}
		if(searchcity.length > 0) {
			subAction += '&city=' + searchcity;
		}

		if(subAction.length > 0) {
			subAction = subAction.substr(1);

			if(action.indexOf('?') >= 0) {
				action = action + subAction;
			} else {
				action = action + '?' + subAction;
			}
		}

		//$(target).closest('form').attr('action', action);
		//$(target).closest('form').find('input[type="submit"]').click();
		document.location.href = action;
		return false;
	},
	submitFindPanel: function(target)
	{
		var $form = $(target);
		var type = $form.find('select.bootstripe').val();
		switch(type){
			case '1':
				var searchtext = $form.find('#findpanel-searchall').val();
				var textarea = 'searchall';
				break;
			case '2':
				var searchtext = $form.find('#findpanel-searchpeople').val();
				var textarea = 'searchpeople';
				break;
			case '3':
				var searchtext = $form.find('#findpanel-searchcompany').val();
				var textarea = 'searchcompany';
				break;
			case '4':
				var searchtext = $form.find('#findpanel-searchgroup').val();
				var textarea = 'searchgroup';
				break;
			case '5':
				var searchtext = $form.find('#findpanel-searchschool').val();
				var textarea = 'searchschool';
				break;
			case '6':
				var searchtext = $form.find('#findpanel-searchjob').val();
				var textarea = 'searchjob';
				break;
		}
		$action = $form.attr('action');

		var after = $action.length - $action.indexOf('?') - 1 ;
		if($action.indexOf('?') <= 0) {
			$action += '?' + textarea + '=' + searchtext;
		} else if(after > 2){
			$action += '&' + textarea + '=' + searchtext;
		} else {
			$action += textarea + '=' + searchtext;
		}

		//$form.attr('action', $action);
		//$form.find('input:submit').click();
		document.location.href = $action;
		return false;

	},
	submitPublicFindPanel: function(target)
	{
		var $form = $(target);

		var searchtext = $form.find('#findshort-firstName').val() + ' ' + $form.find('#findshort-lastName').val();
		var textarea = 'searchpeople';

		$action = $form.attr('action');

		var after = $action.length - $action.indexOf('?') - 1 ;
		if($action.indexOf('?') <= 0) {
			$action += '?' + textarea + '=' + searchtext;
		} else if(after > 2){
			$action += '&' + textarea + '=' + searchtext;
		} else {
			$action += textarea + '=' + searchtext;
		}

		//$form.attr('action', $action);
		//$form.find('input:submit').click();
		document.location.href = $action;
		return false;

	},
	changeProfileStatistic: function(target)
	{
		var $target = $(target);

		$('.graph-month').parent().addClass('hidden');
		$('.graph-week').parent().addClass('hidden');
		$('.graph-day').parent().addClass('hidden');

		switch($target.val()){
			case '1':
				$('.graph-month').parent().removeClass('hidden');
				break;
			case '2':
				$('.graph-week').parent().removeClass('hidden');
				break;
			case '3':
				$('.graph-day').parent().removeClass('hidden');
				break;
		}
	},
	searchInProfile:function(target)
	{
		var $target = $(target);
		var $form = $target.closest('form');
		var data = $form.serialize();

		var $func = function() {
			web.searchInProfileAjax = false;
		};
		if(web.searchInProfileAjax !== false && typeof web.searchInProfileAjax !== 'undefined') {
			web.searchInProfileAjax.abort();
		}

		$ajax = system.ajaxGet($form.attr('action'), $func, data, true, 'GET');
		web.searchInProfileAjax = $ajax;
		return false;
	},
	// Old version. Search only in page
	//searchInProfile:function(target)
	//{
	//	var $target = $(target);
	//	var $finded = new Array();
	//	var $block = $('.block-all_connections-connections');
	//
	//	$block.find('> li.searchresult').remove();
	//
	//	$block.find('> li:not(.searchresult) a').each(function(){
	//		var $this = $(this);
	//		var text = $this.find('.userava-info > div').text().toLowerCase();
	//
	//		if($target.val().length === 0){
	//			$this.closest('li').removeClass('hidden');
	//		} else {
	//			$this.closest('li').addClass('hidden');
	//			$this.closest('li').removeClass('active');
	//			if(text.indexOf($target.val().toLowerCase()) >= 0) {
	//				$finded.push($this);
	//			}
	//		}
	//	});
	//
	//	if($target.val() === '') {
	//		$('.block-all_connections .text-bgtitle span').html($block.find('> li:not(.searchresult) a').size());
	//	} else {
	//		$('.block-all_connections .text-bgtitle span').html($finded.length);
	//	}
	//
	//
	//	if($target.val().length === 0){
	//		$block.find('> li:first-child').addClass('active');
	//	}
	//
	//	var i = 0, j = 1, $page = '';
	//	$.each($finded, function(key, value){
	//		i++;
	//		if(i === 1) {
	//			if($page !== '') {
	//				$page = $('<li class="searchresult"></li>').insertAfter($page);
	//			} else {
	//				$page = $('<li class="searchresult"></li>').prependTo($block);
	//			}
	//
	//			if(j === 1) {
	//				$page.addClass('active');
	//			}
	//		}
	//
	//		$page.append($(value).clone());
	//		if(i === 6) {
	//			i = 0;
	//			j++;
	//		}
	//	});
	//
	//	$('.gallery-navigation .icon-prev').removeClass('active');
	//	$('.gallery-navigation .icon-next').removeClass('active');
	//	if($finded.length > 6) {
	//		$('.gallery-navigation .icon-next').addClass('active');
	//	}
	//	if($finded.length === 0) {
	//		if($block.find('li').length > 1) {
	//			$('.gallery-navigation .icon-next').addClass('active');
	//		}
	//	}
	//
	//	return false;
	//},
	searchMemberInGroup:function(target)
	{
		var $target = $(target);
		var $block = $('.block-group_list_member');
		var $countFind = $('.text-bgtitle > span');

		$block.find('> li').addClass('hidden');

		var i = 0;
		$block.find('> li a').each(function(){
			var $this = $(this);
			var text = $this.find('.userava-info > div').text().toLowerCase();

			if($target.val().length !== 0){
				if(text.indexOf($target.val().toLowerCase()) >= 0) {
					i ++;
					$this.closest('li').removeClass('hidden');
				}
			} else {
				$block.find('> li.hidden').removeClass('hidden');
			}
		});

		if(i === 0) {
			$countFind.html($block.find('> li a').size());
		} else {
			$countFind.html(i);
		}

		return false;
	},
	searchMemberInSchool:function(target)
	{
		var $target = $(target);
		var $form = $target.closest('form');
		var data = $form.serialize();
		this.ajaxGet($form.attr('action'), false, data);
		return false;
	},
	localSearchKeyCode:function($code, target)
	{
		var $target = $(target);

		if($target.hasClass('isLocalSearch')) {
			var $localSearch = $target.next();
		} else {
			var $localSearch = $target.closest('.localsearch.isLocalSearch');
		}

		change = 0;
		switch($code){
			case 38:
				change = -1;
				$localSearch.addClass('active');
				break;
			case 40:
				change = 1;
				$localSearch.addClass('active');
				break;
			default:
				$localSearch.removeClass('active');
		}

		var $elements = $localSearch.find('a');
		var position = -1;
		$elements.each(function(key){
			var $this = $(this);
			if($this.hasClass('active')) {
				position = key;
			}
		});

		if(change === 1 && $elements.size() <= position + 1) {
			change = 0;
		}
		if(change === -1 && position < 1) {
			change = 0;
		}

		var $element = $($elements.get(position + change));
		$elements.removeClass('active');
		$element.focus().addClass('active');
	},
	localSearchKeyPress: function(target)
	{
		var $target = $(target);
		var $localSearch_List = $($target.data('localsearch-list') + ':not(.isLocalSearch)');

		$localSearch_List.find('li.hidden').removeClass('hidden');

		$localSearch_List.find('li .userava-name').each(function(){
			var $this = $(this);
			if($this.text().toLowerCase().indexOf($target.val().toLocaleLowerCase()) < 0) {
				$this.closest('li').addClass('hidden');
			}
		});

		this.showFormSelect(target, true);
	},
	localSearchFocusOut: function(target)
	{
		var $target = $(target);
		var $localSearch_List = $($target.data('localsearch-list') + ':not(.isLocalSearch)');

		var $hidden = $($target.data('localsearch-hidden'));
		if($hidden.size() > 0 && $hidden.val().length != 0) {
			var id = $hidden.val();
			var hiddenName = $localSearch_List.find('a[data-id="profile_' + id + '"] .userava-name').text();
			$target.val(hiddenName);
		} else {
			$target.val('');
		}

		if($target.next().hasClass('isLocalSearch') && !$target.next().hasClass('active')){
			setTimeout(function(){
				$target.next().remove();
			},150);
		}
	},
	localSearchFocus: function(target)
	{
		this.localSearchKeyPress(target);
	},
	showFormSelect: function(target, isUpdate)
	{
		var $target = $(target);
		var width = $target.innerWidth(true);
		var $localSearch_List = $($target.data('localsearch-list') + ':not(.isLocalSearch)').clone();

		$localSearch_List.find('li.hidden').remove();


			if(!$target.next().hasClass('isLocalSearch') || isUpdate === true) {

				if($localSearch_List.find('li').size() === 0) {
					$target.next().remove();
					return;
				}

				if(isUpdate && $target.next().hasClass('isLocalSearch')) {
					var $element = $target.next();
					$element.html($localSearch_List.html());
				} else {
					var $element = $localSearch_List.insertAfter($target);
					$element.addClass('isLocalSearch');
					$element.css('width', width + 'px');

					var totalHeight = 0;
					$element.children().each(function(){
						totalHeight += $(this).height();
					});

					if($element.height() < totalHeight) {
						$element.addClass('is-scroll');
					}


					var $docClick = function(event){
						if($(event.target).closest('.isLocalSearch').size() === 0 && $(event.target).attr('id') !== $target.attr('id')) {
							$element.remove();
							$(document).unbind('click', $docClick);
						}
					};
					$(document).bind('click', $docClick);
				}

				$element.find('a').unbind('click').click(function(){
					var $this = $(this);
					var userName = $this.find('.userava-name').text();
					$target.val(userName);
					$($target.data('localsearch-hidden')).val($this.data('id').substr(8));
					var $tmps = $target.closest('form').find('input, textarea, select');
					var isFinded = false;
					$tmps.each(function(){
						var $this = $(this);
						if(isFinded) {
							$this.focus();
							return false;
						}
						if($this.attr('id') === $target.attr('id')){
							isFinded = true;
						}
					});
					$element.remove();
					return false;
				}).keydown(function(e){
					if (e.keyCode == 38 || e.keyCode == 40) {
						web.localSearchKeyCode(e.keyCode, this);
						return false;
					}
				});

			}
	},
	changeMessagesFilter : function(target)
	{
		var $target = $(target);
		var $form = $target.closest('form');
		var action = $form.attr('action');
		if(action.indexOf('=') >= 0) {
			action += '&filter=' + $target.val();
		} else {
			action += 'filter=' + $target.val();
		}

		$form.attr('action', action);
		$form.find('input:submit').click();
	},
	changeCountMessages:function(count)
	{
		if(count > 0) {
			$('.messages-countreceived').html('(' + count + ')').addClass('active');
		} else {
			$('.messages-countreceived').html('(0)').removeClass('active');
		}

	},
	reinitCheckboxControl:function()
	{
		$('.checkbox-control').each(function(){
			var $this = $(this);
			if($this.hasClass('checkboxControl')){
//				$this.removeClass('checkboxControl');

				var id = $this.data('id');
				var $list = $($this.data('list'));
				var $buttons = $this.find('a');
				var label = $this.data('select_label');
				var type = $this.data('select_type');
				var $hiddenData = $($this.data('hidden_data'));

				$other_buttons = false;
				if(typeof $this.data('control') !== 'undefined'){
					$other_buttons = $($this.data('control'));
				}

				// Remove old Control panel
				$this.find('input#checkboxControl-control_' + id).remove();
				$this.find('label#[for="checkboxControl-control_' + id + '"]').remove();

				// Create new Control panel
				if(type !== 'one') {
					var text = '<input type="checkbox" id="checkboxControl-control_' + id + '" >' +
						'<label for="checkboxControl-control_' + id + '" class="form-checkbox">' + label + '</label>';
					var $control = $(text).appendTo($this);
					$control.change(function(){
						web.checkboxControlChangeControl(this, $list, $buttons, $other_buttons, $hiddenData);
					});
				}

				// Generate new check elements
				var i = 0;
				var $lastElement = false;
				$list.each(function(){
					i++
					var $this2 = $(this);
					var isChecked = '';
					if($this2.find('.checkboxElementControlPanel').is(':checked')) {
						isChecked = 'checked="checked"';
					}
					if($this2.data('ischecked') === 1) {
						isChecked = 'checked="checked"';
					}
					$this2.html('');

					var text = '<input type="checkbox" id="checkboxControl-element_' + id + '_' + i + '" ' + isChecked + '  class="checkboxElementControlPanel" >' +
						'<label for="checkboxControl-element_' + id + '_' + i + '" class="form-checkbox"></label>';
					var $element = $(text).appendTo($this2);
					$element.change(function(){
						web.checkboxControlChangeElement(this, $control, $list, $buttons, $other_buttons, type, $hiddenData);
					});

					if(isChecked.length > 0) {
						$element.change();
					}
					$lastElement = $element;
				});

				if($lastElement) {
					$lastElement.change();
				}
			}
		});
		this.initCheckboxControl();
	},
	initCheckboxControl:function()
	{
		$('.checkbox-control').each(function(){
			var $this = $(this);
			if(!$this.hasClass('checkboxControl')){
				$this.addClass('checkboxControl');

				var id = $this.data('id');
				var $list = $($this.data('list'));
				var $buttons = $this.find('a');
				var label = $this.data('select_label');
				var type = $this.data('select_type');
				var $hiddenData = $($this.data('hidden_data'));

				$other_buttons = false;
				if(typeof $this.data('control') !== 'undefined'){
					$other_buttons = $($this.data('control'));

					// set parameters for buttons
					$other_buttons.each(function(){
						var $this = $(this);
						var link = $this.attr('href');

						if(link.indexOf('?') >= 0) {
							link = link.substr(0, link.indexOf('?')) + '%data/' + link.substr(link.indexOf('?'));
						} else {
							link += '%data/';
						}

						$this.data('link', link);
					});
				}

				// set parameters for buttons
				$buttons.each(function(){
					var $this = $(this);
					var link = $this.attr('href');

					if(link.indexOf('?') >= 0) {
						link = link.substr(0, link.indexOf('?')) + '%data/' + link.substr(link.indexOf('?'));
					} else {
						link += '%data/';
					}

					$this.data('link', link);
				});


				// Create Control panel
				if(type !== 'one') {
					var text = '<input type="checkbox" id="checkboxControl-control_' + id + '" >' +
						'<label for="checkboxControl-control_' + id + '" class="form-checkbox">' + label + '</label>';
					var $control = $(text).appendTo($this);
					$control.change(function(){
						web.checkboxControlChangeControl(this, $list, $buttons, $other_buttons, $hiddenData);
					});
				}


				// Generate check elements
				var i = 0;
				$list.each(function(){
					i++;
					var isChecked = '';
					if($(this).data('ischecked') === 1) {
						isChecked = 'checked="checked"';
					}
					var text = '<input type="checkbox" id="checkboxControl-element_' + id + '_' + i + '" ' + isChecked +  ' class="checkboxElementControlPanel">' +
						'<label for="checkboxControl-element_' + id + '_' + i + '" class="form-checkbox"></label>';
					var $element = $(text).appendTo($(this));
					$element.change(function(){
						web.checkboxControlChangeElement(this, $control, $list, $buttons, $other_buttons, type, $hiddenData);
					});
				});
			}
		});
	},
	checkboxControlChangeElement: function(target, $control, $list, $buttons, $other_buttons, type, $hiddenData)
	{
		var $target = $(target);

		var countChecked = 0;
		$list.each(function(){
			var $this = $(this).find('input[type="checkbox"]');
			if($this.is(':checked')){
				countChecked ++;
			}
		});

		if(type === 'one'){
			$list.find('input:not(#' + $target.attr('id') + ')').removeAttr('checked');
		}

		if($target.is(':checked')) {
			if(type !== 'one') {
				$control.attr('checked', 'checked');
			}
			$buttons.removeClass('hidden');

			if($other_buttons){
				$other_buttons.removeClass('hidden');
			}
		} else {
			if(countChecked == 0) {
				if(type !== 'one') {
					$control.removeAttr('checked');
				}
				$buttons.addClass('hidden');

				if($other_buttons){
					$other_buttons.addClass('hidden');
				}
			}
		}

		this.checkboxControlUpdateButtonLinks($list, $buttons, $other_buttons, $hiddenData);
	},
	checkboxControlChangeControl: function(target, $list, $buttons, $other_buttons, $hiddenData)
	{
		var $target = $(target);

		if($target.is(':checked')) {
			$list.find('input[type=checkbox]').attr('checked', 'checked');
			$buttons.removeClass('hidden');

			if($other_buttons) {
				$other_buttons.removeClass('hidden');
			}
		} else {
			$list.find('input[type=checkbox]').removeAttr('checked', 'checked');
			$buttons.addClass('hidden');

			if($other_buttons) {
				$other_buttons.addClass('hidden');
			}
		}

		this.checkboxControlUpdateButtonLinks($list, $buttons, $other_buttons, $hiddenData);
	},
	checkboxControlUpdateButtonLinks: function($list, $buttons, $other_buttons, $hiddenData)
	{
		var text = '';
		$list.each(function(){
			var $this = $(this);
			var $element = $this.find('input[type="checkbox"]');

			if($element.is(':checked')){
				text += ',' + $this.data('id');
			}
		});

		if(text.length > 0) {
			text = text.substr(1);
		}

		$hiddenData.val(text);

		$buttons.each(function(){
			var $this = $(this);
			var link = $this.data('link');

			link = link.replaceAll('%data', text);
			$this.attr('href', link);
		});
		if($other_buttons) {
			$other_buttons.each(function(){
				var $this = $(this);
				var link = $this.data('link');

				link = link.replaceAll('%data', text);
				$this.attr('href', link);
			});
		}


	},
	setCountTo:function(element, count)
	{
		var $element = $(element);
		$element.html(count);
	},
	addCountTo:function(element)
	{
		var $element = $(element);
		$element.each(function(){
			var $this = $(this);
			var val = 0;

			if(typeof $this.data('count') !== 'undefined') {
				var val = parseFloat($this.data('count')) + 1;
				$this.data('count', val);
			} else {
				var val = parseFloat($this.text()) + 1;
			}

			if(typeof $this.data('max_count') !== 'undefined') {
				if(val > $this.data('max_count')) {
					val = '+' + $this.data('max_count');
				}
			}

			$element.html(val);
		});
	},
	negativeCountTo:function(element)
	{
		var $element = $(element);
		$element.each(function(){
			var $this = $(this);
			var val = 0;

			if(typeof $this.data('count') !== 'undefined') {
				var val = parseFloat($this.data('count')) - 1;
				$this.data('count', val);
			} else {
				var val = parseFloat($this.text()) - 1;
			}

			if(typeof $this.data('max_count') !== 'undefined') {
				if(val > $this.data('max_count')) {
					val = '+' + $this.data('max_count');
				}
			}

			if(typeof $this.data('visiblezero') === 'undefined') {
				if(val === 0) {
					$element.addClass('slow-hidden');
					$element.closest('.counter-body').addClass('slow-hidden');
				}
			}

			$element.html(val);
		});
	},
	showHideComments:function(target)
	{
		var $target = $(target);
		var $element = $target.closest('li').find('.isComments');

		if($element.size() > 0) {
			if($element.hasClass('hidden')) {
				$element.removeClass('hidden');
			} else {
				$element.addClass('hidden');
			}
		} else {
			web.ajaxGet($target);
		}

		return false;
	},
	autoScroll: function(element) {
		return system.autoScroll(element);
	},
	removeImageFromAddUpdate:function(target)
	{
		var $target = $(target);
		var $form = $target.closest('form');

//		$form.find('.i-attach').removeClass('hidden');
		$form.find('#addupdate-text').attr('required', 'required');
		this.removeFileFromUploaderList($target);

		return false;
	},
	removeFileFromUploaderList:function(target)
	{
		var $target = $(target);
		var $list = $target.closest('.uploader-list');
		web.ajaxGet($target, function($data){
			$list.find('#images_' + $data.id).closest('li').remove();
			web.reinitUserGallery('.upload-images');
			if($list.find('li:not(.hidden)').size() <= 0) {
				$list.closest('.upload-images').addClass('hidden');
			}
		});
		$('<div class="loader"></div>').insertBefore($target);
		$target.remove();
		return false;
	},
	updateAddUrl: function(target)
	{
		var $target = $(target);
		var text = ' ' + $target.val();
		var url = false;

		if(text.indexOf('https:') >= 0){
			url = text.substr(text.indexOf('https:'), (text.length - text.indexOf('https:')));
		} else if(text.indexOf('http:') >= 0){
			url = text.substr(text.indexOf('http:'), (text.length - text.indexOf('http:')));
		} else if(text.indexOf('www.') >= 0){
			url = text.substr(text.indexOf('www.'), (text.length - text.indexOf('www.')));
		}


		if(url && url.indexOf(' ') >= 0) {
			url = url.substr(0, url.indexOf(' '));
		}

		if(url && web.isLoadingUpdateUrl !== true) {
			web.isLoadingUpdateUrl = true;
			this.updateLoadUrlData(url, $target);
		}
	},
	updateLoadUrlData: function(url, $target)
	{
		var $form = $target.closest('form');
		var $this = this;
		if(url){
			var data = {
				'url': url
			}
			var $func = function($data){
				web.isLoadingUpdateUrl = false;
				web.updateSetUrlData($data, $target);
				$form.find('#addupdate-urltext').trigger('autosize.resize')
			}

			var $ajax = this.ajaxGet('/updates/loadUrlData/', $func, data, true);
			$form.find('.loader').removeClass('hidden');
			$form.find('.i-close').removeClass('hidden').click(function(){
				$ajax.abort();
				web.updateClear($form);
				return false;
			});
			$form.find('#addupdate-text').addClass('update-content_link').attr('disabled', 'disabled');
			$form.find('#addupdate-field-titletext').addClass('hidden');
			$form.find('#addupdate-titletext').removeAttr('required', 'required');
		} else {
			$form.find('.updateUrlData').addClass('hidden');
			$form.find('.i-close').addClass('hidden');
			$form.find('#addupdate-text').removeClass('update-content_link').removeAttr('disabled');
			$form.find('#addupdate-field-titletext').removeClass('hidden');
			if($form.find('#addupdate-titletext').hasClass('update-for-group')) {
				$form.find('#addupdate-titletext').attr('required', 'required');
			}
		}
	},
	updateSetUrlData: function($data, $target)
	{
		var $form = $target.closest('form');
		$form.find('.loader').addClass('hidden');
		$form.find('.updateUrlData').removeClass('hidden');
		$form.find('#addupdate-title').val($data.content.title);
		$form.find('#addupdate-urltext').val($data.content.description);
		$form.find('#addupdate-type').val($data.content.type);
		$form.find('.updates-file').addClass('hidden');
		if($data.content.images.length > 0) {
			$.each($data.content.images, function(key, value){
				$form.find('.uploader-list').append('<li>' + value.html + '</li>');
			});
		}
		$('.upload-images').removeClass('hidden');

//		web.reinitUserGallery('.upload-images');
	},
	updateIncludeImages: function(target)
	{
		var $target = $(target);
		var $form = $target.closest('form');
		if($target.is(':checked')) {
			$form.find('.i-attach').removeClass('hidden');
			$form.find('.upload-images').removeClass('hidden');
		} else {
			$form.find('.i-attach').addClass('hidden');
			$form.find('.upload-images').addClass('hidden');
		}
	},
	updateSubmit: function(target)
	{
		var $target = $(target);
		var id = $target.closest('form').find('.uploader-list li.active img').data('id');
		$('#addupdate-selected_image').val(id);
	},
	updateAddFileLink: function($url, name)
	{
		$('.updates-file .updates-file-link').attr('href', $url).html(name);
		$('.updates-file').removeClass('hidden');
	},
	updateClear:function($form)
	{
		$form.find('textarea, input').val('');
		$form.find('#addupdate-text').attr('required', 'required').css('height', '85px').removeAttr('disabled').removeClass('update-content_link').keyup();
		$form.find('#addupdate-type').val('1');
		$form.find('#addupdate-includeImage').val(true);
		$form.find('#addupdate-field-titletext').removeClass('hidden');
		if($form.find('#addupdate-titletext').hasClass('update-for-group')) {
			$form.find('#addupdate-titletext').attr('required', 'required');
		}
		$form.find('.loader').addClass('hidden');
		$form.find('.updateUrlData').addClass('hidden');
		$form.find('.upload-images li:not(.hidden)').remove();
		$form.find('.upload-images').addClass('hidden');
		$form.find('.updates-file').addClass('hidden');
		$form.find('.icon-next').removeClass('active');
		$form.find('.icon-prev').removeClass('active');
		$form.find('.i-close').addClass('hidden');
		web.ajaxGet('/updates/clearType/', true);
		web.isLoadingUpdateUrl = false;
		return false;
	},
	clickPost: function(target)
	{
		var $target = $(target);
		this.ajaxGet($target.data('url'));
	},
	switchGraph: function(target) {
		var $target = $(target);
		var $block = $('.' + $target.data('block'));

		$target.closest('div').find('a').removeClass('active');
		$target.addClass('active');

		$block.find('> div').removeClass('active');
		$block.find('.graph-block.' + $target.data('graph')).addClass('active');
		return false;

	},
	showHideNotifications: function(target)
	{
		var $target = $(target);
		var $element = $('.block-notifications');

		if($element.find('li').size() >= 2) {
			var $docClick = function(event){
				if($(event.target).closest('.block-notifications > div > div').size() === 0 && $(event.target).closest('.notificationBtn').size() === 0 && $(event.target).closest('.pbox-overlay').size() === 0) {
					$element.removeClass('active');
					$target.removeClass('active');
					$(document).unbind('click', $docClick);
				}
			};
			$(document).bind('click', $docClick);

			if ($element.hasClass('active')) {
				$element.removeClass('active');
				$target.removeClass('active');
			} else{
				$element.addClass('active');
				$target.addClass('active');
			}
			web.notificationHideNew();
		} else {
			$element.removeClass('active');
			$target.removeClass('active');
		}

		return false;
	},
	removeNotifications: function(target)
	{
		var $target = $(target);
		if($target.closest('ul').find('li').size() <= 3){
			if($target.closest('ul').find('#paginator a').size() > 0) {
				$target.closest('ul').find('#paginator a').click();
			} else {
				this.showHideNotifications($('.notificationBtn'));
			}
		}
		this.ajaxGet(target);
		return false;
	},
	inviteFormShow:function(target)
	{
		var $target = $(target);
		$("#findnewconnection-name").attr('placeholder', 'File name');
		$('#findnewconnection').removeClass('hidden').addClass('is-invite-file');
		$('.invite-next').addClass('hidden');
		return false;
	},
	inviteFormOnChangeFile:function(target)
	{
		var $target = $(target);
		var val = $target.val();
		val = val.toLowerCase();
		$("#findnewconnection-name").val(val);
		$(".invite-next").removeClass('hidden');
	},
	userpanelPopup: function(target)
	{
		var $target = $(target);
		var $element = $target.parent().find('.userpanel-sub-menu');
		if($element.hasClass('hidden')) {
			$element.removeClass('hidden');
		} else {
			$element.addClass('hidden');
		}

		var $docClick = function(event){
			if($(event.target).closest('.icon-down').size() === 0) {
				$element.addClass('hidden');
				$(document).unbind('click', $docClick);
			}
		}
		$(document).bind('click', $docClick);

		var $docClick = function(event){
			if($(event.target).closest('.block-notifications > div > div').size() === 0 && $(event.target).closest('.notificationBtn').size() === 0 && $(event.target).closest('.pbox-overlay').size() === 0) {
				$element.removeClass('active');
				$target.removeClass('active');
				$(document).unbind('click', $docClick);
			}
		};
		$(document).bind('click', $docClick);
	},
	showGroupMoreDiscription: function()
	{
		if($('.group_description_short').hasClass('hidden')) {
			$('.group_description_short').removeClass('hidden');
			$('.group_description').addClass('hidden');
		} else {
			$('.group_description_short').addClass('hidden');
			$('.group_description').removeClass('hidden');
		}
		return false;
	},
	notificationHideNew: function(){
		if($('.block-notifications').hasClass('active')) {
			var $elements = $('li.is-new');
			var ids = '';

			$elements.each(function(){
				var $this = $(this);
				ids += ',' + $this.data('id').substr(13);
			});
			if(ids.length > 0) {
				ids = ids.substr(1);
				web.ajaxGet('/notifications/setView/' + ids + '/');
			}

			var $i = 1;
			setTimeout(function(){
				$elements.removeClass('is-new');
				for($i = 1; $i <= $elements.size(); $i++) {
					web.negativeCountTo('.userpanel-control .notification-btn .userpanel-counter');
				}
			}, 2000);
		}
	},
	showLikes:function(target)
	{
		var $target = $(target);
		if($target.is('a') || $target.is('span')) {
			var $block = $target.closest('.update-textblock').find('.update-who_likes');
		} else {
			var $block = $target;
		}

		var $shareBlock = $target.closest('.update-textblock').find('.update-who_shares');
		var $followDiscussionBlock = $target.closest('.update-textblock').find('.update-who_follow_discussion');

		var tmp1 = $.trim($block.html());
		if(tmp1.search('<script') >= 0) {
			tmp1 = tmp1.substr(1, tmp1.search('<script'));
		}

		if(tmp1.length >  0 ) {
			$block.css('display', 'block');
			$block.addClass('active');
			$shareBlock.removeClass('active').css('display', 'none');
			$followDiscussionBlock.removeClass('active').css('display', 'none');
		}
	},
	showShares:function(target)
	{
		var $target = $(target);
		if($target.is('a') || $target.is('span')) {
			var $block = $target.closest('.update-textblock').find('.update-who_shares');
		} else {
			var $block = $target;
		}

		var $likeBlock = $target.closest('.update-textblock').find('.update-who_likes');
		var $followDiscussionBlock = $target.closest('.update-textblock').find('.update-who_follow_discussion');

		var tmp1 = $.trim($block.html());
		if(tmp1.search('<script') >= 0) {
			tmp1 = tmp1.substr(1, tmp1.search('<script'));
		}

		if(tmp1.length >  0 ) {
			$block.css('display', 'block');
			$block.addClass('active');
			$likeBlock.removeClass('active');
			$followDiscussionBlock.removeClass('active').css('display', 'none');
		}
	},
	showFollowDiscussion:function(target)
	{
		var $target = $(target);
		if($target.is('a') || $target.is('span')) {
			var $block = $target.closest('.update-textblock').find('.update-who_follow_discussion');
		} else {
			var $block = $target;
		}

		var $likeBlock = $target.closest('.update-textblock').find('.update-who_likes');
		var $sharesBlock = $target.closest('.update-textblock').find('.update-who_shares');

		var tmp1 = $.trim($block.html());
		if(tmp1.search('<script') >= 0) {
			tmp1 = tmp1.substr(1, tmp1.search('<script'));
		}
		if(tmp1.length >  0 ) {
			$block.css('display', 'block');
			$block.addClass('active');
			$likeBlock.removeClass('active');
			$sharesBlock.removeClass('active').css('display', 'none');
		}
	},
	changeCountry: function(target, isReduired)
	{
		if(typeof isReduired === 'undefined') {
			isReduired = true;
		}
		var $target = $(target);
		var $form = $target.closest('form');
		if(isReduired){
			if($target.val() === 'US') {
				$form.find('.stateSelect').attr('required', 'required')
					.closest('li').css('display', 'inline-block');

				$form.find('.stateText').removeAttr('required')
					.closest('li').css('display', 'none');
			} else {

				$form.find('.stateText').attr('required', 'required')
					.closest('li').css('display', 'inline-block');

				$form.find('.stateSelect').removeAttr('required')
					.closest('li').css('display', 'none');
			}
		} else {
			if($target.val() === 'US') {
				$form.find('.stateSelect').closest('li').css('display', 'inline-block');
				$form.find('.stateText').closest('li').css('display', 'none');
			} else {
				$form.find('.stateText').closest('li').css('display', 'inline-block');
				$form.find('.stateSelect').closest('li').css('display', 'none');
			}
		}

	},
	newjobChangeReceive: function(target)
	{
		var $target = $(target);
		if($target.val() == 2) {
			$('#newjob-field-email').css('display', 'inline-block').addClass('form-required')
				.find('input').attr('required', 'required');
		} else {
			$('#newjob-field-email').css('display', 'none').removeClass('form-required')
				.find('input').removeAttr('required');
		}
	},
	applyJobAddFile: function(file)
	{
		var $files = $('#applyjob-files');

		var files = $files.val();
		if(files !== '') {
			files = files.split(',');
		} else {
			files = [];
		}

		files.push(file);
		$files.val(files.join(','));
	},
	//changeTypeInSchool: function(target)
	//{
	//	var $target = $(target);
	//	var $form = $target.closest('form');
	//	$('#selecttypeinschool-fieldset-submit').removeClass('hidden');
	//
	//	return false;
	//},
	setTypeInSchool: function(target)
	{
		var $target = $(target);
		var $form = $target.closest('form');
		var data = $form.serialize();

		this.ajaxGet($form.attr('action'), false, data, 'POST');
		return false;
	},
	popupLoginOrRegister: function(target, isConnect)
	{
		var $target = $(target);
		var id = $target.data('id').substr(8);
		box.message('View profile', 'Please <a class="a-custom" href="#" onclick="box.close(); $(\'#login-email\').focus(); web.saveViewPublicAccout(' + id + ', ' + isConnect + '); return web.showHideLogin(this);">login</a> or <a  class="a-custom" href="/" onclick="web.saveViewPublicAccout(' + id + ', ' + isConnect + ');" >register</a> Mekooshar!');
		return false;
	},
	saveViewPublicAccout: function(id, isConnect)
	{
		//setCookie("viewProfile", id, false, "/");
		document.cookie = 'viewProfile=' + id + ' ; ; path=/';
		if(isConnect) {
			document.cookie = 'addConnectProfile=' + id + ' ; ; path=/';
		}
		return true;
	},
	sortYearInDatepicker: function() {

	},
	autoCompleteShowList: function(data)
	{

	},
	autoCompleteListSkills: function(target)
	{
		var $target = $(target);

		this.ajaxGet('/autocomplete/listSkills/', false);
	},
	showHideJobsFilter: function(target)
	{
		var $target = $(target);
		if($target.hasClass('active')) {
			$target.removeClass('active');
			$('.search_filters-filter_box').removeClass('active');
			document.cookie = 'isOpenedFilterJobInJobs=0 ; ; ';
		} else {
			$target.addClass('active');
			$('.search_filters-filter_box').addClass('active');
			document.cookie = 'isOpenedFilterJobInJobs=1 ; ; ';
		}
		return false;
	},
	showTermsOfUse: function(index_element)
	{
		var text = $(index_element).html();

		box.message('Terms of use', text);
		return false;
	},
	afterUpdatePage: function()
	{
			iniToolTips();
			web.iniAutoResizeWindow();
			web.initBootstrap();
			web.initInfuture();
			web.initFormElements();
			web.initAutosizeTewxtarea();
			web.initDatePicker();
			web.reinitCheckboxControl();
//			web.initCheckboxControl();
			web.sortYearInDatepicker();
			web.initSelectize();
	}
}