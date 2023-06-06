<div class="content-header">
	<div class="back_button">
		<a class="btn btn-left" title="Go back" href="<?=Request::$controller . 'orders/' ?>">Back</a>
	</div>
	<? if(!isset($title) || !$title) {
		$title = (!empty($crumbs)) ? array_pop($crumbs) : false; $title = ($title) ? $title[0] : 'Dashboard';
	} ?>
	
	<h1><?=Text::short($title) ?></h1>
</div>
<div class="content-data">
    <div class="content-inner">
	<div class="content-data-inner">
		<h2>Order #<?=$order->token ?></h2>
		<table class="w100">
			<tr>
				<td class="w50 paddingRight10">
					Date: <?=$order->dateTimeAdded ?><br />
					Status: <?=($order->isPaid == 1 ? 'paid' : 'unpaid')?><br />
					<? if ($order->delivery): ?>
						Delivery: <strong>OVERNIGHT DELIVERY</strong><br />
						Delivery cost: $<?=$order->deliveryCost ?><br />
					<? endif ?>
					<br /><b>Total amount: $<?=$order->amount ?></b><br />
					
					
					
					
					
					<hr />
					<h4 style="margin-bottom: 5px;">Payment details:</h4>
					Payent method: 
						<? switch($order->paypalMethod) {
							case 'DoDirectPayment':
								echo 'Credit card';
								break;
							case 'DoExpressCheckoutPayment':
							case 'StandartPayment':
								echo 'Paypal';
								break;
							default:
								echo '-';
								break;
						} ?><br />
					PayPal response: <?=$order->paypalAck ?><br />
					<? if($order->paypalMessage) : ?>
						PayPal message: <?=$order->paypalMessage ?><br />
					<? endif; ?>
					PayPal Transaction ID: <?=$order->transactionId ?><br />
					PayPal timestamp: <?=$order->paypalTimestamp ?><br /><br />
					
					<h4 style="margin-bottom: 5px;">Customer information:</h4>
					Name: <?=Html::chars($order->customer) ?><br />
					State: <?=Html::chars($order->state) ?><br />
					Zip: <?=Html::chars($order->zip) ?><br />
					City: <?=Html::chars($order->city) ?><br />
					Address: <?=Html::chars($order->address) ?><br />
					Phone: <?=Html::chars($order->phone) ?><br />
					Comment: <?=nl2br(Html::chars($order->comment)) ?><br />
				</td>
				<td class="paddingLeft10">
					<?if (count($items)):?>
						<table class="w100 TableWithPadding TableWithBorder">
							<thead>
								<tr>
									<th class="border padding10">
										Item name
									</th>
									<th class="border padding10">
										Product
									</th>
<!--									<th class="border padding10">-->
<!--										Q-ty-->
<!--									</th>-->
									<th class="border padding10">
										Price, $
									</th>
								</tr>
							</thead>

							<?foreach ($items as $item):?>
								<tr>
									<td class="border padding10">
										<?=Html::chars($item->itemName) ?>
										<? if ($item->source == 'advertising'): ?>
											(banner)
										<? endif ?>
									</td>
									<td class="border padding10 center" >
										<? if(!is_null($item->job_id)) : ?>
											Job <br>
											<?= $item->planName ?>
										<? endif; ?>
									</td>
<!--									<td class="border padding10" >-->
<!--										--><?//=$item->quantity ?>
<!--									</td>-->
									<td class="border padding10" >
										$ <?=$item->price ?>
									</td>
								</tr>
							<?endforeach?>
						</table>
					<?endif?>
				</td>
			</tr>
		</table>
	</div>
</div>
</div>
<div class="content-footer">
	
</div>