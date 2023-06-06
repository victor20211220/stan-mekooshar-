<?// dump($complaint, 1); ?>
<?
$complaint->userFromAlias = trim($complaint->userFromAlias);
if(!empty($complaint->userFromAlias)) {
	$url_profile = Request::generateUri('profile', $complaint->userFromAlias);
} else {
	$url_profile = Request::generateUri('profile', $complaint->userFromId);
}
?>
<tr class="<?= ($complaint->isViewed == 0) ? 'is-new' : null ?>" data-id="complaint_<?= $complaint->complaintId ?>">
	<td>
		<?= $complaint->complaintId ?>
	</td>
	<td>
		<a href="<?= $url_profile ?>" target="_blank"><?=Html::chars($complaint->userFromFirstName. ' ' . $complaint->userFromLastName )?></a><br />
	</td>
	<td>
		<a href="mailto:<?=$complaint->userFromEmail ?>"><?=Html::chars($complaint->userFromEmail) ?></a>
	</td>
	<td>
		<?= nl2br($complaint->description) ?>
	</td>
	<td>
		<?= date('m-d-Y', strtotime($complaint->createDate)) ?>
	</td>
	<td>
		<? if($complaint->isViewed == 1) : ?>
			is viewed
		<? else: ?>
			New
		<? endif; ?>
	</td>
	<td>
		<? if($complaint->isViewed == 1) : ?>
			<a href="<?= Request::generateUri('admin', 'users', array('setComplaintAsNew', $complaint->complaintId)) ?>" onclick="return system.ajaxGet(this);">Set as new</a>
		<? else: ?>
			<a href="<?= Request::generateUri('admin', 'users', array('setComplaintAsViewed', $complaint->complaintId)) ?>" onclick="return system.ajaxGet(this);">Set as viewed</a>
		<? endif; ?>

	</td>
</tr>
