<div id="dbg" class="<?=(isset($_COOKIE['dbg-closer']) and $_COOKIE['dbg-closer']) ? 'opened' : '' ?>">
	<div class="closer" eva-content="Click and you will see MAGIC!!!">&times;</div>
	<div class="dbg-inner">
		<table>
			<tr>
				<td>
					Execution time: [ <span>{execution_time}</span> ms ]
				</td>
				<td>
					Memory usage: [ <span>{memory_usage}</span> MB ]
				</td>
				<td>
					Included files count: [ <span>{included_files}</span> ]
				</td>
				<td>
					Database queries count: [ <span>{database_queries}</span> ]
				</td>
			</tr>
		</table>
		{database_queries_log}

		<?!empty ($_SESSION['history']) ? dump($_SESSION['history']) : ''?>
	</div>
</div>

<script type="text/javascript" >
$(function() {
	$('#dbg').find('.closer').click(function() {
		$('#dbg').toggleClass('opened');
		if(typeof($.cookie) != 'undefined') {
			if($('#dbg').hasClass('opened')) {
				$.cookie('dbg-closer', 1, {'path': '/'});
			} else {
				$.cookie('dbg-closer', 0, {'path': '/'});
			}
		}
	});
});
</script>