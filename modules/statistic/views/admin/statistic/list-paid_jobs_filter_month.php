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
						Month and Year
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
				<? foreach ($orders as $date => $item): $i++; ?>
					<tr>
						<td>
							<b><?= $i; ?></b>
						</td>
						<td>
							<?
								$text = date('F Y', strtotime($date.'01'));
							?>
							<a href="<?= Request::generateUri('admin', 'statistic', 'paidJobs') . Request::getQuery('from', strtotime($date . '01')) ?>"><?= $text ?></a>
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
