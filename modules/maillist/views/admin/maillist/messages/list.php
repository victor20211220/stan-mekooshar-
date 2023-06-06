<div class="content-header">
	<div class="back_button">
		<a class="btn btn-left" title="Go back" href="/admin/">Back</a>
	</div>
	<?
	if (!isset($title) || !$title) {
		$title = (!empty($crumbs)) ? array_pop($crumbs) : false;
		$title = ($title) ? $title[0] : 'Dashboard';
	}
	?>

	<h1><?= Text::short($title) ?></h1>
</div>
<div class="content-data">
	<div class="content-inner">
		<div class="content-data-inner">
			<p>
				<a class="btn btn-plus" href="<?= Request::$controller ?>message/" class="large bold">+ add new message</a>
				<a class="user-list" href="/admin/users/" class="large bold">Users list</a>
			</p>
		</div>

		<? if (count($messages)): ?>
			<table class="w100 TableWithPadding TableWithBorder">
				<thead>
					<tr>
						<th class="w15">
							Add date
						</th>
						<th>
							Title
						</th>
						<th class="w15">
							Status
						</th>
						<th class="w10">
							Count
						</th>
						<th class="w15">
							Send date
						</th>
						<th class="w10">
							Delete
						</th>
					</tr>
				</thead>
				<tbody>
					<? foreach ($messages as $message): ?>
						<tr>
							<td>
								<?= $message->dateTime ?>
							</td>
							<td>
								<? if ($message->status == ''): ?>
									<?= Html::anchor(Request::$controller . 'message/' . $message->id . '/', $message->name ? Html::chars($message->name) : Html::chars($message->subject)) ?>
								<? else: ?>
									<?= $message->name ? $message->name : $message->subject ?>
								<? endif; ?>
							</td>
							<td>

								<?php echo Html::anchor(Request::$controller . 'preview/' . $message->id . '/', 'Send'); ?>

							</td>
							<td>

								<?
								foreach ($recipients as $k => $v) {
									if ($message->id == $k) {
										echo count($v) . ' - ';
									}
								}
								?>
								<?= $count ?>

							</td>
							<td>
								<?= ($message->date) ? $message->date : 'Not send yet' ?>
							</td>
							<td>
								<?= Html::anchor(Request::$controller . 'removeMessage/' . $message->id . '/', '<img src="/resources/images/icons/remove.png" />', array('onclick' => 'javascript:return confirm("Are you sure?")')) ?>
							</td>
						</tr>
					<? endforeach; ?>
				</tbody>
			</table>
		<? else: ?>
			<h2>No messages has been created yet.</h2>
		<? endif; ?>


	</div>
</div>
<div class="content-footer"></div>




