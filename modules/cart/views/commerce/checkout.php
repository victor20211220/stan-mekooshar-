<h2>Review your order</h2>
	
<? if(!empty($items)) : ?>
	<table>
		<thead>
			<tr>
				<th>Item</th>
				<th>Price</th>
				<th>Qty</th>
				<th>Total price</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<? $total = 0; ?>
		<? foreach($items as $item) : ?>
			<tr>
				<td><?=$item['name'] ?></td>
				<td><?=number_format($item['price'], 2) ?></td>
				<td><?=$item['quantity'] ?></td>
				<td><?=number_format($item['price']*$item['quantity'], 2) ?></td>
			</tr>
			<? $total += $item['price']*$item['quantity']; ?>
		<? endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="3">
					<a href="/">&larr; Back to Shop Online</a>
				</td>
				<td>
					<b>TOTAL: <span>$ <?=number_format($total, 2) ?></span></b>
				</td>
			</tr>
		</tfoot>
	</table>

	<? if($settings['paypalType'] == 'paypalPro' && ($settings['paypalUsername'] && $settings['paypalPassword'] && $settings['paypalSignature'])) : ?>
		<form name="payment" method="POST" >
			<p><?=$form->elements['delivery']->render() ?></p>
			<h2>Please, select payment method:</h2>
			<p>
				<input type="radio" value="creditcard" id="creditcard" name="payment[method]" checked="checked" />
				<label for="creditcard">
					<img src="/resources/images/icons/payment/visa.png" width="50" height="30" alt="" class="middle" />
					<img src="/resources/images/icons/payment/mastercard.png" width="50" height="30" alt="" class="middle" />
					<img src="/resources/images/icons/payment/americanexpress.png" width="50" height="30" alt="" class="middle" />
					<img src="/resources/images/icons/payment/discover.png" width="50" height="30" alt="" class="middle" />
				</label>
			</p>
			<p>
				<input type="radio" value="paypal" id="paypal" name="payment[method]" />
				<label for="paypal">
					<img src="/resources/images/icons/payment/paypal.png" width="50" height="30" alt="" class="middle" />
				</label>
			</p>
			<?=$form->elements['submit']->render() ?>
		</form>
	<? elseif($settings['paypalType'] == 'paypalStandart' && $settings['paypalEmail']) : ?>
		<form name="payment" method="POST" >
			<p><?=$form->elements['delivery']->render() ?></p>
			<p><?=$form->elements['submit']->render() ?></p>
		</form>
	<? endif; ?>
<? else : ?>
<p>Your shopping cart is empty</p>
<? endif; ?>
 