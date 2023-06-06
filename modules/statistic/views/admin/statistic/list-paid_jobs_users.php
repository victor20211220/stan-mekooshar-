<?// dump($orders, 1); ?>
<?// dump($filters, 1); ?>


		<table class="w100 TableWithPadding TableWithBorder table-white" id="userList">
			<thead>
				<tr>
					<th>
						Paid ID
					</th>
					<th>
						User Name
					</th>
					<th>
						Token
					</th>
					<th>
						Plan name
					</th>
					<th>
						Job name
					</th>
					<th>
						Date paid
					</th>
					<th>
						Sum
					</th>
				</tr>
			</thead>
			<tbody>
				<? foreach ($orders['data'] as $order): ?>
					<tr>
						<td>
							<b><?= $order->id ?></b>
						</td>
						<td>
							<a href="<?= Request::generateUri('profile', $order->userId)?>" target="_blank"><?= $order->userFirstName . ' ' . $order->userLastName ?></a>
						</td>
						<td>
							<a href="<?= Request::generateUri('admin', 'cart', array('orderDetails', $order->orderToken))?>" target="_blank"><?= $order->orderToken ?></a>
						</td>
						<td>
							<?= $order->planName ?>
						</td>
						<td>
							<?= $order->itemName ?>
						</td>
						<td>
							<?= date('m/d/Y H:m:i', strtotime($order->orderDatePaid)) ?>
						</td>
						<td>
							<? if($order->price > 0) : ?>
								<b>$<?= sprintf("%0.2f", $order->price) ?></b>
							<? else: ?>
								$0.00
							<? endif; ?>
						</td>
					</tr>
				<? endforeach; ?>
			</tbody>
		</table>
