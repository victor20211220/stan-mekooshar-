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
						Days
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
								$text = date('d F Y', strtotime($date));
							?>
							<a href="<?= Request::generateUri('admin', 'statistic', 'connections') . Request::getQuery('from', strtotime($date)) ?>"><?= $text ?></a>
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
