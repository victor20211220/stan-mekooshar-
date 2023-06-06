<?// dump($orders, 1); ?>
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
						Count paid
					</th>
					<th>
						Count accounts
					</th>
					<th>
						Sum
					</th>
				</tr>
			</thead>
			<tbody>
				<? foreach ($orders as $key => $item): $i++; ?>
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
							<a href="<?= Request::generateUri('admin', 'statistic', 'paidJobs') . Request::getQuery('from', date('U', time() - 60*60*24*7*$key)) ?>"><?= $text ?></a>
						</td>
						<td>
							<? if($item['paid'] > 0) : ?>
								<b><?= $item['paid'] ?></b>
							<? else: ?>
								0
							<? endif; ?>
						</td>
						<td>
							<? if($item['accounts'] > 0) : ?>
								<b><?= $item['accounts'] ?></b>
							<? else: ?>
								0
							<? endif; ?>
						</td>
						<td>
							<? if($item['sum'] > 0) : ?>
								<b>$<?= sprintf("%0.2f", $item['sum']) ?></b>
							<? else: ?>
								$0.00
							<? endif; ?>
						</td>
					</tr>
				<? endforeach; ?>
			</tbody>
		</table>
