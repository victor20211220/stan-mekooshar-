<?// dump($member, 1); ?>
<?// dump($school, 1); ?>

<li data-id="member_<?= $member->userId ?>">
<?=  View::factory('parts/userava-more', array(
	'ouser' => $member,
	'avasize' => 'avasize_52',
	'keyId' => 'userId',
	'isTooltip' => FALSE,
	'isCustomInfo' => FALSE,
	'isLinkProfile' => FALSE,
	'isUsernameLink' => TRUE,
	'isBtnStaffMember' => TRUE,
	'isShowName' => TRUE,
	'school_id' => $school->id
)); ?>
</li>
