<?// dump($users, 1); ?>
<?
$i = 0;
?>

		<table class="w100 TableWithPadding TableWithBorder table-white" id="userList">
			<thead>
				<tr>
					<th>
						#
					</th>
					<th>
						Week
					</th>
					<th>
						Count registered
					</th>
				</tr>
			</thead>
			<tbody>
				<? foreach ($users as $key => $item): $i++; ?>
					<tr>
						<td>
							<b><?= $i; ?></b>
						</td>
						<td>
							<?
							if($key == '0') {
								$text = 'This Week';
							} else {
								$text = 'From: ' . date('m/d/Y', time() - 60*60*24*7*$key) . ' - To: ' . date('m/d/Y', time() - (60*60*24*7*($key + 1) - 60*60*24));
							}
							?>
							<a href="<?= Request::generateUri('admin', 'statistic', 'users') . Request::getQuery('from', date('U', time() - 60*60*24*7*$key)) ?>"><?= $text ?></a>
						</td>
						<td>
							<? if($item > 0) : ?>
								<b><?= $item ?></b>
							<? else: ?>
								0
							<? endif; ?>
						</td>
					</tr>
				<? endforeach; ?>
			</tbody>
		</table>
