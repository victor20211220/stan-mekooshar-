<?// dump($student, 1); ?>
<?// dump($school, 1); ?>

<li data-id="student_<?= $student->userId ?>">
<?=  View::factory('parts/userava-more', array(
	'ouser' => $student,
	'avasize' => 'avasize_52',
	'keyId' => 'userId',
	'isTooltip' => FALSE,
	'isCustomInfo' => FALSE,
	'isLinkProfile' => FALSE,
	'isUsernameLink' => TRUE,
	'isRemoveNotableAlumni' => TRUE,
	'isShowName' => TRUE,
	'school_id' => $school->id
)); ?>
</li>
