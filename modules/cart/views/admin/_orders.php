<h1>Orders</h1>
<br /><br />
<?if (count($orders)):?>
	<table class="w100">
		<?foreach ($orders['data'] as $order):?>
			<tr>
				<td class="padding10 bold border" style="width: 380px;">
					Order #<?=Html::chars($order['token'])?>
					<? if ($order['delivery']): ?>
						<img src="/resources/images/icons/small/delivery.png" class="middle" width="16" height="16" alt="Overnight delivery" />
					<? endif ?>
				</td>
				<td class="border padding10" style="width: 90px;">
					$<?=$order['amount']?>
				</td>
				<td class="border padding10" style="width: 90px;">
					<?=Html::anchor(Request::$controller . 'orderDetails/' . $order['id'] . '/', 'Details');?>
				</td>
				<td class="border padding10" style="width: 120px;">
					<? if ($order['isPaid']): ?>
						<img src="/resources/images/icons/small/tick.png" width="16" height="16" alt="Paid" class="middle"/> <strong class="middle">Paid</strong>
						<br />
						<?=Html::anchor(Request::$controller . 'unpaid/' . $order['id'] . '/', 'Mark as unpaid', array('class' => 'dull'));?>
						<br />
					<? else: ?>
						<?=Html::anchor(Request::$controller . 'paid/' . $order['id'] . '/', 'Mark as paid');?>
					<? endif ?>
				</td>
				<td class="border padding10" style="width: 140px;">
					<? if ($order['processed']): ?>
						<img src="/resources/images/icons/small/tick.png" width="16" height="16" alt="Processed" class="middle"/> <strong class="middle">Processed</strong>
						<br />
						<?=Html::anchor(Request::$controller . 'unprocessed/' . $order['id'] . '/', 'Mark as unprocessed', array('class' => 'dull'));?>
						<br /><br />
					<? else: ?>
						<?=Html::anchor(Request::$controller . 'processed/' . $order['id'] . '/', 'Mark as processed');?>
					<? endif ?>
				</td>
				<td class="border padding10" style="width: 80px;">
					<?=Html::anchor(Request::$controller . 'remove/' . $order['id'] . '/', 'Delete', array('onclick' => 'javascript:return confirm(CONFIRMATION_REMOVE)'));?>
				</td>
			</tr>
		<?endforeach?>
	</table>
<?endif?>