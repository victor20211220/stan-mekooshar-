<?// dump($user, 1); ?>
<?
$user->alias = trim($user->alias);
if(!empty($user->alias)) {
	$url_profile = Request::generateUri('profile', $user->alias);
} else {
	$url_profile = Request::generateUri('profile', $user->id);
}
?>
<tr class="<?= ($user->countReadedComplaints != $user->countComplaints) ? 'is-new' : null ?>" data-id="usercomplaint_<?= $user->id ?>">
	<td>

		<a href="<?= $url_profile ?>" target="_blank"><?= Html::chars($user->firstName . ' ' . $user->lastName) ?></a><br />
	</td>
	<td>
		<a href="mailto:<?=$user->email ?>"><?=Html::chars($user->email) ?></a>
	</td>
	<td>
		<a href="<?= Request::generateUri('admin', 'users', array('showUserComplaints', $user->id)) ?>">
			<?= $user->countComplaints ?> complaints <?
			if($user->countReadedComplaints != $user->countComplaints) : ?>
				(<?= $user->countComplaints - $user->countReadedComplaints ?> new)
			 <? endif; ?>
		</a>
	</td>
	<td>
		<?= date('m-d-Y', strtotime($user->createDate)) ?>
	</td>
	<td>
		<? if($user->isBlocked == 1) : ?>
			Blocked
		<? else: ?>
			Not blocked
		<? endif; ?>
	</td>
	<td>
		<? if($user->isBlocked == 1) : ?>
			<a href="<?= Request::generateUri('admin', 'users', array('unblockUser', $user->id)) ?>" onclick="return system.ajaxGet(this);">Unblock</a>
		<? else: ?>
			<a href="<?= Request::generateUri('admin', 'users', array('blockUser', $user->id)) ?>" onclick="return system.ajaxGet(this);">Block</a>
		<? endif; ?>

	</td>
</tr>
