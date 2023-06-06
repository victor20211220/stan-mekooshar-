<?// dump($connections, 1); ?>
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
						Month and Year
					</th>
					<th>
						Count connected
					</th>
				</tr>
			</thead>
			<tbody>
				<? foreach ($connections as $date => $item): $i++; ?>
					<tr>
						<td>
							<b><?= $i; ?></b>
						</td>
						<td>
							<?
								$text = date('F Y', strtotime($date.'01'));
							?>
							<a href="<?= Request::generateUri('admin', 'statistic', 'connections') . Request::getQuery('from', strtotime($date . '01')) ?>"><?= $text ?></a>
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
