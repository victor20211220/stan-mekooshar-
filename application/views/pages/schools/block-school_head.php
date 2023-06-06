<?// dump($active, 1); ?>
<?// dump($school, 1); ?>
<?

if(!isset($active)) {
	$active = 'home';
}

$isMy = FALSE;
if($school->user_id == $user->id) {
	$isMy = TRUE;
}

if(is_null($school->avaToken)) {
	$schoolAva = '/resources/images/noimage_100.jpg';
} else {
	$schoolAva = Model_Files::generateUrl($school->avaToken, 'jpg', FILE_SCHOOL_AVA, TRUE, false, 'userava_100');
}

?>

<div class="block-school_head">
	<div class="school_head-left">
		<img src="<?= $schoolAva ?>" title="<?= $school->name ?>" alt="ava <?= $school->name ?>" width="104"/>
	</div>
	<div class="school_head-right">
		<div class="title-big"><?= $school->name ?></div>
		<? if($isMy) : ?>
			<a href="<?= Request::generateUri('schools', $school->id) ?>" class="blue-btn <?= ($active == 'home') ? 'active' : null ?>"><span class="icons i-companyhome"><span></span></span>Home</a>
			<a href="<?= Request::generateUri('schools', 'studentsAlumni', $school->id) ?>" class="blue-btn <?= ($active == 'students') ? 'active' : null ?>"><span class="icons i-schools"><span></span></span>Students & alumni</a>
			<a href="<?= Request::generateUri('schools', 'edit', $school->id) ?>"  class="userinfo-editprofile btn-roundblue-border icons i-editcustom school_head-edit"><span></span>Edit school</a>
			<a href="<?= Request::generateUri('schools', 'followers', $school->id) ?>" class="school_head-followers"><?= (!is_null($school->countFollowers)) ? $school->countFollowers : '0' ?> follower<?= ($school->countFollowers > 1) ? 's' : null ?></a>
		<?	else:	?>
			<a href="<?= Request::generateUri('schools', 'follow', $school->id) ?>" class="blue-btn school_head-follow"><span class="icons i-companyfollowing"><span></span></span><?= (!is_null($school->followUserId)) ? 'Unfollow' : 'Follow' ?></a>
			<? if(!is_null($school->followUserId)) : ?>
				<a href="<?= Request::generateUri('schools', 'followers', $school->id) ?>" class="school_head-followers school_head-follow"><?= (!is_null($school->countFollowers)) ? $school->countFollowers : '0' ?> follower<?= ($school->countFollowers > 1) ? 's' : null ?></a>
			<? else : ?>
				<div class="school_head-followers school_head-follow"><?= (!is_null($school->countFollowers)) ? $school->countFollowers : '0' ?> follower<?= ($school->countFollowers > 1) ? 's' : null ?></div>
			<? endif ?>
		<? endif ?>
	</div>
</div>