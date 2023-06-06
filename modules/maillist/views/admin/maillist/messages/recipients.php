<?=new View('admin/maillist/messages/header');?>
<div class="content-inner">
<br />
	<?=new View('admin/maillist/messages/nav',array('messageId' => $messageId,'active' => $active));?>

<form action="" class="autoform" id="recipients" method="post">
	<table class="w75 list">
		<thead>
			<tr>
				<td width="30"></td>
				<td class="w70">
					<a href="#" class="check-all">check all</a> / <a href="#" class="uncheck-all">uncheck all</a>
				</td>
				<td>
					<input class="awesome" type="submit" id="recipients-submit" name="recipients[submit]" value="Save and preview" />
				</td>
				
			</tr>
			<tr>
				<td></td>
				<td>
					Name filter: <input type="text" name="name" size="30" />
				</td>
				<td>
					
				</td>
			</tr>
		</thead>
		<tbody>
			<? foreach($subscribers as $k => $subscriber) : ?>
			<tr>
				<td>
					<input type="checkbox" id="recipients-recipient-<?=$subscriber->id ?>" name="recipients[recipient-<?=$subscriber->id ?>]" value="1" <?foreach($recipients as $users) : if($subscriber->id == $users->subscriberId) {echo 'checked'; } endforeach;?> />
				</td>
				<td>
					<label for="recipients-recipient-<?=$subscriber->id ?>"><?=$subscriber->firstName ?> <?=$subscriber->lastName ?></label>
				</td>
				<td></td>
			</tr>
			<? endforeach; ?>
		</tbody>
		
	</table>
</form>
</div>
<!--<div class="content-footer"></div>-->
<script type="text/javascript">
	$(function() {
		var $checks = $('.list tbody').find('input[type="checkbox"]');
		$checks.change(function () {
			var tr = $(this).parent().parent();
			if($(this).is(':checked')) {
				tr.addClass('marked');
			} else {
				tr.removeClass('marked');
			}
		});
		
		$('a.check-all').click(function() {
			checkBody();
			checkNav();
			return false;
		});
		
		$('a.uncheck-all').click(function() {
			uncheckBody();
			uncheckNav();
			return false;
		});

		
		$('#recipients-submit').click(function() {
			if($('.list tbody').find('input[type="checkbox"]:checked').length < 1) {
				alert('Select users to send message');
				return false;
			}
		});
		
		$('.list thead').find('input[type="checkbox"]').change(function() {
			check($(this));
		});
		
		$(".list thead").find('input[type="text"]').bind('keyup', function() {
			var f = $(this).val().toLowerCase();
			if(f.length > 0) {
				$('.list tbody').children('tr').each(function() {
					var obj = $(this).children().eq(1);
					var objName = obj.find('label').text().toLowerCase();
					if(objName.indexOf(f) == -1) {
						$(this).hide();
					} else {
						$(this).show();
					}
				});
			} else {
				$('.list tbody').children('tr').show();
			}
		});
	});
	
	function check($this)
	{
		var checkedAll = $('.list thead').find('input[type="checkbox"]:checked');
		
		if(checkedAll.length == 0) {
			$('a.uncheck-all').click();
		} else {
			var roles = $('.group-roles').find('input:checked');
			var types = $('.group-types').find('input:checked');
			var categories = $('.group-categories').find('input:checked');
			
			var tr = $('.list tbody tr');
			
			var active = 0;
			
			if(roles.length > 0) {
				uncheckBody();
				var filter = createFilter(roles);
				tr = tr.filter(filter);
				active++;
			}
			
			if(types.length > 0) {
				var filter = createFilter(types);
				
				if(!active) {
					uncheckBody();
				}
				tr = tr.filter(filter);
				active++;
			}
			
			if(categories.length > 0) {
				var filter = createFilter(categories);
				
				if(!active) {
					uncheckBody();
				}
				tr = tr.filter(filter);
				active++;
			}
			
			tr.each(function() {
				$(this).addClass('marked').find('input[type="checkbox"]').attr('checked', 'checked');
			});
		}
	}
	
	function createFilter($items) 
	{
		var str = '';
		$items.each(function() {
			var $this = $(this);
			str += 'tr['+$this.attr('cat')+'="'+$this.val()+'"], ';
		});
		
		return str.substr(0, str.length - 2);
	}
	
	function uncheckBody()
	{
		$('.list tbody').children('tr').each(function() {
			$(this).removeClass('marked').find('input[type="checkbox"]:checked').removeAttr('checked');
		});
	}
	
	function checkBody()
	{
		$('.list tbody').children('tr').each(function() {
			$(this).addClass('marked').find('input[type="checkbox"]').attr('checked', 'checked');
		});
	}
	
	function uncheckNav()
	{
		$('.group-roles').find('input:checked').removeAttr('checked');
		$('.group-types').find('input:checked').removeAttr('checked');
		$('.group-categories').find('input:checked').removeAttr('checked');
	}
	
	function checkNav()
	{
		$('.group-roles').find('input').attr('checked', 'checked');
		$('.group-types').find('input').attr('checked', 'checked');
		$('.group-categories').find('input').attr('checked', 'checked');
	}
</script>