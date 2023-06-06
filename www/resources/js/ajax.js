var ajax = {
	FirstLoad: true,
	LoadNewPageStatus: true,
	LoadingPage: false,
	CurrentPage: '',
	DelayBeforeAxaj: 1,
	DelayAfterAxaj: 1,
	CurrentPage: '',
		ArrayPagesDontSaveHistory: $([]),
	ArrayPagesAllowAjax: $([]),
	ArrayPages: $([]),
	ArrayContent: $([]),
	LoadPage: function(target, type, savehistory) {
		if ($(html).hasClass('ie7')) {
			return true;
		}
		if (type === 'GET' || typeof type === 'undefined' || type === '' || type === null) {
			type = 'GET';
		} else if (type === 'POST') {
			type = 'POST';
		} else {
			type = 'GET';
		}
		var isIE = false;


		if (ajax.LoadNewPageStatus === true && ajax.LoadingPage === false) {
			if (typeof $(target).attr('href') === "undefined") {
				if (typeof $(target).attr('action') === "undefined") {
					return false;
				}
				url = $(target).attr('action');
				var data = $(target).serialize();
			} else {
				url = $(target).attr('href');
				var data = null;
			}


			var findtodenide = jQuery.inArray(url, ajax.ArrayPagesAllowAjax);
			var flag_savehistory = true;

			if (url !== ajax.CurrentPage || findtodenide >= 0) {
				ajax.GetBeforeAjax();
				ajax.FirstLoad = false;
				ajax.LoadingPage = true;
				ajax.CurrentPage = url;
				isIE = $('html').hasClass('ie9') || $('html').hasClass('ie8') || $('html').hasClass('ie7');
				if (!isIE) {
					if (type === 'GET') {
						for (var i = 0; i < ajax.ArrayPagesDontSaveHistory.length; i++) {
							if (url.indexOf(ajax.ArrayPagesDontSaveHistory[i].toString()) >= 0) {
								flag_savehistory = false;
							}
						}
						if (flag_savehistory === true && (savehistory !== false || savehistory === true)) {
							history.pushState('', 'New URL: ' + url, url);

						}
					}
					ajax.GetBeforeAjax();
					setTimeout(function() {
						ajax.ProcessLoading(url, type, data);
					}, ajax.DelayBeforeAxaj);
				} else {
					window.location.hash = url;
				}
			}
		}
		return false;
	},
	ProcessLoading: function(url, type, data) {
		var findtodenide = jQuery.inArray(url, ajax.ArrayPagesAllowAjax);
		var find = -1;
		if (findtodenide < 0) {
			find = jQuery.inArray(url, ajax.ArrayPages);
		}
		if (find >= 0) {
			setTimeout(function() {
				ajax.ProcessLoading_BUFFER_SUCCESS(find);
			}, ajax.DelayAfterAxaj);
		} else {
			$.ajax({
				type: type,
				dataType: 'json',
				url: url,
				data: data,
				success: function(data) {
					setTimeout(function() {
						ajax.ProcessLoading_AJAX_SUCCESS(data);
					}, ajax.DelayAfterAxaj);
				},
				complete: function() {
					ajax.LoadingPage = false;
				},
				error: function(xhr, ajaxOptions, thrownError) {
					ajax.GetBeforAjaxError(xhr, ajaxOptions, thrownError);
				}
			});
		}
		return false;
	},
	ProcessLoading_AJAX_SUCCESS: function(data) {
		if (typeof data.redirect === 'undefined') {
			if (typeof data.html !== 'undefined') {
				ajax.ArrayPages.push(url);
				ajax.ArrayContent.push(data);
				$.each(data.html, function(key, value) {
					$(key).html(value);
				});
			}
			if (typeof data.JSCommand !== 'undefined') {
				ajax.StartJSCommand(data.JSCommand);
			}
			ajax.GetAfterAjax();
		} else {
			if (data.redirect.substr(0, 5) === 'https') {
				window.location.href = data.redirect;
				return true;
			}
			if (typeof data.JSCommand !== 'undefined') {
				ajax.StartJSCommand(data.JSCommand);
			}
			var a = $("<a href='" + data.redirect + "'></a>");
			ajax.LoadingPage = false;
			ajax.LoadPage(a, 'GET');
		}

	},
	ProcessLoading_BUFFER_SUCCESS: function(find) {
		var tmp = $(ajax.ArrayContent[find]);
		if (typeof tmp[0].html !== 'undefined') {
			$.each(tmp[0].html, function(key, value) {
				$(key).html(value);
			});
		}
		if (typeof tmp[0].JSCommand !== 'undefined') {
			ajax.StartJSCommand(tmp[0].JSCommand);
		}
		ajax.LoadingPage = false;
		ajax.GetAfterAjax();
	},
	SetBeforeAjax: function(delay, funct) {
		ajax.GetBeforeAjax = funct;
		ajax.DelayBeforeAxaj = delay;
	},
	GetBeforeAjax: function() {
	},
	SetAfterAjax: function(delay, funct) {
		ajax.GetAfterAjax = funct;
		ajax.DelayAfterAxaj = delay;
	},
	GetAfterAjax: function() {
	},
	SetAllowPagesAjax: function(page) {
		ajax.ArrayPagesAllowAjax.push(page);
	},
	SetPagesDontSaveHistory: function(page) {
		ajax.ArrayPagesDontSaveHistory.push(page);
	},
	SetBeforAjaxError: function(funct) {
		ajax.GetBeforAjaxError = funct;
	},
	GetBeforAjaxError: function(xhr, ajaxOptions, thrownError) {
	},
	StartJSCommand: function(data) {
		if (typeof data !== 'undefined' && data !== '') {
			$.each(data, function(key, value) {
				var ret = eval(value);
			});
		}
	}
}

$(window).load(function() {
	isIE = $('html').hasClass('ie9') || $('html').hasClass('ie8') || $('html').hasClass('ie7');

	if (isIE) {
		var str = window.location.href;

		if (str.indexOf('#') + 1) {
			str = 'http://' + str.split(/\/+/g)[1] + str.substr(str.indexOf('#') + 1, str.length - str.indexOf('#') - 1);
			window.location.href = str;
		}
		window.onhashchange = function() {
			ajax.LoadingPage = false;
			ajax.GetBeforeAjax();
			setTimeout(function() {
				var rand = new Date().getTime();
				if (window.location.hash.substr(1).indexOf('?') >= 0) {
					var url = window.location.hash.substr(1) + '&rand=' + rand;
				} else {
					var url = window.location.hash.substr(1) + '?rand=' + rand;
				}
				ajax.ProcessLoading(url, 'GET', '');
			}, ajax.DelayBeforeAxaj);
		};
	} else {
		window.onpopstate = function(event) {
			if (event.state !== null || ajax.FirstLoad === false) {
				ajax.LoadingPage = false;
				ajax.GetBeforeAjax();
				setTimeout(function() {
					ajax.ProcessLoading(window.location.href, 'GET', '');
				}, ajax.DelayBeforeAxaj);
			}
		};
	}
});