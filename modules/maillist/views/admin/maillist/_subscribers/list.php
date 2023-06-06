<h1>Subscriber list</h1><br />
<table class="w50" style="margin-bottom: 20px;">
	<tr>
		<td class="w50">
			<a href="<?=Request::$controller?>" class="awesome light-gray">&larr; back to messages</a>
		</td>
		<td>
			<a href="<?=Request::$controller?>add/" class="awesome green">+ add new subscriber</a>
		</td>
	</tr>
</table>
<? if (count($subscribers)): ?>
	<table class="w100 TableWithPadding TableWithBorder" id="subscriberList">
		<thead>
			<tr>
				<th class="w25">
					Name
				</th>
				<th class="w20" filter-type='ddl'>
					Type
				</th>
				<th class="w20" filter-type='ddl'>
					Category
				</th>
				<th class="w20" filter="false">
					Email
				</th>
				<th class="w10" filter-type='ddl'>
					Confirmed
				</th>
				<th class="w5" filter="false">

				</th>
			</tr>
		</thead>
		<tbody>
			<? foreach ($subscribers as $subscriber): ?>
				<tr <?=(($subscriber['confirmed']) ? '' : 'style="background-color: #eee; color: #999;"')?>>
					<td>
						<label for="subscriber-<?=$subscriber['id']?>">
							<?=Html::anchor(Request::$controller.'edit/' . $subscriber['id'] . '/', Html::chars($subscriber['name']))?>
						</label>
					</td>
					<td><?=$subscriber['type']?></td>
					<td><?=$subscriber['category']?></td>
					<td><a href="mailto:<?=$subscriber['email'] ?>" ><?=$subscriber['email'] ?></a></td>
					<td><?=($subscriber['confirmed'] ? 'confirmed' : 'unconfirmed')?></td>
					<td>
						<?=Html::anchor(Request::$controller.'remove/' . $subscriber['id'] . '/', '<img src="/resources/images/icons/remove-user.png" />', array('onclick' => 'javascript:return confirm("Are you sure?")'))?>
					</td>
				</tr>
			<? endforeach; ?>
		</tbody>
	</table>
	<script type="text/javascript">
		$(document).ready(function() {
			$('#subscriberList').tableFilter({ selectOptionLabel : 'Show all' });
		});
	</script>

	<? if(count($subscribers) > 20) : ?>
	<table class="w50" style="margin-bottom: 20px;">
		<tr>
			<td class="w50">
				<a href="<?=Request::$controller?>" class="awesome light-gray">&larr; back to messages</a>
			</td>
			<td>
				<a href="<?=Request::$controller?>add/" class="awesome green">+ add new subscriber</a>
			</td>
		</tr>
	</table>
	<? endif; ?>
<? endif; ?>