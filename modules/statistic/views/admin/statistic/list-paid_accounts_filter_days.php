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
						Days
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
								$text = date('d F Y', strtotime($date));
							?>
							<a href="<?= Request::generateUri('admin', 'statistic', 'paidAccounts') . Request::getQuery('from', strtotime($date)) ?>"><?= $text ?></a>
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
