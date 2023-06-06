<?// dump($group, 1); ?>
<?
if(is_null($group->avaToken)) {
	$groupAva = '/resources/images/noimage_100.jpg';
} else {
	$groupAva = Model_Files::generateUrl($group->avaToken, 'jpg', FILE_GROUP_EMBLEM, TRUE, false, 'userava_100');
}

$isMy = FALSE;
if($group->user_id == $user->id) {
	$isMy = TRUE;
}

?>

<div class="userava">
	<div><a href="<?= Request::generateUri('groups', $group->id) ?>" class="userava-user_name_link"><b><?= $group->name ?></b></a></div>
	<div>
		<a href="<?= Request::generateUri('groups', $group->id) ?>" class="userava-user_name_link">
			<img src="<?= $groupAva ?>" title=""  width="106px"/>
			<? $countNewManage = Model_Group_Members::getCountAllNewMemberByGroupid($user->id, $group->id) ?>
			<? $countNewContent = Model_Posts::getCountAllNewGroupContent($user->id, $group->id) ?>
			<? $newGroup = $countNewManage + $countNewContent ?>
			<? if($newGroup > 0) : ?><div class="userpanel-counter" data-count="<?= $newGroup ?>"><?= ($newGroup > 9) ? '+9' : $newGroup ?></div><? endif; ?>
		</a></div>
	<div class="group-join_count_discussion">
		<span><?= $group->countDiscussions ?></span>
		Discussions
	</div>
</div>