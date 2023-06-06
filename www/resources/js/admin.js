$(function() {
	$(document).ready(function() {
		$.admin.__init();
	});
});
(function($){
	var admin = {
		__init : function() {
			
			this.messageBox = $('#ajax-message');
			
			if(this.messageBox.text()) {
				this.message(false, 7000);
			}
		}, 
		message : function(text, delay) {
			if(typeof(text) != 'undefined' && text !== false) {
				this.messageBox.text(text);
			}
			
			if(typeof(delay) != 'undefined') {
				this.messageBox.stop().fadeTo(300, 1).delay(delay).fadeTo(1000, 0);
			} else {
				this.messageBox.stop().fadeTo(300, 1);
			}
		},
		messageBox: []
	}
	$.extend({
		admin:admin
	});
})(jQuery)




$(function () {
//	if($('#ajax-message').text()) {
//		$('#ajax-message').show().delay(5000).fadeOut(1000);
//	}
//	
//	
//	$('.feedback-message').animate({
//		 backgroundColor  : 'white'
//		,borderTopColor   : '#eee'
//		,borderRightColor : '#eee'
//		,borderBottomColor: '#eee'
//		,borderLeftColor  : '#eee'
//		,color: '#999'
//	}, 3000);
//
//	$('.focused').focus();
//	$("#sortable").sortable({
//		axis:'y',
//		update : function()
//		{
//			serial = $("#sortable").sortable('serialize');
//			$.ajax({
//				type: "POST",
//				data: serial,
//				dataType: 'json',
//				success: function(data) {
//					$('#ajax-message').html(data.answer)
//						.css({'display':'inline'})
//						.oneTime("2s", function() {
//							$('#ajax-message').css({'display':'none'});
//						});
//				},
//				error: function() {
//					$('#ajax-message').html('Error');
//				}
//			});
//		}
//	});	
});

