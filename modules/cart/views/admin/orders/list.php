<div class="content-header">
	<div class="back_button">
		<a class="btn btn-left" title="Go back" href="/admin/">Back</a>
	</div>
	<? if(!isset($title) || !$title) {
		$title = (!empty($crumbs)) ? array_pop($crumbs) : false; $title = ($title) ? $title[0] : 'Dashboard';
	} ?>
	
	<h1><?=Text::short($title) ?></h1>
</div>
<div class="content-data">
    <div class="content-inner">
	<div class="content-data-inner">
		<? if (count($items)): ?>
		<table class="w100 TableWithPadding TableWithBorder" id="userList">
			<thead>
				<tr>
					<th>
						Date
					</th>
					<th>
						Order #
					</th>
					<th class="w15" >
						Amount, $
					</th>
					<th class="w20" >
						Paid status
					</th>
					<th class="w25" >
						Processed status
					</th>
					<th class="w10">
						Rem.
					</th>
				</tr>
			</thead>
			<tbody>
				<? foreach ($items['data'] as $item): ?>
					<tr>
						<td>
							<?=$item->dateTimeAdded ?>
						</td>
						<td>
							<b><a href="<?=Request::$controller . 'orderDetails/' . $item->token . '/' ?>">#<?=$item->token ?></a></b>
						</td>
						<td>
							$<?=$item->amount ?>
						</td>
						<td>
							<? if ($item->isPaid): ?>
								<img src="/resources/images/dashboard/tick.png" width="16" height="16" alt="Paid" class="middle"/> <strong class="middle">Paid</strong>
								<br />
								<?=Html::anchor(Request::$controller . 'unpaid/' . $item->id . '/', 'Mark as unpaid', array('class' => 'small'));?>
							<? else: ?>
								<?=Html::anchor(Request::$controller . 'paid/' . $item->id . '/', 'Mark as paid');?>
							<? endif ?>
						</td>
						<td>
							<? if ($item->processed): ?>
								<img src="/resources/images/dashboard/tick.png" width="16" height="16" alt="Processed" class="middle"/> <strong class="middle">Processed</strong>
								<br />
								<?=Html::anchor(Request::$controller . 'unprocessed/' . $item->id . '/', 'Mark as unprocessed', array('class' => 'small'));?>
							<? else: ?>
								<?=Html::anchor(Request::$controller . 'processed/' . $item->id . '/', 'Mark as processed');?>
							<? endif ?>
						</td>
						<td>
							<a eva-content="Remove this order" eva-confirm="" class="nav-btn main-btn-remove" href="<?=Request::$controller . 'remove/' . $item->id . '/' ?>"><span>Remove</span></a>
						</td>
					</tr>
				<? endforeach; ?>
			</tbody>
		</table>
		<? endif; ?>
	</div>
</div>
</div>
<div class="content-footer">
	<span class="status">
	<? if(count($items['data'])) : ?>
		Total: <span class="showed"><?=count($items['data']) ?></span> orders
	<? else : ?>
		No orders
	<? endif; ?>
	</span>
</div>