<?// dump($active, 1); ?>
<?// dump($group, 1); ?>
<?// dump($counter, 1); ?>
<?// dump($countUnchecked, 1); ?>
<?
$isMy = FALSE;
if($group->user_id == $user->id) {
	$isMy = TRUE;
}
$isAdmin = FALSE;
if($group->memberType == GROUP_MEMBER_TYPE_ADMIN && $group->memberUserId == $user->id) {
	$isAdmin = TRUE;
}


if(is_null($group->avaToken)) {
	$groupAva = '/resources/images/noimage_100.jpg';
} else {
	$groupAva = Model_Files::generateUrl($group->avaToken, 'jpg', FILE_GROUP_EMBLEM, TRUE, false, 'userava_100');
}

if(!is_null($group->memberUserId)) {
	if($group->memberIsApproved == 1){
		$join_btn_text = 'Leave';
	} else {
		$join_btn_text = 'Cancel request';
	}
} else {
	$join_btn_text = 'Join';
}

$manageLink = Request::generateUri('groups', 'settings', $group->id);
if($countUnchecked > 0) {
	$manageLink = Request::generateUri('groups', 'checkContent', $group->id);
}
if($counter > 0) {
	$manageLink = Request::generateUri('groups', 'membersRequest', $group->id);
}


?>

<div class="block-group_head">
	<div class="group_head-left">
		<img src="<?= $groupAva ?>" title="<?= $group->name ?>" alt="ava <?= $group->name ?>" width="104px"/>
	</div>
	<div class="group_head-right">
		<div class="title-big"><?= $group->name ?></div>
		<? if($isMy || $isAdmin) : ?>
			<a href="<?= Request::generateUri('groups', $group->id) ?>" class="blue-btn <?= ($active == 'discussions') ? 'active' : null ?>"><span class="icons i-discussions"><span></span></span>Discussions</a>
			<? $countNewManage = Model_Group_Members::getCountAllNewMemberByGroupid($user->id, $group->id) ?>
			<? $countNewContent = Model_Posts::getCountAllNewGroupContent($user->id, $group->id) ?>
			<? $newGroup = $countNewManage + $countNewContent ?>
			<a href="<?= $manageLink ?>" class="blue-btn group_head-edit <?= ($active == 'manage') ? 'active' : null ?> <?= ($newGroup > 0) ? 'is-counter' : null ?>">
				<span class="icons i-manage"><span></span></span>Manage
				<? if($newGroup > 0) : ?><div class="userpanel-counter" data-count="<?= $newGroup ?>"><?= ($newGroup > 9) ? '+9' : $newGroup ?></div><? endif; ?>
			</a>
			<a href="<?= Request::generateUri('groups', 'members', $group->id) ?>" class="group_head-followers group_head-follow"><span><?= $group->members ?></span> Member<?= ($group->members > 1) ? 's' : null ?></a>
		<?	else:	?>
			<a href="<?= Request::generateUri('groups', 'join', $group->id) ?>" class="blue-btn group_head-follow"><span class="icons i-joingroup"><span></span></span><?= $join_btn_text ?></a>
			<a href="<?= Request::generateUri('groups', 'members', $group->id) ?>" class="group_head-followers group_head-follow"><span><?= $group->members ?></span> Member<?= ($group->members > 1) ? 's' : null ?></a>
		<? endif ?>
	</div>
</div>