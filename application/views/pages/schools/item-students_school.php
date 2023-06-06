<?// dump($student, 1); ?>

<li data-id="student_<?= $student->userId ?>">
<?=  View::factory('parts/userava-more', array(
	'ouser' => $student,
	'avasize' => 'avasize_52',
	'keyId' => 'userId',
	'isTooltip' => FALSE,
	'isLinkProfile' => FALSE,
	'isUsernameLink' => TRUE,
	'isShowName' => TRUE,
	'isShowSchoolName' => TRUE,
	'schoolName' => $student->universityName,
	'isShowYearNearName' => TRUE,
	'year' => (!empty($student->yearTo) ? $student->yearTo : ''),
	'isBtnAddConnection' => TRUE,
	'isBtnSendMessage' => TRUE,
)); ?>
</li>
